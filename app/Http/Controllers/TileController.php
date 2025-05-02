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
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grid_category' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'category_id' => 'required|array',
            'category_id.*' => 'exists:categories,id', // Ensure each category ID exists
        ]);

        try {
            // Handle image upload
            $imagePath = HelperMethods::uploadImage($request->file('image'));

            // Create a new tile
            $tile = Tile::create([
                'name' => $validated['name'],
                'grid_category' => $validated['grid_category'],
                'description' => $validated['description'],
                'image' => $imagePath,
            ]);

            // Sync categories
            $tile->categories()->sync($validated['category_id']);

            return $this->responseSuccess($tile->load('categories'), 'Tile created successfully', 201);
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
        return 'hi';
        $query = $request->input('q');

        $tile = Tile::with('categories')
            ->where('name', $query)
            ->first(); // Use `first()` to return a single tile, not a list

        if (!$tile) {
            return response()->json([
                'success' => false,
                'message' => 'Tile not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tile retrieved successfully',
            'data' => $tile,
        ]);
    }
}