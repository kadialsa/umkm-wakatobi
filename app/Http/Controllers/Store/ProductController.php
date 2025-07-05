<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the owner's products, with optional search.
     */
    public function index(Request $request)
    {
        $storeId = Auth::user()->store->id;

        // Mulai query hanya untuk produk milik store ini
        $query = Product::where('store_id', $storeId);

        // Jika ada input pencarian 'name', filter by nama produk (LIKE)
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Jalankan paginate dan pertahankan query string (untuk pagination + search)
        $products = $query
            ->orderBy('created_at', 'DESC')
            ->paginate(10)
            ->withQueryString();

        return view('store.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands     = Brand::orderBy('name')->get(['id', 'name']);

        return view('store.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created product for this store.
     */
    public function store(Request $request)
    {


        $storeId = Auth::user()->store()->first()->id;

        $data = $request->validate([
            'name'              => 'required|string',
            'slug'              => [
                'required',
                Rule::unique('products')->where(fn($q) => $q->where('store_id', $storeId)),
            ],
            'short_description' => 'required',
            'description'       => 'required',
            'regular_price'     => 'required|numeric',
            'sale_price'        => 'nullable|numeric',
            'SKU'               => 'required',
            'stock_status'      => 'required|in:instock,outofstock',
            'featured'          => 'required|boolean',
            'quantity'          => 'required|integer',
            'image'             => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'images.*'          => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'category_id'       => 'required|exists:categories,id',
            // 'brand_id'          => 'required|exists:brands,id',
        ]);

        $data['store_id'] = $storeId;
        $data['slug']     = Str::slug($data['name']);

        // handle main image
        $timestamp = Carbon::now()->timestamp;
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $imgName   = $timestamp . '.' . $file->extension();
            $this->generateProductThumbnailImage($file, $imgName);
            $data['image'] = $imgName;
        }

        // handle gallery
        $gallery = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $name = $timestamp . '-' . ($i + 1) . '.' . $file->extension();
                $this->generateProductThumbnailImage($file, $name);
                $gallery[] = $name;
            }
        }
        $data['images'] = implode(',', $gallery);

        Product::create($data);

        return redirect()->route('store.products.index')
            ->with('status', 'Product berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $storeId = Auth::user()->store()->first()->id;

        $product = Product::where('store_id', $storeId)
            ->findOrFail($id);

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands     = Brand::orderBy('name')->get(['id', 'name']);

        return view('store.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $storeId = Auth::user()->store()->first()->id;

        $product = Product::where('store_id', $storeId)
            ->findOrFail($id);

        $data = $request->validate([
            'name'              => 'required|string',
            'slug'              => [
                'required',
                Rule::unique('products')
                    ->ignore($product->id)
                    ->where(fn($q) => $q->where('store_id', $storeId)),
            ],
            'short_description' => 'required',
            'description'       => 'required',
            'regular_price'     => 'required|numeric',
            'sale_price'        => 'nullable|numeric',
            'SKU'               => 'required',
            'stock_status'      => 'required|in:instock,outofstock',
            'featured'          => 'required|boolean',
            'quantity'          => 'required|integer',
            'image'             => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'images.*'          => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'category_id'       => 'required|exists:categories,id',
            // 'brand_id'          => 'required|exists:brands,id',
        ]);

        $data['slug'] = Str::slug($data['name']);

        // replace main image if uploaded
        $timestamp = Carbon::now()->timestamp;
        if ($request->hasFile('image')) {
            // delete old
            File::delete([
                public_path("uploads/products/{$product->image}"),
                public_path("uploads/products/thumbnails/{$product->image}"),
            ]);
            $file    = $request->file('image');
            $imgName = $timestamp . '.' . $file->extension();
            $this->generateProductThumbnailImage($file, $imgName);
            $data['image'] = $imgName;
        }

        // replace gallery if uploaded
        if ($request->hasFile('images')) {
            // delete old gallery files
            foreach (explode(',', $product->images) as $old) {
                File::delete([
                    public_path("uploads/products/{$old}"),
                    public_path("uploads/products/thumbnails/{$old}"),
                ]);
            }
            $gallery = [];
            foreach ($request->file('images') as $i => $file) {
                $name = $timestamp . '-' . ($i + 1) . '.' . $file->extension();
                $this->generateProductThumbnailImage($file, $name);
                $gallery[] = $name;
            }
            $data['images'] = implode(',', $gallery);
        }

        $product->update($data);

        return redirect()->route('store.products.index')
            ->with('status', 'Product berhasil diubah');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $storeId = Auth::user()->store()->first()->id;

        $product = Product::where('store_id', $storeId)
            ->findOrFail($id);

        // delete images
        File::delete(array_merge(
            [public_path("uploads/products/{$product->image}")],
            [public_path("uploads/products/thumbnails/{$product->image}")],
            collect(explode(',', $product->images))
                ->flatMap(fn($f) => [
                    public_path("uploads/products/{$f}"),
                    public_path("uploads/products/thumbnails/{$f}")
                ])
                ->toArray()
        ));

        $product->delete();

        return redirect()->route('store.products.index')
            ->with('status', 'Product berhasil dihapus');
    }

    /**
     * Generate thumbnail and main image versions.
     */
    protected function generateProductThumbnailImage($image, $imageName)
    {
        $dest      = public_path('uploads/products');
        $thumbDest = public_path('uploads/products/thumbnails');

        // Read instead of make
        $img = Image::read($image->path());
        $img->cover(540, 689, 'top')
            ->resize(540, 689, fn($c) => $c->aspectRatio())
            ->save("{$dest}/{$imageName}");

        Image::read($image->path())
            ->resize(104, 104, fn($c) => $c->aspectRatio())
            ->save("{$thumbDest}/{$imageName}");
    }
}
