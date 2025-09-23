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

    // حذف عناصر سلة الضيف بعد النقل
    $deleted = GuestCart::where('guest_token', $request->guest_token)->delete();

    // تأكيد الحذف - عدد العناصر اللي تم حذفها
    if ($deleted > 0) {
        return response()->json(['message' => 'تم نقل السلة إلى حسابك بنجاح']);
    } else {
        return response()->json(['message' => 'لا توجد عناصر لنقلها أو تم حذفها مسبقاً']);
    }
}

}
