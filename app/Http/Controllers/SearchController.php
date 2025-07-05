<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q', '');
        $products = [];

        if (strlen($q) >= 2) {
            // Cari di nama atau deskripsi, sesuaikan kolom
            $products = Product::where('name', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->paginate(12)
                ->appends(['q' => $q]);
        }

        // dd($products);

        return view('search-results', compact('products', 'q'));
    }
}
