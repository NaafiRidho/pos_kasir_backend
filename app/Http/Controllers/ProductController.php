<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use App\Utils\Response;
use Throwable;

class ProductController extends Controller
{
    /**
     * Add a new product.
     */
    public function add_product(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'categories_id' => 'nullable|exists:categories,categories_id',
                'name' => 'required|string|max:191',
                'description' => 'nullable|string',
                'cost_price' => 'nullable|numeric',
                'selling_price' => 'nullable|numeric',
                'product_images' => 'nullable|array',
                'stock' => 'nullable|integer',
                'barcode' => 'nullable|string|max:191',
            ]);

            if (isset($data['product_images']) && is_array($data['product_images'])) {
                $data['product_images'] = json_encode($data['product_images']);
            }

            $product = Product::create($data);

            // decode images for response
            if (! empty($product->product_images)) {
                $product->product_images = json_decode($product->product_images);
            }

            return Response::success($product, 'Product created', 201);
        } catch (Throwable $e) {
            return Response::error($e);
        }
    }

    /**
     * Edit an existing product.
     */
    public function edit_product(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (! $product) {
                return Response::notFound('Product not found');
            }

            $data = $request->validate([
                'categories_id' => 'nullable|exists:categories,categories_id',
                'name' => 'sometimes|required|string|max:191',
                'description' => 'nullable|string',
                'cost_price' => 'nullable|numeric',
                'selling_price' => 'nullable|numeric',
                'product_images' => 'nullable|array',
                'stock' => 'nullable|integer',
                'barcode' => 'nullable|string|max:191',
            ]);

            if (array_key_exists('product_images', $data) && is_array($data['product_images'])) {
                $data['product_images'] = json_encode($data['product_images']);
            }

            $product->update($data);

            $product = $product->fresh();
            if (! empty($product->product_images)) {
                $product->product_images = json_decode($product->product_images);
            }

            return Response::success($product, 'Product updated');
        } catch (Throwable $e) {
            return Response::error($e);
        }
    }

    /**
     * Delete a product.
     */
    public function delete_product($id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (! $product) {
                return Response::notFound('Product not found');
            }

            $product->delete();

            return Response::success(null, 'Product deleted');
        } catch (Throwable $e) {
            return Response::error($e);
        }
    }

    /**
     * List products with optional filters.
     */
    public function list_product(Request $request): JsonResponse
    {
        try {
            $query = Product::with('category');

            if ($request->filled('category_id')) {
                $query->where('categories_id', $request->input('category_id'));
            }

            $products = $query->get();

            // decode images for each product
            $products->transform(function ($p) {
                if (! empty($p->product_images)) {
                    $p->product_images = json_decode($p->product_images);
                }
                return $p;
            });

            return Response::success($products);
        } catch (Throwable $e) {
            return Response::error($e);
        }
    }
}
