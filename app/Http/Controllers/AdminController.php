<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\OrderPayment;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        // 1) Hitung semua entitas
        $storeCount     = Store::count();
        $userCount     = User::where('utype', 'USR')->count();
        $orderCount     = Order::count();
        $productCount   = Product::count();
        $categoryCount  = Category::count();
        $paymentCount   = OrderPayment::count();

        // 2) 10 order terbaru
        $orders = Order::withCount('orderItems')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 3) Ringkasan totals per status
        $dashboardDatas = DB::table('orders')
            ->selectRaw(<<<SQL
                SUM(total) AS TotalAmount,
                SUM(CASE WHEN status = 'ordered'   THEN total ELSE 0 END) AS TotalOrderedAmount,
                SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) AS TotalDeliveredAmount,
                SUM(CASE WHEN status = 'canceled'  THEN total ELSE 0 END) AS TotalCanceledAmount,
                COUNT(*) AS Total,
                SUM(CASE WHEN status = 'ordered'   THEN 1 ELSE 0 END) AS TotalOrdered,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS TotalDelivered,
                SUM(CASE WHEN status = 'canceled'  THEN 1 ELSE 0 END) AS TotalCanceled
            SQL)
            ->first();

        // 4) Bulanan YTD
        $monthlySub = DB::table('orders')
            ->selectRaw(<<<SQL
                MONTH(created_at)   AS MonthNo,
                DATE_FORMAT(created_at,'%b') AS MonthName,
                SUM(total)          AS TotalAmount,
                SUM(CASE WHEN status='ordered'   THEN total ELSE 0 END) AS TotalOrderedAmount,
                SUM(CASE WHEN status='delivered' THEN total ELSE 0 END) AS TotalDeliveredAmount,
                SUM(CASE WHEN status='canceled'  THEN total ELSE 0 END) AS TotalCanceledAmount
            SQL)
            ->whereYear('created_at', now()->year)
            ->groupBy('MonthNo', 'MonthName');

        $monthlyDatas = DB::table('month_names AS M')
            ->leftJoinSub($monthlySub, 'D', 'D.MonthNo', 'M.id')
            ->selectRaw(<<<SQL
                M.id   AS MonthNo,
                M.name AS MonthName,
                COALESCE(D.TotalAmount,0)          AS TotalAmount,
                COALESCE(D.TotalOrderedAmount,0)   AS TotalOrderedAmount,
                COALESCE(D.TotalDeliveredAmount,0) AS TotalDeliveredAmount,
                COALESCE(D.TotalCanceledAmount,0)  AS TotalCanceledAmount
            SQL)
            ->get();

        // 5) Seri chart
        $AmountM          = $monthlyDatas->pluck('TotalAmount')->implode(',');
        $OrderedAmountM   = $monthlyDatas->pluck('TotalOrderedAmount')->implode(',');
        $DeliveredAmountM = $monthlyDatas->pluck('TotalDeliveredAmount')->implode(',');
        $CanceledAmountM  = $monthlyDatas->pluck('TotalCanceledAmount')->implode(',');

        // 6) Totals YTD
        $TotalAmount          = $monthlyDatas->sum('TotalAmount');
        $TotalOrderedAmount   = $monthlyDatas->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = $monthlyDatas->sum('TotalDeliveredAmount');
        $TotalCanceledAmount  = $monthlyDatas->sum('TotalCanceledAmount');

        return view('admin.index', compact(
            'storeCount',
            'userCount',
            'orderCount',
            'productCount',
            'categoryCount',
            'paymentCount',
            'orders',
            'dashboardDatas',
            'AmountM',
            'OrderedAmountM',
            'DeliveredAmountM',
            'CanceledAmountM',
            'TotalAmount',
            'TotalOrderedAmount',
            'TotalDeliveredAmount',
            'TotalCanceledAmount'
        ));
    }

    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand()
    {
        return view('admin.brand-add');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateBrandThumbailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully!');
    }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->image);
            }

            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBrandThumbailsImage($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been update successfully!');
    }

    public function GenerateBrandThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function brand_delete($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return redirect()->route('admin.brands')->with('error', 'Brand not found!');
        }

        // Hapus file gambar jika ada
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }

        // Hapus data dari database
        $brand->delete();

        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully!');
    }

    // category

    public function categories(Request $request)
    {
        // 1) Ambil query builder
        $query = Category::query();

        // 2) Jika ada parameter `name`, filter dengan LIKE
        if ($search = $request->input('name')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // 3) Paginate dan sertakan querystring supaya `?name=...` tetap ada di pagination links
        $categories = $query
            ->orderBy('id', 'DESC')
            ->paginate(10)
            ->appends($request->only('name'));

        return view('admin.categories', compact('categories'));
    }

    public function category_add()
    {
        return view('admin.category-add');
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048'
        ]);

        $caregory = new Category();
        $caregory->name = $request->name;
        $caregory->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbailsImage($image, $file_name);
        $caregory->image = $file_name;
        $caregory->save();
        return redirect()->route('admin.categories')->with('status', 'category has been added successfully!');
    }

    public function GenerateCategoryThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
                File::delete(public_path('uploads/categories') . '/' . $category->image);
            }

            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumbailsImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been update successfully!');
    }

    public function category_delete($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('admin.categories')->with('error', 'Category not found!');
        }

        // Hapus file gambar jika ada
        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }

        // Hapus data dari database
        $category->delete();

        return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully!');
    }

    // Product
    public function products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function product_add()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = new Product();

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $curren_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $curren_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbailsImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if ($request->hasFile('images')) {
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if ($gcheck) {
                    $gfileName = $curren_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateProductThumbailsImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;
        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product has been added successfully!');
    }

    public function GenerateProductThumbailsImage($image, $imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());
        $img->cover(540, 689, "top");
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);

        $img->resize(104, 104, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail . '/' . $imageName);
    }

    public function product_edit($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product', 'categories', 'brands'));
    }

    public function product_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = Product::find($request->id);

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $curren_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
                File::delete(public_path('uploads/products') . '/' . $product->image);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
            }

            $image = $request->file('image');
            $imageName = $curren_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbailsImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if ($request->hasFile('images')) {
            foreach (explode(',', $product->images) as $ofile) {
                if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                    File::delete(public_path('uploads/products') . '/' . $ofile);
                }
                if (File::exists(public_path('uploads/products/thumbnails') . '/' . $ofile)) {
                    File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
                }
            }

            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if ($gcheck) {
                    $gfileName = $curren_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateProductThumbailsImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $product->images = $gallery_images;
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }

    public function product_delete($id)
    {
        $product = Product::find($id);
        if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
            File::delete(public_path('uploads/products') . '/' . $product->image);
        }
        if (File::exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
            File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
        }

        foreach (explode(',', $product->images) as $ofile) {
            if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                File::delete(public_path('uploads/products') . '/' . $ofile);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $ofile)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
            }
        }

        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully!');
    }

    // coupon

    public function coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }
    public function coupon_add()
    {
        return view('admin.coupon-add');
    }

    public function coupon_store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);
        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully!');
    }

    public function coupon_edit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit', compact('coupon'));
    }

    public function coupon_update(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);
        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully!');
    }

    public function coupon_delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted successfully');
    }

    public function orders()
    {
        $orders = Order::orderBy('created_at', 'DESC')->paginate(12);
        return view('admin.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();
        return view('admin.order-details', compact('order', 'orderItems', 'transaction'));
    }

    public function update_order_status(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;
        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
        } else if ($request->order_status == 'canceled') {
            $order->canceled_date = Carbon::now();
        }
        $order->save();

        if ($request->order_status == 'delivered') {
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            $transaction->status = 'approved';
            $transaction->save();
        }
        return back()->with("status", "Status changed successfily!");
    }

    // sliders
    public function slides(Request $request)
    {
        // Mulai query
        $query = Slide::orderBy('id', 'DESC');

        // Jika ada parameter ?search=...
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('tagline', 'like', "%{$search}%")
                    ->orWhere('title',   'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('link',    'like', "%{$search}%");
            });
        }

        // Paginate dan bawa parameter search ke pagination links
        $slides = $query->paginate(12)
            ->appends($request->only('search'));

        return view('admin.slides', compact('slides'));
    }

    public function slide_add()
    {
        return view('admin.slide-add');
    }

    public function slide_store(Request $request)
    {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048'
        ]);

        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;

            // Simpan langsung ke folder uploads/slides tanpa resize
            $image->move(public_path('uploads/slides'), $file_name);

            $slide->image = $file_name;
        }

        $slide->save();

        return redirect()->route('admin.slides')->with("status", "Slide added successfully!");
    }


    public function GenerateSlideThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/slides');
        $img = Image::read($image->path());
        $img->cover(400, 690, "top");
        $img->resize(400, 690, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function slide_edit($id)
    {
        $slide = Slide::find($id);
        return view('admin.slide-edit', compact('slide'));
    }

    public function slide_update(Request $request)
    {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);
        $slide = slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
                File::delete(public_path('uploads/slides') . '/' . $slide->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateSlideThumbailsImage($image, $file_name);
            $slide->image = $file_name;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with("status", "Slide updated successfully!");
    }

    public function slide_delete($id)
    {
        $slide = Slide::find($id);
        if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
            File::delete(public_path('uploads/slides') . '/' . $slide->image);
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with("status", "Slide deleted successfully!");
    }

    // Contact
    public function contacts()
    {
        $contacts = Contact::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.contacts', compact('contacts'));
    }

    public function contact_delete($id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        return redirect()->route('admin.contacts')->with("status", "Contact deleted successfully!");
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name', 'LIKE', "%($query)%")->get()->take(8);
        return response()->json($results);
    }

    // address

    public function address()
    {
        // Ambil ID admin yang sedang login
        $adminId = Auth::id(); // atau Auth::user()->id

        // Ambil alamat yang hanya dimiliki oleh admin yang sedang login
        $addresses = Address::with('user')
            ->where('user_id', $adminId)
            ->get();

        return view('admin.address', compact('addresses'));
    }

    public function add_address()
    {
        return view('admin.address-add');
    }

    public function addres_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'zip' => 'required',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
            'country' => 'required', //  penting!
        ]);

        Address::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'zip' => $request->zip,
            'state' => $request->state,
            'city' => $request->city,
            'address' => $request->address,
            'locality' => $request->locality,
            'landmark' => $request->landmark,
            'country' => $request->country,
        ]);

        return redirect()->route('admin.address')->with('success', 'Address added successfully.');
    }

    public function address_edit($id)
    {
        $address = Address::findOrFail($id);
        return view('admin.address-edit', compact('address'));
    }

    public function address_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'zip' => 'required',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
            'country' => 'required',
        ]);

        $address = Address::findOrFail($id);
        $address->update($request->all());

        return redirect()->route('admin.address')->with('success', 'Address updated successfully.');
    }

    public function address_destroy($id)
    {
        $address = Address::findOrFail($id);

        // Pastikan alamat milik user yang sedang login
        if ($address->user_id !== Auth::id()) {
            return redirect()->route('admin.address')->with('error', 'Unauthorized action.');
        }

        $address->delete();

        return redirect()->route('admin.address')->with('success', 'Address deleted successfully.');
    }

    public function stores(Request $request)
    {
        // Ambil query pencarian
        $q = $request->input('name');

        // Jika ada q, filter by name like %q%, lalu paginate
        $stores = Store::when($q, function ($builder) use ($q) {
            return $builder->where('name', 'like', "%{$q}%");
        })
            ->orderBy('created_at', 'DESC')
            ->paginate(12)
            // biar query string 'name' ikut di pagination links
            ->appends(['name' => $q]);

        return view('admin.stores', compact('stores', 'q'));
    }


    public function store_add()
    {
        return view('admin.store-add');
    }

    public function store_store(Request $request)
    {
        $request->validate([
            'name'         => 'required',
            'slug'         => 'required|unique:stores,slug',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description'  => 'nullable',

            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|unique:users,email',
            'owner_mobile' => 'required|string|unique:users,mobile',
            'owner_password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            // 1) Simpan User (owner)
            $user = User::create([
                'name'     => $request->owner_name,
                'email'    => $request->owner_email,
                'mobile'   => $request->owner_mobile,
                'password' => Hash::make($request->owner_password),
                'utype'    => 'STR',
            ]);

            // 2) Siapkan data Store
            $store = new Store([
                'name'        => $request->name,
                'slug'        => Str::slug($request->slug),
                'description' => $request->description,
                'owner_id'    => $user->id,
            ]);

            // 3) Handle upload image dengan Storage::store()
            if ($request->hasFile('image')) {
                // (opsional) hapus file lama jika ada, misal:
                // Storage::disk('public')->delete($store->image);

                // simpan file ke storage/app/public/stores/...
                $store->image = $request
                    ->file('image')
                    ->store('stores', 'public');
            }

            // 4) Simpan store
            $store->save();

            DB::commit();
            return redirect()
                ->route('admin.stores')
                ->with('status', 'Store and owner added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function store_edit($id)
    {
        $store = Store::findOrFail($id);
        $owner = $store->owner; // assuming Store has relation: owner()

        return view('admin.store-edit', compact('store', 'owner'));
    }

    public function store_update(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:stores,id',
            'name'          => 'required|string|max:255',
            'slug'          => 'required|unique:stores,slug,' . $request->id,
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description'   => 'nullable|string',

            'owner_name'    => 'required|string|max:255',
            'owner_email'   => 'required|email|unique:users,email,' . $request->owner_id,
            'owner_mobile'  => 'required|string|unique:users,mobile,' . $request->owner_id,
            'owner_password' => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            // 1. Ambil store & owner
            $store = Store::findOrFail($request->id);
            $owner = User::findOrFail($store->owner_id);

            // 2. Update fields dasar store
            $store->name        = $request->name;
            $store->slug        = Str::slug($request->slug);
            $store->description = $request->description;

            // 3. Handle image upload/replace
            if ($request->hasFile('image')) {
                // hapus file lama jika ada
                if ($store->image) {
                    Storage::disk('public')->delete($store->image);
                }
                // simpan file baru ke storage/app/public/stores/...
                $store->image = $request->file('image')
                    ->store('stores', 'public');
            }

            $store->save();

            // 4. Update owner (user)
            $owner->name   = $request->owner_name;
            $owner->email  = $request->owner_email;
            $owner->mobile = $request->owner_mobile;
            if ($request->filled('owner_password')) {
                $owner->password = Hash::make($request->owner_password);
            }
            $owner->save();

            DB::commit();
            return redirect()
                ->route('admin.stores')
                ->with('status', 'Store dan owner berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function store_delete($id)
    {
        DB::beginTransaction();
        try {
            $store = Store::findOrFail($id);

            if ($store->image && File::exists(public_path('uploads/stores/' . $store->image))) {
                File::delete(public_path('uploads/stores/' . $store->image));
            }

            $owner = $store->owner;
            $store->delete();

            if ($owner && $owner->utype === 'STR') {
                $owner->delete(); // Optional: only delete if utype STR
            }

            DB::commit();
            return redirect()->route('admin.stores')->with('status', 'Store and owner deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete store: ' . $e->getMessage());
        }
    }

    public function GenerateStoreThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/stores');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }
}
