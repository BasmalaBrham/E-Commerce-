<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Slide;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function index(){
        $orders = Order::orderBy('created_at', 'DESC')->take(10)->get();

        $dashboardData = DB::select("
            SELECT
                SUM(total) AS TotalAmount,
                SUM(IF(status = 'ordered', total, 0)) AS TotalOrderedAmount,
                SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
                SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount,
                COUNT(*) AS Total,
                SUM(IF(status = 'ordered', 1, 0)) AS TotalOrdered,
                SUM(IF(status = 'delivered', 1, 0)) AS TotalDelivered,
                SUM(IF(status = 'canceled', 1, 0)) AS TotalCanceled
            FROM orders
        ");

        $monthlyData = DB::select("
            SELECT
                M.id AS MonthId,
                M.name AS MonthName,
                IFNULL(D.TotalAmount, 0) AS TotalAmount,
                IFNULL(D.TotalOrderedAmount, 0) AS TotalOrderedAmount,
                IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                IFNULL(D.TotalCanceledAmount, 0) AS TotalCanceledAmount
            FROM month_names M
            LEFT JOIN (
                SELECT
                    MONTH(created_at) AS MonthId,
                    SUM(total) AS TotalAmount,
                    SUM(IF(status = 'ordered', total, 0)) AS TotalOrderedAmount,
                    SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
                    SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount
                FROM orders
                WHERE YEAR(created_at) = YEAR(NOW())
                GROUP BY MONTH(created_at)
            ) D ON D.MonthId = M.id
        ");

        $Amount = implode(',', collect($monthlyData)->pluck('TotalAmount')->toArray());
        $OrderedAmount = implode(',', collect($monthlyData)->pluck('TotalOrderedAmount')->toArray());
        $DeliveredAmount = implode(',', collect($monthlyData)->pluck('TotalDeliveredAmount')->toArray());
        $CanceledAmount = implode(',', collect($monthlyData)->pluck('TotalCanceledAmount')->toArray());

        $TotalAmount = collect($monthlyData)->sum('TotalAmount');
        $TotalOrderedAmount = collect($monthlyData)->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = collect($monthlyData)->sum('TotalDeliveredAmount');
        $TotalCanceledAmount = collect($monthlyData)->sum('TotalCanceledAmount');

        return view('admin.index', compact(
            'orders', 'dashboardData', 'Amount', 'OrderedAmount', 'DeliveredAmount',
            'CanceledAmount', 'TotalAmount', 'TotalOrderedAmount', 'TotalDeliveredAmount',
            'TotalCanceledAmount'
        ));
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

    //to show edit page
    public function editCoupon($id){
        $coupon=Coupon::findOrFail($id);
        return view('admin.coupon.editCoupon',compact('coupon'));
    }
    //to update
    public function updateCoupon(Request $request){
        $request->validate([
            'code'=>'required',
            'type'=>'required',
            'value'=>'required|numeric',
            'cart_value'=>'required|numeric',
            'expiry_date'=>'required|date'
        ]);
        $coupon=Coupon::find($request->id);
        $coupon->code=$request->code;
        $coupon->type=$request->type;
        $coupon->value=$request->value;
        $coupon->cart_value=$request->cart_value;
        $coupon->expiry_date=$request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('success','Coupons has been updated successfully');
    }
    //to delete coupon
    public function deleteCoupon($id){
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('success', 'Coupon has been deleted successfully');
    }

    public function orders(){
        $orders=Order::orderBy('created_at','DESC')->paginate(12);
        return view('admin.order.orders',compact('orders'));
    }
    //to show order details
    public function orderDetails($order_id){
        $order=Order::find($order_id);
        $orderItems=OrderItem::where('order_id',$order_id)->orderBy('created_at','DESC')->paginate(12);
        $transaction=Transaction::where('order_id',$order_id)->first();
        return view('admin.order.orderDetails',compact('order','orderItems','transaction'));
    }

    public function updateOrderStatus(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;

        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
        } elseif ($request->order_status == 'canceled') {
            $order->canceled_date = Carbon::now();
        }

        $order->save();

        if ($request->order_status == 'delivered') {
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            $transaction->status = 'approved';
            $transaction->save();
        }

        return back()->with("success", "Status changed successfully");
    }

    public function slides(){
        $slides= Slide::orderBy('created_at','DESC')->paginate(12);
        return view('admin.slide.slides',compact('slides'));
    }

    public function addSlide(){
        return view('admin.slide.addSlide');
    }

    public function storeSlide(Request $request){
        $request->validate([
            'tagline'=>'required',
            'title'=>'required',
            'subtitle'=>'required',
            'link'=>'required',
            'image'=>'required|mimes:png,jpg,jpeg|max:2024',
            'status'=>'required'
        ]);
        $slide=new Slide();
        $slide->tagline=$request->tagline;
        $slide->title=$request->title;
        $slide->subtitle=$request->subtitle;
        $slide->link=$request->link;
        $slide->status=$request->status;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/slides'), $imageName);
            $slide->image = 'uploads/slides/' . $imageName;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with('success','Slide added successfully!');
    }

    public function editSlide($id){
        $slide=Slide::findOrFail($id);
        return view('admin.slide.editSlide',compact('slide'));
    }

    public function updateSlide(Request $request){
        $request->validate([
            'tagline'=>'required',
            'title'=>'required',
            'subtitle'=>'required',
            'link'=>'required',
            'image'=>'required|mimes:png,jpg,jpeg|max:2024',
            'status'=>'required'
        ]);
        $slide=Slide::find($request->id);
        $slide->tagline=$request->tagline;
        $slide->title=$request->title;
        $slide->subtitle=$request->subtitle;
        $slide->link=$request->link;
        $slide->status=$request->status;
        if ($request->hasFile('image')) {
            if (!empty($slide->image) && File::exists(public_path($slide->image))) {
                File::delete(public_path($slide->image));
            }
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/slides'), $imageName);
            $slide->image = 'uploads/slides/' . $imageName;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with('success', 'slide$slide has been updated successfully');
    }
    //tO delete
    public function deleteSlide($id){
        $slide = Slide::findOrFail($id);
        if (!empty($slide->image) && File::exists(public_path($slide->image))) {
            File::delete(public_path($slide->image));
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with('success', 'slide$slide has been deleted successfully');
    }

}
