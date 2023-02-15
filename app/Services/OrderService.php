<?php

namespace App\Services;

use App\Events\OrderReviewed;
use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\CouponCode;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * @param object       $user   当前操作用户
     * @param null|integer $limit  每页条数，可不传
     * @return mixed       $orders 订单分页集合
     */
    public function paginateOrders($user, $limit = null)
    {
        $orders = Order::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return $orders;
    }

    /**
     * 开启事务保存订单保存商品sku信息更新订单并返回订单
     * @param  OrderRequest      $request
     * @param  object            $user     当前操作用户
     * @param  null|CouponCode   $coupon   优惠卷
     * @throws CouponCodeUnavailableException
     * @return mixed                  返回新建的order对象
     */
    public function store(OrderRequest $request, $user, CouponCode $coupon = null)
    {
        // 如果传入了优惠券，则先检查是否可用
        if ($coupon) {
            // 但此时我们还没有计算出订单总金额，因此先不校验
            $coupon->checkAvailable($user);
        }
        //开启事务
        $order = DB::transaction(function () use($user, $request, $coupon) {
            $address = UserAddress::query()->find($request->input('address_id'));
            //更新该地址最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            //创建订单
            $order   = new Order([
                'address'      => [
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $request->input('remark'),
                'total_amount' => 0,
            ]);
            //关联用户
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            $items       = $request->input('items');
            //遍历提交sku
            foreach ($items as $data) {
                $sku  = ProductSku::query()->find($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            if ($coupon) {
                // 总金额已经计算出来了，检查是否符合优惠券规则
                $coupon->checkAvailable($user, $totalAmount);
                // 把订单金额修改为优惠后的金额
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                // 将订单与优惠券关联
                $order->couponCode()->associate($coupon);
                // 增加优惠券的用量，需判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throw new CouponCodeUnavailableException('该优惠券已被兑完');
                }
            }

            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id');
            app(CartService::class)->deleteItems($user, $skuIds);

            return $order;
        });

        //延时任务，关闭未支付订单
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }

    /**
     * 更新订单信息
     * @param Order $order 需要更新的订单
     * @param array $data  更新的数据
     */
    public function update(Order $order, $data)
    {
        //TODO 不限制参数个数
        $order->update($data);
    }

    /**
     * 保存用户上传的评价信息并更新商品评分
     * @param Order $order   需要更新的订单
     * @param array $reviews 用户上传的评价信息
     */
    public function reviewing(Order $order, $reviews)
    {
        //开启事务
        DB::transaction(function () use ($reviews, $order) {
            //遍历用户提交的数据并修改订单状态信息
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                $orderItem->update([
                    'rating'      => $review['rating'],
                    'review'      => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            $this->update($order, ['reviewed' => true]);
            //手动触发更新评分事件
            event(new OrderReviewed($order));
        });
    }
}
