<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Category;
use App\Models\House;
use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the main showcase landing page.
     */
    public function index(): View
    {
        $featuredHouses = House::featured()
            ->with(['category', 'amenities'])
            ->take(3)
            ->get();

        $reviews = Review::with(['user', 'house'])
            ->where('is_published', true)
            ->latest()
            ->take(2)
            ->get();

        $categories = Category::withCount('houses')->get();
        $amenities = Amenity::all();

        return view('home', [
            'houses' => $featuredHouses,
            'reviews' => $reviews,
            'categories' => $categories,
            'amenities' => $amenities,
        ]);
    }
}
