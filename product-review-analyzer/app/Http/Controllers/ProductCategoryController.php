<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);

        ProductCategory::create([
            'user_id' => Auth::id(),
            'name'    => $request->name,
        ]);

        return back()->with('success', 'Category "' . $request->name . '" created.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        abort_if($productCategory->user_id !== Auth::id(), 403);

        $productCategory->delete();

        return back()->with('success', 'Category deleted.');
    }
}
