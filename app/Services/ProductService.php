<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    /**
     * 根据条件查询并返回分页商品
     * @param  Request      $request
     * @param  integer|null $limit   每页显示条数
     * @return array
     */
    public function search(Request $request, $limit = null)
    {
        // 创建一个查询构造器
        $builder = Product::query()->where('on_sale', true);
        // 判断是否有提交 search 参数，如果有就赋值给 $search 变量
        // search 参数用来模糊搜索商品
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            // 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        // 是否有提交 order 参数，如果有就赋值给 $order 变量
        // order 参数用来控制商品的排序规则
        if ($order = $request->input('order', '')) {
            // 是否是以 _asc 或者 _desc 结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 根据传入的排序值来构造排序参数
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate($limit);

        return [
            'products' => $products,
            'filters'  => [
                'order' => $order,
                'search' => $search
            ]
        ];
    }

    /**
     * @param Product $product 是否收藏过的商品
     * @param object  $user    当前操作用户
     * @return bool   $favored 是否已经收藏过该商品，true：收藏过 false：没有
     */
    public function favored(Product $product, $user)
    {
        $favored = false;
        // 用户未登录时返回的是 null，已登录时返回的是对应的用户对象
        if($user) {
            // 从当前用户已收藏的商品中搜索 id 为当前商品 id 的商品
            // boolval() 函数用于把值转为布尔值
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        return $favored;
    }

    /**
     * 收藏商品
     * @param  Product $product 要收藏的商品
     * @param  object  $user    当前操作用户
     * @return array
     */
    public function favor(Product $product, $user)
    {
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    /**
     * 取消收藏商品
     * @param Product $product 取消收藏的商品
     * @param object  $user    当前操作用户
     */
    public function disfavor(Product $product, $user)
    {
        $user->favoriteProducts()->detach($product);
    }

    /**
     * 获取该商品有限的评论
     * @param Product $product 需要显示评论的商品
     * @param integer $limit   显示的评论限制条数
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function reviews(Product $product, $limit)
    {
        return OrderItem::query()
            ->with(['order.user', 'productSku'])
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
