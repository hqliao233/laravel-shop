<?php

namespace App\Services;

use App\Models\CartItem;

class CartService
{
    /**
     * @param object  $user   当前操作用户
     * @param integer $skuId  商品sku id
     * @param integer $amount 添加商品数量
     */
    public function createItem($user, $skuId, $amount)
    {
        $cart = new CartItem(['amount' => $amount]);
        $cart->productSku()->associate($skuId);
        $cart->user()->associate($user);
        $cart->save();
    }

    /**
     * @param object        $user    当前操作用户
     * @param array|integer $skuIds  要删除的商品sku id,可以是一个sku id数组
     */
    public function deleteItems($user, $skuIds)
    {
        // 可以传单个 ID，也可以传 ID 数组
        if (! is_array($skuIds)) {
            $skuIds = [$skuIds];
        }
        $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
    }

    /**
     * @param  object $user 当前操作用户
     * @return mixed        返回购物车中所有添加的商品
     */
    public function getItems($user)
    {
        return $user->cartItems()->with(['productSku.product'])->get();
    }

    /**
     * @param  object $user 当前操作用户
     * @return mixed        以最后使用时间排序返回当前用户的所有收货地址
     */
    public function getAddresses($user)
    {
        return $user->addresses()->orderBy('last_used_at', 'desc')->get();
    }

    /**
     * @param  object  $user  当前操作用户
     * @param  integer $skuId 商品sku id
     * @return mixed          判断当前sku id是否存在购物车中，存在则返回该item
     */
    public function existInItems($user, $skuId)
    {
        return $user->cartItems()->where('product_sku_id', $skuId)->first();
    }

    /**
     * 将商品添加到购物车中
     * @param object  $user   当前操作用户
     * @param integer $skuId  要添加的商品sku id
     * @param integer $amount 要添加的数量
     */
    public function add($user, $skuId, $amount)
    {
        //判断该商品sku是否存在于购物车中，存在则更新购买数量不存在则新规item
        if ($cart = $this->existInItems($user, $skuId)) {
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        } else {
            $this->createItem($user, $skuId, $amount);
        }
    }
}
