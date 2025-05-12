<?php

namespace App\Http\Controllers;

use App\Helpers\HelperMethods;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ColorController extends Controller
{
    public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['store', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'paginate_count' => 'nullable|integer|min:1',
            'query' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        $paginate_count = $validated['paginate_count'] ?? 10;
        $query = $validated['query'] ?? null;
        $status = $validated['status'] ?? null;

        try {
           

            $colorQuery = Color::query();

            if ($query) {
                $colorQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', $query . '%')
                        ->orWhere('code', 'like', $query . '%');
                });
            }
            
            if ($status) {
                $colorQuery->where('status', 'like', $status . '%');
            }

            $colors = $colorQuery->paginate($paginate_count);

            return response()->json([
                'data' => $colors,
                'current_page' => $colors->currentPage(),
                'total_pages' => $colors->lastPage(),
                'per_page' => $colors->perPage(),
                'total' => $colors->total(),
                'message' => 'color retrieved successfully',
                'success' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return $this->responseError('Something went wrong, please try again later.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // return 'zabeer';
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255', // Code is optional
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        // return $request;


        try {
            // Handle image upload using the helper function
            $imagePath = HelperMethods::uploadImage($request->file('image'));

            // Create a new color instance
            $color = new Color();
            $color->name = $validated['name'];
            $color->code = $request->code;
            $color->image = $imagePath;
            $color->save();

            // Sync categories to the color (add or remove as necessary)


            // Return success response using colorsResource
            return $this->responseSuccess($color, 'color created successfully', 201);
        } catch (\Exception $e) {
            // Log the error with additional context
            Log::error('Error creating color: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            // Return error response
            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Color $color)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255', // Color code is optional
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image is optional
            'status' => 'nullable|string|max:255', // Status is optional
        ]);
    
        try {
            // Find the existing color by ID
            $color = Color::findOrFail($id);
    
            // Prepare update data
            $updateData = [
                'name' => $validated['name'],
            ];
    
            // Only update code if provided, and set image to null
            if ($request->filled('code')) {
                $updateData['code'] = $validated['code'];
                $updateData['image'] = null;
            }
    
            // Only update status if provided
            if ($request->filled('status')) {
                $updateData['status'] = $validated['status'];
            }
    
            // Handle image update (if provided), and set code to null
            $imagePath = HelperMethods::updateImage($request, $color);
            if ($imagePath) {
                $updateData['image'] = $imagePath;
                $updateData['code'] = null;
            }
    
            // Update the color
            $color->update($updateData);
    
            // Return success response
            return $this->responseSuccess($color, 'Color updated successfully', 200);
        } catch (\Exception $e) {
            // Log the error with additional context
            Log::error('Error updating color: ' . $e->getMessage(), [
                'color_id' => $id,
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);
    
            // Return error response
            return $this->responseError('Failed to update color', $e->getMessage(), 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        try {
            // Delete associated image file if it exists
            if ($color->image && file_exists(public_path($color->image))) {
                unlink(public_path($color->image));
            }

            // Delete the color from the database
            $color->delete();
            Log::info('Color deleted', ['color_id' => $color->id]);

            // Return success response
            return $this->responseSuccess(null, 'Color deleted successfully', 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error deleting color: ' . $e->getMessage(), [
                'color_id' => $color->id,
                'error' => $e->getTraceAsString(),
            ]);

            // Return error response
            return $this->responseError('Failed to delete color', $e->getMessage(), 500);
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
        $color = Color::findOrFail($id);
    
        // Update the status
        $color->status = $request->input('status');
        $color->save();
    
        // Return a success response
        return response()->json([
            'message' => 'Category status updated successfully',
            'color' => $color
        ], 200);
    }
    // arr = [1,5,7]
    //for(i=1;i<arr.len(arr);i++){
    //  api delete }
}
