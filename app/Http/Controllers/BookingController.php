<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingStoreRequest;
use App\Models\Booking;
use App\Models\House;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Store a newly created booking request.
     */
    public function store(BookingStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $house = House::active()->findOrFail($validated['house_id']);

        // Check if guests count is within house limit
        if ($validated['guests_count'] > $house->capacity) {
            return back()
                ->withInput()
                ->with('error', "Выбранный домик рассчитан максимум на {$house->capacity} гостей.");
        }

        // Calculate nights and price
        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $nights = $checkIn->diffInDays($checkOut);
        $nights = $nights > 0 ? $nights : 1;

        $totalPrice = $nights * (float) $house->price_per_night;

        Booking::create([
            'user_id' => Auth::id(),
            'house_id' => $house->id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'guests_count' => $validated['guests_count'],
            'total_price' => $totalPrice,
            'status' => 'pending',
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()
            ->route('client.bookings')
            ->with('success', "Заявка на бронирование домика «{$house->name}» успешно оформлена! Менеджер свяжется с вами.");
    }

    /**
     * Show client's personal bookings list.
     */
    public function myBookings(): View
    {
        $bookings = Auth::user()
            ->bookings()
            ->with(['house.category', 'house.amenities'])
            ->latest()
            ->get();

        return view('client.bookings', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Cancel an unconfirmed booking by the client.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        // Enforce boundary: only author can cancel their own booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Вы не можете отменить чужое бронирование.');
        }

        // Only pending bookings can be cancelled by client
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Отменить можно только заявки со статусом «Ожидает подтверждения».');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Бронирование успешно отменено.');
    }
}
