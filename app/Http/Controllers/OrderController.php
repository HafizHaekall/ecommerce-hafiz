<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index_order()
    {
        $user = Auth::user();
        $is_admin = $user->is_admin;
        $orders = $is_admin ? Order::all() : Order::where('user_id', $user->id)->get();
        return view('order.index_order', compact('orders'));
    }

    public function show_order(Order $order)
    {
        $user = Auth::user();
        $is_admin = $user->is_admin;

        if ($is_admin || $order->user_id == $user->id) {
            return view('order.show_order', compact('order'));
        }

        return redirect()->route('index_order')->with('error', 'Anda tidak diizinkan untuk melihat pesanan ini.');
    }

    public function checkout()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();

        if ($carts->isEmpty()) {
            return redirect()->route('show_cart')->with('error', 'Keranjang Anda kosong.');
        }

        try {
            DB::beginTransaction();

            $order = Order::create(['user_id' => $user_id]);

            foreach ($carts as $cart) {
                $product = Product::find($cart->product_id);

                $product->update(['stock' => $product->stock - $cart->amount]);

                Transaction::create([
                    'amount' => $cart->amount,
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id
                ]);

                $cart->delete();
            }

            DB::commit();

            return redirect()->route('index_order')->with('success', 'Pesanan berhasil ditempatkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('show_cart')->with('error', 'Gagal menempatkan pesanan. Silakan coba lagi.');
        }
    }

    public function submit_payment_receipt(Order $order, Request $request)
    {
        $request->validate([
            'payment_receipt' => 'required|file|mimes:jpeg,png,pdf|max:2048', // Sesuaikan jenis dan ukuran berkas sesuai kebutuhan
        ]);

        $file = $request->file('payment_receipt');
        $path = time() . '_' . $order->id . '.' . $file->getClientOriginalExtension();

        Storage::disk('local')->put('public/' . $path, file_get_contents($file));

        $order->update(['payment_receipt' => $path]);

        return redirect()->route('show_order', $order)->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    public function confirm_payment(Order $order)
    {
        $order->update(['is_paid' => true]);

        return redirect()->route('index_order')->with('success', 'Pembayaran dikonfirmasi dengan berhasil.');
    }
}
