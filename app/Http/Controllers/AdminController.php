<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $category=Brand::find($request->id);
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


}
