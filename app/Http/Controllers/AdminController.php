<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }
    public function brands(){
        $brands=Brand::orderBy('id','DESC')->paginate(10);
        return view('admin.brand',compact('brands'));
    }


    //to show the add brand page
    public function addBrand(){
        return view('admin.addBrand');
    }
    //to store brand
    public function storeBrand(Request $request){
        // Validation
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|unique:brands,slug',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048'
        ]);
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/brands'), $imageName);
            $brand->image = 'uploads/brands/' . $imageName;
        }
        $brand->save();

        return redirect()->route('admin.brands')->with('success', 'Brand has been added successfully');
    }

    //to edit brand
    public function editBrand($id){
        $brand=Brand::findOrFail($id);
        return view('admin.editBrand',compact('brand'));
    }

    //to store updated data
    public function updateBrand(Request $request){
        // Validation
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|unique:brands,slug',
            'image' => 'image|mimes:png,jpg,jpeg|max:2048'
        ]);
        $brand=Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        if (!empty($request->image)) {
            if (File::exists(public_path('uploads/brands/' . $brand->image))) {
                File::delete(public_path('uploads/brands/' . $brand->image));
            }
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/brands'), $imageName);
            $brand->image = 'uploads/brands/' . $imageName;
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('success', 'Brand has been updated successfully');
    }

    //to delete brand
    public function deleteBrand($id){
        $brand = Brand::findOrFail($id);
        if (File::exists(public_path('uploads/brands/' . $brand->image))) {
            File::delete(public_path('uploads/brands/' . $brand->image));
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('success', 'Brand has been deleted successfully');
    }


    //categories
    public function categories(){
        $categories=Category::orderBy('id','DESC')->paginate(10);
        return view('admin.category.categories',compact('categories'));
    }
    //to show the add brand page
    public function addCategory(){
        return view('admin.category.addCategory');
    }
    //to store Category
    public function storeCategory(Request $request){
        // Validation
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|unique:categories,slug',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048'
        ]);
        $category = new Category();
        $category->name = $request->name;
        $category->slug = $request->slug;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/categories'), $imageName);
            $category->image = 'uploads/categories/' . $imageName;
        }
        $category->save();

        return redirect()->route('admin.categories')->with('success', 'category has been added successfully');
    }

    //to edit brand
    public function editCategory($id){
        $category=Category::findOrFail($id);
        return view('admin.category.editCategory',compact('category'));
    }

    //to store updated category
    public function updateCategory(Request $request){
        // Validation
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|unique:categories,slug',
            'image' => 'image|mimes:png,jpg,jpeg|max:2048'
        ]);
        $category=Category::find($request->id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        if (!empty($request->image)) {
            if (File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/categories'), $imageName);
            $category->image = 'uploads/categories/' . $imageName;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('success', 'category has been updated successfully');
    }
    //tO delete
    public function deleteCategory($id){
        $category = Category::findOrFail($id);
        if (File::exists(public_path('uploads/categories/' . $category->image))) {
            File::delete(public_path('uploads/categories/' . $category->image));
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('success', 'Category has been deleted successfully');
    }

    //products
    public function products(){
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.product.products',compact('products'));
    }

    //to show the add product page
    public function addProduct(){
        $categories=Category::select('id','name')->orderBy('name')->get();
        $brands=Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product.addProduct',compact('categories','brands'));
    }

    //to store product in db
    public function storeProduct(Request $request){
        // Validation
        $request->validate([
            'name' => 'required|max:100',
            'slug' => 'required|unique:products,slug|max:100',
            'short_description' => 'required|max:255',
            'description' => 'required|max:1000',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'SKU' => 'required|unique:products,SKU|max:50',
            'stock_status' => 'required|in:instock,outofstock',
            'featured' => 'required|boolean',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        // Create product
        $product = new Product();
        $product->name = $request->input('name');
        $product->slug = Str::slug($request->input('name')); // Use Str::slug() for better readability
        $product->short_description = $request->input('short_description');
        $product->description = $request->input('description');
        $product->regular_price = $request->input('regular_price');
        $product->sale_price = $request->input('sale_price');
        $product->SKU = $request->input('SKU');
        $product->stock_status = $request->input('stock_status');
        $product->featured = $request->input('featured');
        $product->quantity = $request->input('quantity');
        $product->category_id = $request->input('category_id');
        $product->brand_id = $request->input('brand_id');

        // Handle main image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $imageName = Carbon::now()->timestamp . '.' . $ext;
            $image->move(public_path('uploads/products'), $imageName);
            $product->image = 'uploads/products/' . $imageName;
        }

        // Handle gallery images upload
        if ($request->hasFile('images')) {
            $galleryArr = [];
            foreach ($request->file('images') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products/gallery'), $fileName);
                $galleryArr[] = 'uploads/products/gallery/' . $fileName;
            }
            $product->images = implode(',', $galleryArr);
        }

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product has been added successfully!');
    }

    //update
    public function editProduct($id){
        $product=Product::findOrFail($id);
        $categories=Category::select('id','name')->orderBy('name')->get();
        $brands=Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product.editProduct',compact('categories','brands','product'));
    }

    public function updateProduct(Request $request){
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
        $product=Product::findOrFail($request->id);
        $product->name = $request->input('name');
        $product->slug = Str::slug($request->input('name')); // Use Str::slug() for better readability
        $product->short_description = $request->input('short_description');
        $product->description = $request->input('description');
        $product->regular_price = $request->input('regular_price');
        $product->sale_price = $request->input('sale_price');
        $product->SKU = $request->input('SKU');
        $product->stock_status = $request->input('stock_status');
        $product->featured = $request->input('featured');
        $product->quantity = $request->input('quantity');
        $product->category_id = $request->input('category_id');
        $product->brand_id = $request->input('brand_id');

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/products/' . $product->image))) {
                File::delete(public_path('uploads/products/' . $product->image));
            }
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/products'), $imageName);
            $product->image = 'uploads/products/' . $imageName;
        }

        //gallery image
        if ($request->hasFile('images')) {
            if (!empty($product->images)) {
                $oldImages = explode(',', $product->images);
                foreach ($oldImages as $oldImage) {
                    $imagePath = public_path($oldImage);
                    if (File::exists($imagePath)) {
                        File::delete($imagePath);
                    } else {
                        logger("Image not found: " . $imagePath);
                    }
                }
            }
            $galleryArr = [];
            foreach ($request->file('images') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products/gallery'), $fileName);
                $galleryArr[] = 'uploads/products/gallery/' . $fileName;
            }
            $product->images = implode(',', $galleryArr);
            $product->save();
        }
        $product->save();
            $product->save();
            return redirect()->route('admin.products')->with('success', 'product has been updated successfully');
    }
    //delete product
    public function deleteProduct($id){
        $product = Product::findOrFail($id);
        if (File::exists(public_path('uploads/products/' . $product->image))) {
            File::delete(public_path('uploads/products/' . $product->image));
        }
        if (File::exists(public_path('uploads/products/gallery/' . $product->image))) {
            File::delete(public_path('uploads/products/gallery/' . $product->image));
        }
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product has been deleted successfully');
    }

    //coupons
    public function coupons(){
        $coupons=Coupon::orderBy('expiry_date','DESC')->paginate(12);
        return view('admin.coupon.coupons',compact('coupons'));
    }
    //to show add coupon page
    public function addCoupon(){
        return view('admin.coupon.addCoupon');
    }
    //to store data
    public function storeCoupon(Request $request){
        $request->validate([
            'code'=>'required',
            'type'=>'required',
            'value'=>'required|numeric',
            'cart_value'=>'required|numeric',
            'expiry_date'=>'required|date'
        ]);
        $coupon=new Coupon();
        $coupon->code=$request->code;
        $coupon->type=$request->type;
        $coupon->value=$request->value;
        $coupon->cart_value=$request->cart_value;
        $coupon->expiry_date=$request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('success','Coupons has been added successfully');
    }
}
