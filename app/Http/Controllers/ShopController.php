<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $o_column = "";
        $o_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 500000;

        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order = 'DESC';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order = 'ASC';
                break;
            case 3:
                $o_column = 'sale_price';
                $o_order = 'ASC';
                break;
            case 4:
                $o_column = 'sale_price';
                $o_order = 'DESC';
                break;
            default:
                $o_column = 'id';
                $o_order = 'DESC';
        }
        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $products = Product::where(function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'" . $f_brands . "'=''");
        })
            ->where(function ($query) use ($f_categories) {
                $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'" . $f_categories . "'=''");
            })
            ->where(function ($query) use ($min_price, $max_price) {
                $query->whereBetween('regular_price', [$min_price, $max_price])
                    ->orwhereBetween('sale_price', [$min_price, $max_price]);
            })
            ->orderBy($o_column, $o_order)->paginate($size);

        // Ambil 5 produk secara acak
        $randomProducts = Product::inRandomOrder()->take(5)->get();

        // dd($randomProducts);

        return view('shop', compact('products', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price', 'randomProducts'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $rproducts = Product::where('slug', '<>', $product_slug)->take(8)->get();

        // dd($product->images);
        return view('details', compact('product', 'rproducts'));
    }
}
