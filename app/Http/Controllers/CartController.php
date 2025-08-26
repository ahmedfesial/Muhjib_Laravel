<?php


namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\GuestCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function syncGuestCart(Request $request)
    {
        $request->validate([
            'guest_token' => 'required|uuid',
        ]);

        $user = $request->user();

        $guestItems = GuestCart::where('guest_token', $request->guest_token)->get();

        foreach ($guestItems as $item) {
            $existing = CartItem::where('user_id', $user->id)
                                ->where('product_id', $item->product_id)
                                ->first();

            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }

        GuestCart::where('guest_token', $request->guest_token)->delete();

        return response()->json(['message' => 'تم نقل السلة إلى حسابك بنجاح']);
    }
}
