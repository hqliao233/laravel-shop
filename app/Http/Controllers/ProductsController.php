<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        return view('products.index', $this->productService->search($request, 16));
    }

    public function show(Product $product, Request $request)
    {
        //判断商品是否上架，没有则抛出异常
        if (! $product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $reviews = $this->productService->reviews($product, 10);

        $favored = $this->productService->favored($product, $request->user());

        return view('products.show', compact('product', 'favored', 'reviews'));
    }

    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', compact('products'));
    }

    public function favor(Product $product, Request $request)
    {
        return $this->productService->favor($product, $request->user());
    }

    public function disfavor(Product $product, Request $request)
    {
        $this->productService->disfavor($product, $request->user());

        return [];
    }
}
