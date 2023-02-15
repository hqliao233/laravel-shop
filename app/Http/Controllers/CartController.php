<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\ProductSku;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    /**
     * 注册CartService
     * CartController constructor.
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * 购物车列表页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $cartItems = $this->cartService->getItems($user);
        $addresses = $this->cartService->getAddresses($user);

        return view('cart.index', compact('cartItems', 'addresses'));
    }

    /**
     * 添加购物车
     * @param AddCartRequest $request
     * @return array
     */
    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->user(), $request->input('sku_id'), $request->input('amount'));

        return [];
    }

    /**
     * 删除购物车
     * @param ProductSku $sku
     * @param Request $request
     * @return array
     */
    public function remove(ProductSku $sku, Request $request)
    {
        $this->cartService->deleteItems($request->user(), $sku->id);

        return [];
    }
}
