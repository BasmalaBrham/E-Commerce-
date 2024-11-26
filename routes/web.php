<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop',[ShopController::class,'index'])->name('shop.index');
Route::get('/shop/{product_slug}',[ShopController::class,'productDetails'])->name('shop.product.details');
Route::get('/cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add',[CartController::class,'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}',[CartController::class,'increaseQuantity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowId}',[CartController::class,'decreaseQuantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}',[CartController::class,'removeItem'])->name('cart.item.remove');
Route::delete('/cart/clear',[CartController::class,'emptyCart'])->name('cart.empty');

Route::post('/cart/apply-coupon',[CartController::class,'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/cart/remove-coupon',[CartController::class,'removeCouponCode'])->name('cart.coupon.remove');

Route::post('/wishlist/add',[WishlistController::class,'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist',[WishlistController::class,'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}',[WishlistController::class,'removeItem'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear',[WishlistController::class,'emptyWishlist'])->name('wishlist.item.clear');
Route::post('/wishlist/move-to-cart/{rowId}',[WishlistController::class,'moveToCart'])->name('wishlist.move.to.cart');

Route::get('/checkout',[CartController::class,'checkout'])->name('cart.checkout');
Route::post('/place-an-order',[CartController::class,'placeAnOrder'])->name('cart.place.an.order');
Route::get('/order-confirmation',[CartController::class,'orderConfirmation'])->name('cart.order.confirmation');


Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard',[UserController::class,'index'])->name('user.index');
});
Route::middleware(['auth',AuthAdmin::class])->group(function(){
    Route::get('/admin',[AdminController::class,'index'])->name('admin.index');
    Route::get('/admin/brands',[AdminController::class,'brands'])->name('admin.brands');
    Route::get('/admin/brand/add',[AdminController::class,'addBrand'])->name('admin.brand.add');
    Route::post('/admin/brand/store',[AdminController::class,'storeBrand'])->name('admin.brand.store');
    Route::get('/admin/brand/edit/{id}',[AdminController::class,'editBrand'])->name('admin.brand.edit');
    Route::put('/admin/brand/update',[AdminController::class,'updateBrand'])->name('admin.brand.update');
    Route::delete('/admin/brand/{id}/delete', [AdminController::class, 'deleteBrand'])->name('admin.brand.delete');

    //categories
    Route::get('/admin/categories',[AdminController::class,'categories'])->name('admin.categories');
    Route::get('/admin/category/add',[AdminController::class,'addCategory'])->name('admin.category.add');
    Route::post('/admin/category/store',[AdminController::class,'storeCategory'])->name('admin.category.store');
    Route::get('/admin/category/edit/{id}',[AdminController::class,'editCategory'])->name('admin.category.edit');
    Route::put('/admin/category/update',[AdminController::class,'updateCategory'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete', [AdminController::class, 'deleteCategory'])->name('admin.category.delete');

    //products
    Route::get('/admin/products',[AdminController::class,'products'])->name('admin.products');
    Route::get('/admin/products/add',[AdminController::class,'addProduct'])->name('admin.product.add');
    Route::post('/admin/product/store',[AdminController::class,'storeProduct'])->name('admin.product.store');
    Route::get('/admin/product/edit/{id}',[AdminController::class,'editProduct'])->name('admin.product.edit');
    Route::put('/admin/product/update',[AdminController::class,'updateProduct'])->name('admin.product.update');
    Route::delete('/admin/product/{id}/delete', [AdminController::class, 'deleteProduct'])->name('admin.product.delete');
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon/add', [AdminController::class, 'addCoupon'])->name('admin.coupon.add');
    Route::post('/admin/coupon/store', [AdminController::class, 'storeCoupon'])->name('admin.coupon.store');
    Route::get('/admin/coupon/edit/{id}', [AdminController::class, 'editCoupon'])->name('admin.coupon.edit');
    Route::put('/admin/coupon/update', [AdminController::class, 'updateCoupon'])->name('admin.coupon.update');
    Route::delete('/admin/coupon/{id}/delete', [AdminController::class, 'deleteCoupon'])->name('admin.coupon.delete');

    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/order/details/{order_id}', [AdminController::class, 'orderDetails'])->name('admin.order.details');

});