<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    public function __construct() {}

    public function index(Request $request) {
        $paginationSize = 2;
        $query = array("page"=>1, "search"=>"");
        $paginationUrls = array();
        $url = $request->url();
        $user = Cookie::get('User');
        if(isset($user))
        {
            $cart = json_decode($user,TRUE)["cart"];
        }
        else{
            $cart = null;
        }

        if (isset($request->search)) {
            $query["search"] = $request->search;
        }

        if (isset($request->page)) {
            $query["page"] = $request->page;
        }

        $products = Http::get("http://localhost:3000/api/v1/products", $query);
        return view('store', ["products"=>$products->json()["product"], "cart"=>$cart, "user"=>$user]);
    }

    public function cart(Request $request) {

        $user = Cookie::get('User');
        if(isset($user))
        {
            $cart = json_decode($user,TRUE)["cart"];
            $cartStore = json_decode($user,TRUE)["cartStore"];
        }
        else{
            $cart = null;
            $cartStore = null;
        }

        return view('app-cart', ["user"=>$user,"cart"=>$cart, "cartStore"=>$cartStore]);
    }

    public function checkout(Request $request) {
        $bearer = Cookie::get('Bearer');
        $user = Cookie::get('User');
        $storeId = $request->id;
        $cart = Http::withHeaders(["authorization"=>$bearer])->get("http://localhost:3000/api/v1/cartbyidstore", ["id"=>$storeId]);
        $new = $cart->json()["cart"];
        $totalPrice = 0;
        if(isset($user))
        {
            // $cart = json_decode($user,TRUE)["cart"];
            if(isset($new))
            {
                foreach ($new as $cartDetail) {
                    $item = json_decode(json_encode($cartDetail),TRUE);
                    $totalPrice += $item['amount']*$item['price'];
                }
                // for($i = 0; $i < count($new); $i++)
                // {
                //     $totalPrice += $new[$i]['amount']*$new[$i]['price'];
                // }
            }
        }
        else{
            $new = null;
        }

        return view('app-checkout', ["user"=>$user,"cart"=>$new, "totalPrice"=>$totalPrice, "storeId"=>$storeId]);
    }

    public function createOrder(Request $request) {
        $bearer = Cookie::get('Bearer');
        $user = Cookie::get('User');
        $storeId = $request->id;
        $data = Http::withHeaders(["authorization"=>$bearer])->post("http://localhost:3000/api/v1/createorder", ["id"=>$storeId,  $request->all()]);
        Cookie::queue('User', $data->json()["user"], 60);
        return redirect("/store");
    }

    public function addCart(Request $request) {
        $bearer = Cookie::get('Bearer');
        $data = Http::withHeaders(["authorization"=>$bearer])->post("http://localhost:3000/api/v1/user/addcart", ["id"=>$request->all()["id"]]);
        Cookie::queue('User', $data->json()["user"], 60);
        return response()->json(array("msg"=>$data->json()), 200);
    }

    public function blockStore(Request $request) {
        $bearer = Cookie::get('Bearer');
        $stores = Http::withHeaders(["authorization"=>$bearer])->post("http://localhost:3000/api/v1/store/block", ["id"=>$request->all()["id"]]);
        return response()->json(array("msg"=>$stores->json()), 200);
    }
}
