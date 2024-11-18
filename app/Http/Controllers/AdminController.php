<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }
    public function brands(){
        $brands=Brand::orderBy('id','DESC')->paginate(10);
        return view('admin.brand',compact('brands'));
    }


    //toshow the add brand page
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

    // public function storeBrand(Request $request){
    //     $request->validate([
    //         'name'=>'required|string',
    //         'slug'=>'required|unique:brands,slug',
    //         'image'=>'required|mimes:png,jpg,jpeg'
    //     ]);

    //     $brand=new Brand();
    //     $brand->name=$request->name;
    //     $brand->slug=$request->slug;
    //     $image=$request->image;
    //     $file_ext=$request->file('image')->extension();
    //     $file_name=Carbon::neew()->timestamp.'.'.$file_ext;
    //     $this->GenerateBrandImage($image,$file_name);
    //     $brand->image=$file_name;
    //     $brand->save();
    //     return redirect()->route('admin.brands')->with('success','Brand has been added successfuly');
    // }
    // public function GenerateBrandImage($image,$imageName){
    //     $destination=public_path('uploads/brands');
    //     $img=Image::read($image->path);
    //     $img->cover(124,124,('top'));
    //     $img->resize(124.124,function($constraint){
    //         $constraint->aspectRatio();
    //     })->save($destination.'/'.$imageName);
    // }
}
