<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    private $productStoragePath = "productData.json";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = [];

        if(!Storage::exists($this->productStoragePath) ||  empty(Storage::get($this->productStoragePath)) ) {
            return response()->json($products);
        }

        $products = json_decode(Storage::get($this->productStoragePath), true);

        $products = collect( $products )->sortByDesc('datetime_submitted')->values()->all();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $products = json_decode(Storage::get($this->productStoragePath), true);

        if( is_null($products) ) {
            $productCount = 1;
        } else {
            $productCount = count($products) + 1;
        }

        $product = [
            'id' => $productCount,
            'product_name' => $request->product_name,
            'product_qty_in_stock' => (int) $request->product_qty_in_stock,
            'product_price_per_item' => (float) $request->product_price_per_item,
            'datetime_submitted' => now()->format('Y-m-d H:i:s'),
        ];

        $products[] = $product;

        Storage::put($this->productStoragePath, json_encode($products, JSON_PRETTY_PRINT));

        return response()->json([
            'message' => 'Product saved successfully',
            'data' => $products
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $products = json_decode(Storage::get($this->productStoragePath), true);

        foreach($products as &$product) {
            
            if( $product['id'] == $id ) {
                $product['product_name'] = $request->product_name ?? $product->product_name;
                $product['product_qty_in_stock'] = $request->product_qty_in_stock ?? $product->product_qty_in_stock;
                $product['product_price_per_item'] = $request->product_price_per_item ?? $product->product_price_per_item;
                $product['datetime_submitted'] = now()->format('Y-m-d H:i:s');

                break;
            }
        }

        Storage::put($this->productStoragePath, json_encode($products, JSON_PRETTY_PRINT));

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => []
        ], 200);
    }
}
