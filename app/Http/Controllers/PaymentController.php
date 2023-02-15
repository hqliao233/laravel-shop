<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * 支付宝支付逻辑
     * @param Order $order
     * @param Request $request
     * @return mixed
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function payByAlipay(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        //用户再延迟提交支付订单的情况下需要考虑订单创建时就已经将付款时间限制了，这里做的是十五分钟限制，那么从一开始下单就已经开始计时
        //现在需要再点击支付宝支付的时候计算十五分钟还剩下多少时间，将该时间传给支付宝让支付宝和系统能够再同一时间关闭支付订单
        //避免造成订单支付错误的情况
        //还有一种做法就是在创建订单的时候就开始调用支付宝支付这样就可以不用计算时间差了

        //获取系统设置的延迟时间加上订单生成时间再减去现在的时间计算出还剩余的时间戳，格式化时间戳为分钟传给支付宝
        $deplay = $order->created_at->addSeconds(config('app.order_ttl'))->timestamp - Carbon::now()->timestamp;
        $time = (int) ($deplay / 60);
        Log::warning('记录延迟时间' . $deplay . '...' . $time);
        if ($time <= 0) {
            throw new InvalidRequestException('订单支付已超时,请重新购买');
        }


        //调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no'    => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount'    => $order->total_amount, // 订单金额，单位元，支持小数点后两位
            'subject'         => '支付廖氏杂货铺的订单' . $order->no, // 订单标题
            'timeout_express' => $time . 'm',
        ]);
    }

    /**
     * 支付宝前端回调
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function alipayReturn()
    {
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    /**
     * 支付宝支付服务器回调
     * @return string
     */
    public function alipayNotify()
    {
        $data = app('alipay')->verify();
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有交易状态：https://docs.open.alipay.com/59/103672
        if (! in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::query()->where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if (!$order) {
            return 'fail';
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
        ]);

        //订单支付完毕后的处理
        $this->afterPaid($order);

        return app('alipay')->success();
    }

    /**
     * 支付后善后罗技
     * @param Order $order
     */
    public function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }
}
