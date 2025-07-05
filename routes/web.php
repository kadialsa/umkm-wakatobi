<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\SearchController;
// use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Store\OrderController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use App\Http\Middleware\AuthStore;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}', [CartController::class, 'increase_cart_quntity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');

Route::post('/cart/apply-coupon', [CartController::class, 'apply_coupon_code'])->name('cart.coupon.apply');
Route::delete('/cart/ramove-coupon', [CartController::class, 'remove_coupon_code'])->name('cart.coupon.remove');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::delete('/wishlist/item/remove/{rowId}', [WishlistController::class, 'remove_item'])->name('wishlis.item.remove');
Route::delete('/wishlist/clear', [WishlistController::class, 'empty_wishlist'])->name('wishlist.item.clear');
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move.to.cart');


Route::get('/checkout', [CartController::class, 'Checkout'])->name('cart.checkout');
Route::post('/place-an-order', [CartController::class, 'place_an_order'])->name('cart.place.an.order');
Route::get('/order-confirmation', [CartController::class, 'order_confirmation'])->name('cart.order.confirmation');

Route::get('/contact-us', [HomeController::class, 'contact'])->name('home.contact');
Route::post('/contact/store', [HomeController::class, 'contact_store'])->name('home.contact.store');

// Route::get('/search', [HomeController::class, 'search'])->name('home.search');

Route::get('/search', [SearchController::class, 'index'])
    ->name('search');

// Blog
Route::get('/articles', [HomeController::class, 'articles'])->name('home.articles');
Route::get('/articles/{article:slug}', [HomeController::class, 'showArticle'])
    ->name('home.articles.show');

// Test Location
// Pencarian kel/desa
Route::get('/komship/search-address', [LocationController::class, 'searchAddress']);
// Hitung ongkir
Route::get('/komship/calculate-cost',  [LocationController::class, 'calculateCost']);

// Cek demo
Route::get('/komship/demo', [LocationController::class, 'demo']);





// USER
Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');

    // Lihat detail profil
    Route::get('/account-details', [UserController::class, 'detailsIndex'])
        ->name('user.details');
    // Form edit profil
    Route::get('/account-details/edit', [UserController::class, 'detailsEdit'])
        ->name('user.details.edit');
    // Proses update profil
    Route::put('/account-details', [UserController::class, 'detailsUpdate'])
        ->name('user.details.update');

    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-order/{order_id}/details', [UserController::class, 'orders_details'])->name('user.order.details');
    Route::put('/account-order/cancel-order', [UserController::class, 'order_cancel'])->name('account.cancel');

    // Address
    Route::get('account-addresses', [AddressController::class, 'index'])
        ->name('user.address.index');
    Route::get('account-addresses/create', [AddressController::class, 'create'])
        ->name('user.address.create');
    Route::post('account-addresses', [AddressController::class, 'store'])
        ->name('user.address.store');
    Route::get('account-addresses/{id}/edit', [AddressController::class, 'edit'])
        ->name('user.address.edit');
    Route::put('account-addresses/{id}', [AddressController::class, 'update'])
        ->name('user.address.update');
    Route::delete('account-addresses/{id}', [AddressController::class, 'destroy'])
        ->name('user.address.destroy');

    // MIDTRANS
    Route::post('midtrans/notification', [MidtransController::class, 'notificationHandler'])
        ->name('midtrans.notification');
});

// STORE
Route::middleware(['auth', AuthStore::class])
    ->prefix('store')
    ->name('store.')
    ->group(function () {
        Route::get('/', [StoreController::class, 'index'])->name('index');

        // Product
        Route::resource('products', ProductController::class)
            ->except(['show']);

        // Order
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');
        Route::put('orders/{order_id}/update-status', [OrderController::class, 'updateStatus'])
            ->name('orders.update.status');

        // Endpoints untuk processing
        Route::post('orders/{order}/ship',      [OrderController::class, 'ship'])->name('orders.ship');
        Route::post('orders/{order}/deliver',   [OrderController::class, 'deliver'])->name('orders.deliver');
        Route::post('orders/{order}/complete',  [OrderController::class, 'complete'])->name('orders.complete');
        Route::post('orders/{order}/cancel',    [OrderController::class, 'cancel'])->name('orders.cancel');

        // Order Tracking
        Route::post('orders/{order}/trackings', [OrderTrackingController::class, 'store'])->name('orders.trackings.store');
        Route::put('orders/{order}/trackings/{tracking}', [OrderTrackingController::class, 'update'])->name('orders.trackings.update');

        // Profile
        Route::get('/profile', [StoreController::class, 'profile'])
            ->name('profile');
        Route::post('/profile', [StoreController::class, 'updateProfile'])
            ->name('profile.update');
    });



// ADMIN
Route::middleware(['auth', AuthAdmin::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('admin/category/store', [AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('/admin/category/{id}/edit', [AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/admin/category/update', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete', [AdminController::class, 'category_delete'])->name('admin.category.delete');

    Route::get('/admin/stores', [AdminController::class, 'stores'])->name('admin.stores');
    Route::get('/admin/store/add', [AdminController::class, 'store_add'])->name('admin.store.add');
    Route::post('admin/store/store', [AdminController::class, 'store_store'])->name('admin.store.store');
    Route::get('/admin/store/{id}/edit', [AdminController::class, 'store_edit'])->name('admin.store.edit');
    Route::put('/admin/store/update', [AdminController::class, 'store_update'])->name('admin.store.update');
    Route::delete('/admin/store/{id}/delete', [AdminController::class, 'store_delete'])->name('admin.store.delete');

    Route::get('/admin/slides', [AdminController::class, 'slides'])->name('admin.slides');
    Route::get('/admin/slide/add', [AdminController::class, 'slide_add'])->name('admin.slide.add');
    Route::post('/admin/slide/store', [AdminController::class, 'slide_store'])->name('admin.slide.store');
    Route::get('admin/slide/{id}/edit', [AdminController::class, 'slide_edit'])->name('admin.slide.edit');
    Route::put('/admin/slide/update', [AdminController::class, 'slide_update'])->name('admin.slide.updete');
    Route::delete('/admin/slide/{id}/delete', [AdminController::class, 'slide_delete'])->name('admin.slide.delete');

    Route::get('/admin/contact', [AdminController::class, 'contacts'])->name('admin.contacts');
    Route::delete('/admin/contact/{id}/delete', [AdminController::class, 'contact_delete'])->name('admin.contact.delete');

    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');

    Route::get('/admin/address', [AdminController::class, 'address'])->name('admin.address');

    Route::get('/admin/address/add', [AdminController::class, 'add_address'])->name('admin.address.add');
    Route::post('/admin/address/store', [AdminController::class, 'addres_store'])->name('admin.address.store');

    Route::get('/admin/address/{id}/edit', [AdminController::class, 'address_edit'])->name('admin.address.edit');
    Route::put('/admin/address/{id}', [AdminController::class, 'address_update'])->name('admin.address.update');

    Route::delete('/admin/address/{id}', [AdminController::class, 'address_destroy'])->name('admin.address.delete');

    // MIDTRANS (Check Later)
    Route::get('admin/orders', [OrderController::class, 'index']);

    // BLOG
    Route::resource('/admin/blog', BlogController::class);

    // USER MANAGEMENT
    Route::resource('/admin/users', AdminUserController::class)
        ->names([
            'index'   => 'admin.users.index',
            'create'  => 'admin.users.create',
            'store'   => 'admin.users.store',
            'show'    => 'admin.users.show',
            'edit'    => 'admin.users.edit',
            'update'  => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
});
