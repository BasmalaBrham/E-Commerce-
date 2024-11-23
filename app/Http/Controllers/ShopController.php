<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request){
        $size = $request->query('size') ?? 12;
        $o_column = "";
        $o_order = "";
        $order = $request->query('order') ?? -1;
        $f_brands=$request->query('brands');
        $f_categories=$request->query('categories');
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
        $brands=Brand::orderBy('name','ASC')->get();
        $categories=Category::orderBy('name','ASC')->get();
        $brandsFilter = $f_brands ? explode(',', $f_brands) : [];
        $categoriesFilter = $f_categories ? explode(',', $f_categories) : [];
        $products = Product::when($brandsFilter, function ($query) use ($brandsFilter) {
            $query->whereIn('brand_id', $brandsFilter);
        })
        ->when($categoriesFilter, function ($query) use ($categoriesFilter) {
            $query->whereIn('category_id', $categoriesFilter);
        })
        ->orderBy($o_column, $o_order)
        ->paginate($size);
        return view('shop',compact('products','size','order','brands','f_brands','categories','f_categories'));
    }

    public function productDetails($product_slug){
        $product=Product::where('slug',$product_slug)->first();
        $rproducts=Product::where('slug','<>',$product_slug)->take(8)->get();
        return view('productDetails',compact('product','rproducts'));
    }
}
