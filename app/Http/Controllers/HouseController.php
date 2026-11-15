<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Category;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HouseController extends Controller
{
    /**
     * Display the catalog with filters and sorting.
     */
    public function catalog(Request $request): View
    {
        $query = House::active()->with(['category', 'amenities']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Filter by guests capacity
        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', (int) $request->input('capacity'));
        }

        // Filter by specific amenity (Many-to-Many lookup)
        if ($request->filled('amenity')) {
            $amenityId = (int) $request->input('amenity');
            $query->whereHas('amenities', function ($q) use ($amenityId) {
                $q->where('amenities.id', $amenityId);
            });
        }

        // Sorting
        match ($request->input('sort')) {
            'price_asc' => $query->orderBy('price_per_night', 'asc'),
            'price_desc' => $query->orderBy('price_per_night', 'desc'),
            'capacity' => $query->orderBy('capacity', 'desc'),
            default => $query->orderBy('is_featured', 'desc')->latest(),
        };

        $houses = $query->paginate(6)->withQueryString();
        $categories = Category::all();
        $amenities = Amenity::all();

        return view('catalog.index', [
            'houses' => $houses,
            'categories' => $categories,
            'amenities' => $amenities,
            'selectedCategory' => $request->input('category'),
            'selectedCapacity' => $request->input('capacity'),
            'selectedAmenity' => $request->input('amenity'),
            'selectedSort' => $request->input('sort'),
        ]);
    }

    /**
     * Display detailed house card.
     */
    public function show(string $slug): View
    {
        $house = House::active()
            ->where('slug', $slug)
            ->with(['category', 'amenities', 'reviews' => function ($query) {
                $query->where('is_published', true)->with('user')->latest();
            }])
            ->firstOrFail();

        $relatedHouses = House::active()
            ->where('id', '!=', $house->id)
            ->where('category_id', $house->category_id)
            ->take(2)
            ->get();

        return view('catalog.show', [
            'house' => $house,
            'relatedHouses' => $relatedHouses,
        ]);
    }
}
