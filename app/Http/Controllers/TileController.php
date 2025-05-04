<?php

namespace App\Http\Controllers;

use App\Models\Tile;
use Illuminate\Http\Request;
use App\Helpers\HelperMethods;
use Illuminate\Support\Facades\Log;

class TileController extends Controller
{
    public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['store', 'update', 'destroy']);
    }

    // public function index(Request $request)
    // {
    //     // Validate query parameters
    //     $validated = $request->validate([
    //         'paginate_count' => 'nullable|integer|min:1',
    //         'search' => 'nullable|string|max:255',
    //         'category' => 'nullable|string|exists:categories,name',
    //         'color' => 'nullable|string|exists:colors,name',
    //     ]);

    //     // Get query parameters
    //     $paginate_count = $validated['paginate_count'] ?? 10;
    //     $search = $validated['search'] ?? null;
    //     $category = $validated['category'] ?? null;
    //     $color = $validated['color'] ?? null;


    //     // Build the query
    //     $query = Tile::with(['categories', 'colors']);

    //     // Apply search filter (match names starting with search term)
    //     if ($search) {
    //         $query->where('name', 'like', $search . '%');
    //     }

    //     // Apply category filter
    //     if ($category) {
    //         $query->whereHas('categories', function ($q) use ($category) {
    //             $q->where('name', $category);
    //         });
    //     }

    //     // Apply color filter
    //     if ($color) {
    //         $query->whereHas('colors', function ($q) use ($color) {
    //             $q->where('name', $color);
    //         });
    //     }

    //     // Retrieve all matching tiles
    //     $tiles = $query->paginate($paginate_count);

    //     // Debugging: Log the query for inspection
    //     \Log::info('Tile search query: ' . $query->toSql(), $query->getBindings());

    //     // Handle case where no tiles are found
    //     if ($tiles->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No tiles found',
    //             'data' => [],
    //         ], 404);
    //     }

    //     // Return all found tiles
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Tiles retrieved successfully',
    //         'data' => $tiles,
    //     ], 200);
    // }

    public function index(Request $request)
    {
        // Validate query parameters
        $validated = $request->validate([
            'paginate_count' => 'nullable|integer|min:1',
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|string|exists:categories,name',
            'color' => 'nullable|string|exists:colors,name',
        ]);

        // Get query parameters
        $paginate_count = $validated['paginate_count'] ?? 10;
        $search = $validated['search'] ?? null;
        $category = $validated['category'] ?? null;
        $color = $validated['color'] ?? null;

        try {
            // Build the query
            $query = Tile::with(['categories', 'colors']);

            // Apply search filter
            if ($search) {
                $query->where('name', 'like', $search . '%');
            }

            // Apply category filter
            if ($category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('name', $category);
                });
            }

            // Apply color filter
            if ($color) {
                $query->whereHas('colors', function ($q) use ($color) {
                    $q->where('name', $color);
                });
            }

            // Paginate the result
            $tiles = $query->paginate($paginate_count);

            // Check if any data was returned
            if ($tiles->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiles found',
                    'data' => [],
                ], 404);
            }

            // Return with pagination meta
            return response()->json([
                'success' => true,
                'message' => 'Tiles retrieved successfully',
                'data' => $tiles,
                'current_page' => $tiles->currentPage(),
                'total_pages' => $tiles->lastPage(),
                'per_page' => $tiles->perPage(),
                'total' => $tiles->total(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tiles.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Store a newly created tile in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grid_category' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'category_id' => 'required|array',
            'category_id.*' => 'exists:categories,id',
            'color_id' => 'nullable|array',
            'color_id.*' => 'exists:colors,id',
        ]);

        try {
            // Handle image upload if present
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = HelperMethods::uploadImage($request->file('image'));
            }

            // Create a new tile
            $tile = Tile::create([
                'name' => $validated['name'],
                'grid_category' => $validated['grid_category'],
                'description' => $validated['description'],
                'image' => $imagePath,
            ]);

            // Sync relationships
            $tile->categories()->sync($validated['category_id']);
            $tile->colors()->sync($validated['color_id'] ?? []);

            return $this->responseSuccess(
                $tile->load(['categories', 'colors']),
                'Tile created successfully',
                201
            );
        } catch (\Exception $e) {
            Log::error('Error creating tile: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified tile.
     */
    // public function show(Tile $tile)
    // {
    //     try {
    //         // Load categories relationship
    //         $tile->load('categories');

    //         return $this->responseSuccess($tile, 'Tile retrieved successfully');
    //     } catch (\Exception $e) {
    //         Log::error('Error retrieving tile: ' . $e->getMessage(), [
    //             'tile_id' => $tile->id,
    //             'error' => $e->getTraceAsString(),
    //         ]);
    //         return $this->responseError('Failed to retrieve tile', $e->getMessage(), 500);
    //     }
    // }

    public function show(Tile $tile)
    {
        try {
            $tile->load('categories');

            if ($tile->svg_path && file_exists(public_path('uploads/' . $tile->image))) {
                $svgContent = file_get_contents(public_path('uploads/' . $tile->image));
                $tile->svg_inline = $this->extractSvgPath($svgContent);
            } else {
                $tile->svg_inline = null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Tile retrieved successfully',
                'data' => $tile,
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving tile: ' . $e->getMessage(), [
                'tile_id' => $tile->id ?? null,
                'error' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function extractSvgPath($filename)
    {
        $path = storage_path("public/uploads/{$filename}");
        $svgContent = file_get_contents($path);

        $svg = simplexml_load_string($svgContent);
        $svg->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');

        $paths = $svg->xpath('//svg:path');

        $dValues = [];
        foreach ($paths as $path) {
            $dValues[] = (string)$path['d'];
        }

        return response()->json(['paths' => $dValues]);
    }



    /**
     * Update the specified tile in storage.
     */
    public function update(Request $request, Tile $tile)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grid_category' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'status' => 'nullable|string|max:255',
            'category_id' => 'required|array',
            'category_id.*' => 'exists:categories,id',
            'color_id' => 'nullable|array', // Added color_id validation
            'color_id.*' => 'exists:colors,id', // Ensure color_id exists in colors table
        ]);

        try {
            // Handle image update using helper
            $imagePath = HelperMethods::updateImage($request, $tile);

            // Prepare update data
            $updateData = [
                'name' => $validated['name'],
                'grid_category' => $validated['grid_category'],
                'description' => $validated['description'],
                'image' => $imagePath,
            ];

            // Only add status if it's present in the request
            if ($request->filled('status')) {
                $updateData['status'] = $validated['status'];
            }

            // Update tile
            $tile->update($updateData);

            // Sync categories
            $tile->categories()->sync($validated['category_id']);

            // Sync colors (use empty array if color_id is not provided)
            $tile->colors()->sync($validated['color_id'] ?? []);

            return $this->responseSuccess($tile->load(['categories', 'colors']), 'Tile updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating tile: ' . $e->getMessage(), [
                'tile_id' => $tile->id,
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);
            return $this->responseError('Failed to update tile', $e->getMessage(), 500);
        }
    }


    /**
     * Remove the specified tile from storage.
     */
    public function destroy(Tile $tile)
    {
        try {
            // Delete associated image file
            if ($tile->image && file_exists(public_path($tile->image))) {
                unlink(public_path($tile->image));
            }

            // Detach categories
            $tile->categories()->detach();

            // Delete the tile
            $tile->delete();
            Log::info('Tile deleted', ['tile_id' => $tile->id]);

            return $this->responseSuccess(null, 'Tile deleted successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error deleting tile: ' . $e->getMessage(), [
                'tile_id' => $tile->id,
                'error' => $e->getTraceAsString(),
            ]);
            return $this->responseError('Failed to delete tile', $e->getMessage(), 500);
        }
    }
    public function statusUpdate(Request $request, $id)
    {
        // dd($request);
        // Validate the incoming status
        $request->validate([
            'status' => 'required|string' // Adjust allowed values as needed
        ]);

        // Find the category by ID
        $tile = Tile::findOrFail($id);

        // Update the status
        $tile->status = $request->input('status');
        $tile->save();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Category status updated successfully',
            'tile' => $tile
        ], 200);
    }
}
