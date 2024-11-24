<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class WishlistController extends Controller
{
    public function index(){
        $items= Cart::instance('wishlist')->content();
        return view('wishlist',compact('items'));
    }
    public function add_to_wishlist(Request $request){
        Cart::instance('wishlist')->add($request->id,$request->name,$request->quantity,$request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    public function removeItem($rowId){
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back();
    }
    public function emptyWishlist(){
        Cart::instance('wishlist')->destroy();
        return redirect()->back();
    }
}