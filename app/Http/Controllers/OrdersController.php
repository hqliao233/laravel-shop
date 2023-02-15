<?php

namespace App\Http\Controllers;

use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Models\CouponCode;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    protected $orderService;

    /**
     * 注册OrderService
     * OrdersController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * 订单列表首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $orders = $this->orderService->paginateOrders($request->user());

        return view('orders.index', compact('orders'));
    }

    /**
     * 显示订单详情
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException exception 权限限制
     */
    public function show(Order $order)
    {
        $this->authorize('own', $order);

        return view('orders.show', compact('order'));
    }

    /**
     * 新建订单
     * @param OrderRequest $request 订单验证规则
     * @throws CouponCodeUnavailableException
     * @return mixed
     */
    public function store(OrderRequest $request)
    {
        $coupon = null;
        if ($code = $request->input('coupon_code')) {
            $coupon = CouponCode::query()->where('code', $code)->first();
            if (! $coupon) {
                throw new CouponCodeUnavailableException('优惠卷不存在');
            }
        }

        //新建订单
        $order = $this->orderService->store($request, $request->user(), $coupon);

        return $order;
    }

    /**
     * 确认收货
     * @param Order $order
     * @return Order
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function received(Order $order)
    {
        $this->authorize('own', $order);

        //判断订单发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('订单发货状态错误');
        }

        $this->orderService->update($order, ['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        return $order;
    }

    /**
     * 跳转到订单评价页面
     * @param Order $order 要评价的订单信息
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function review(Order $order)
    {
        //权限验证
        $this->authorize('own', $order);
        //判断该订单是否已支付
        if (! $order->paid_at) {
            throw new InvalidRequestException('订单尚未支付，无法评价');
        }

        return view('orders.review', compact('order'));
    }

    /**
     * 保存用户上传的评价信息
     * @param Order             $order   评价的订单
     * @param SendReviewRequest $request 用户验证用户提交信息
     * @return \Illuminate\Http\RedirectResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        //权限验证
        $this->authorize('own', $order);
        //判断该订单是否已支付
        if (! $order->paid_at) {
            throw new InvalidRequestException('该订单尚未支付，无法评价');
        }
        //判断该订单是否已评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        //保存提交评价信息
        $reviews = $request->input('reviews');
        $this->orderService->reviewing($order, $reviews);

        return redirect()->back();
    }

    /**
     * 申请退款
     * @param Order $order
     * @param ApplyRefundRequest $request
     * @return Order
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
        $this->authorize('own', $order);
        //判断是否已付款
        if (! $order->paid_at) {
            throw new InvalidRequestException('该订单尚未支付，无法退款！');
        }
        //判断订单状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已申请过退款，请勿重复操作');
        }

        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra                  = $order->extra ?: [];
        $extra['refund_reason'] = $request->input('reason');
        // 将订单退款状态改为已申请退款
        $this->orderService->update($order, [
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra'         => $extra,
        ]);

        return $order;
    }
}
