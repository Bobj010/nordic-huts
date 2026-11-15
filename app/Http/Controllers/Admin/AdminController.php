<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\House;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display administrator dashboard.
     */
    public function index(): View
    {
        $stats = [
            'houses_count' => House::count(),
            'categories_count' => Category::count(),
            'bookings_count' => Booking::count(),
            'users_count' => User::count(),
            'revenue_total' => Booking::where('status', 'completed')->sum('total_price'),
        ];

        $users = User::latest()->get();
        $houses = House::with(['category', 'amenities'])->latest()->get();

        return view('admin.index', [
            'stats' => $stats,
            'users' => $users,
            'houses' => $houses,
        ]);
    }

    /**
     * Update user role by administrator.
     */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:client,manager,admin'],
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('success', "Роль пользователя {$user->name} обновлена на «{$user->role}».");
    }
}
