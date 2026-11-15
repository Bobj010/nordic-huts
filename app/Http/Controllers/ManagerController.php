<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagerController extends Controller
{
    /**
     * Display manager reception desk with list of all bookings.
     */
    public function index(Request $request): View
    {
        $query = Booking::with(['house.category', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('manager.index', [
            'bookings' => $bookings,
            'stats' => $stats,
            'selectedStatus' => $request->input('status'),
        ]);
    }

    /**
     * Update booking status by manager (confirmed, completed, cancelled).
     */
    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,completed,cancelled'],
        ]);

        $booking->update(['status' => $validated['status']]);

        $label = $booking->status_info['label'];

        return back()->with('success', "Статус бронирования #{$booking->id} успешно изменен на «{$label}».");
    }
}
