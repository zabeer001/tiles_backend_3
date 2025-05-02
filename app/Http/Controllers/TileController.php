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


    /**
     * Display a listing of the tiles.
     */
    public function index()
    {
        try {
            // Retrieve all tiles with their categories
            $tiles = Tile::with('categories')->paginate(10);

            return response()->json([
                'data' => $tiles,
                'current_page' => $tiles->currentPage(),
                'total_pages' => $tiles->lastPage(),
                'per_page' => $tiles->perPage(),
                'total' => $tiles->total(),
                'message' => 'Tiles retrieved successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving tiles: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Failed to retrieve tiles',
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
    public function show(Tile $tile)
    {
        try {
            // Load categories relationship
            $tile->load('categories');

            return $this->responseSuccess($tile, 'Tile retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error retrieving tile: ' . $e->getMessage(), [
                'tile_id' => $tile->id,
                'error' => $e->getTraceAsString(),
            ]);
            return $this->responseError('Failed to retrieve tile', $e->getMessage(), 500);
        }
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
            'category_id' => 'required|array',
            'category_id.*' => 'exists:categories,id',
        ]);

        try {
            // Handle image update using helper
            $imagePath = HelperMethods::updateImage($request, $tile);

            // Update tile
            $tile->update([
                'name' => $validated['name'],
                'grid_category' => $validated['grid_category'],
                'description' => $validated['description'],
                'image' => $imagePath,
            ]);

            // Sync categories
            $tile->categories()->sync($validated['category_id']);

            return $this->responseSuccess($tile->load('categories'), 'Tile updated successfully');
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

    public function search(Request $request)
    {
        // Validate query parameters
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|string|exists:categories,name', // Assumes categories table has a 'name' column
            'color' => 'nullable|string|exists:colors,name', // Assumes categories table has a 'name' column
        ]);

        // Get query parameters
        $search = $validated['search'] ?? null;
        $category = $validated['category'] ?? null;
        $color = $validated['color'] ?? null;

        // Build the query
        $query = Tile::with('categories'); // Eager load categories relationship

        // Apply search filter (partial match on name)
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Apply category filter (assuming tiles belong to categories via relationship)
        if ($category) {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('name', $category); // Match category name
            });
        }

        if($color){
            $query->whereHas('colors', function ($q) use ($color) {
                $q->where('name', $color); // Match category name
            });
        }

        // Retrieve the first matching tile
        $tile = $query->first();

        // Handle case where no tile is found
        if (!$tile) {
            return response()->json([
                'success' => false,
                'message' => 'Tile not found',
                'data' => null,
            ], 404);
        }

        // Return the found tile
        return response()->json([
            'success' => true,
            'message' => 'Tile retrieved successfully',
            'data' => $tile,
        ], 200);
    }
}