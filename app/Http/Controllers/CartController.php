<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Discount;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function add_to_cart(Product $product, Request $request)
    {
        $request->validate([
            'amount' => 'required|gte:1|lte:' . $product->stock
        ]);

        $user_id = Auth::id();
        $product_id = $product->id;

        Cart::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'amount' => $request->amount
        ]);

        return Redirect::route('index_product');
    }

    public function show_cart()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();
        $total_price = $this->getCartTotal(); // Mendapatkan total harga tanpa diskon
        return view('product.show_cart', compact('carts', 'total_price'));
    }

    public function update_cart(Cart $cart, Request $request)
    {
        $request->validate([
            'amount' => 'required|gte:1|lte:' . $cart->product->stock
        ]);

        $cart->update([
            'amount' => $request->amount
        ]);

        return Redirect::route('show_cart');
    }

    public function delete_cart(Cart $cart)
    {
        $cart->delete();
        return Redirect::back();
    }

    public function applyDiscount(Request $request)
{
    $discountCode = $request->input('discount_code');

    $discount = Discount::where('code', $discountCode)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->first();

    if ($discount) {
        // Validasi diskon
        if ($discount->percentage <= 0 || $discount->percentage > 100) {
            return back()->with('error', 'Invalid discount percentage.');
        }

        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();

        // Pastikan keranjang belanja tidak kosong
        if ($carts->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        $total_price = $this->calculateDiscountedPrice($discount);
        return view('product.show_cart', compact('carts', 'total_price'));
    } else {
        return back()->with('error', 'Invalid discount code.');
    }
}

    private function calculateDiscountedPrice($discount)
    {
        $originalPrice = $this->getCartTotal(); // Mendapatkan total harga tanpa diskon
        $discountedPrice = $originalPrice * (1 - ($discount->percentage / 100)); // Menghitung total harga dengan diskon
        return $discountedPrice;
    }

    private function getCartTotal()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();
        $total_price = 0;

        foreach ($carts as $cart) {
            $total_price += $cart->product->price * $cart->amount;
        }

        return $total_price;
    }

    public function checkout(Request $request)
    {
        // Ambil total harga yang sudah didiskon dari input tersembunyi
        $discountedTotalPrice = $request->input('total_price');
    
        // Lakukan logika checkout sesuai kebutuhan, misalnya:
        // Simpan order ke dalam database dengan total harga yang sudah didiskon
        // Misalnya:
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $discountedTotalPrice,
            // Tambahkan informasi order lainnya sesuai kebutuhan
        ]);
    
        // Setelah order berhasil dibuat, Anda dapat mengosongkan keranjang belanja
        Cart::where('user_id', Auth::id())->delete();
    
        // Redirect ke halaman terkait atau tampilkan pesan berhasil
        return Redirect::route('index_order')->with('success', 'Pesanan berhasil ditempatkan.');
    }
}
