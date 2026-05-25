<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
        ]);

        if ($request->category_id) {
            $cat = ProductCategory::find($request->category_id);
            abort_if($cat->user_id !== Auth::id(), 403);
        }

        Product::create([
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'category_id' => $request->category_id ?: null,
        ]);

        return back()->with('success', 'Product "' . $request->name . '" added.');
    }

    public function destroy(Product $product)
    {
        abort_if($product->user_id !== Auth::id(), 403);

        $product->delete();

        return back()->with('success', 'Product deleted.');
    }
}
