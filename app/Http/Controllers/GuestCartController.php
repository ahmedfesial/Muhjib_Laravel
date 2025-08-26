<?php

namespace App\Http\Controllers;

use App\Models\GuestCart;
use Illuminate\Http\Request;

class GuestCartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'guest_token' => 'required|uuid',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $quantity = $request->quantity ?? 1;

        $item = GuestCart::where('guest_token', $request->guest_token)
                         ->where('product_id', $request->product_id)
                         ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            GuestCart::create([
                'guest_token' => $request->guest_token,
                'product_id' => $request->product_id,
                'quantity' => $quantity
            ]);
        }

        return response()->json(['message' => 'تمت إضافة المنتج للكارت']);
    }

    public function viewCart($guest_token)
    {
        $items = GuestCart::with('product')
                          ->where('guest_token', $guest_token)
                          ->get();

        return response()->json($items);
    }
}
