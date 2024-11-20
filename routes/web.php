<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
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


});