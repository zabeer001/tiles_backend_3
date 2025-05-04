<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['store', 'update', 'destroy', 'statusUpdate']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'paginate_count' => 'nullable|integer|min:1',
            'search' => 'nullable|string|max:255',
        ]);

        $search = $validated['search'] ?? null;
        $paginate_count = $validated['paginate_count'] ?? 10;

        try {
            $query = DB::table('categories')
                ->select('categories.*')
                ->selectRaw('(SELECT COUNT(*) FROM category_tiles WHERE category_tiles.category_id = categories.id) as tiles_count');

            if ($search) {
                $query->where('name', 'like', $search . '%');
            }

            $categories = $query->paginate($paginate_count);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'current_page' => $categories->currentPage(),
                'total_pages' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch categories.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            // Get the authenticated user (optional, if you need to associate the category with the user)


            $category = Category::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data' => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // return 'hello';
        return response()->json($category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:255',
        ]);

        $category->update($request->all());

        return response()->json($category);
    }


    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Category $category)
    // {
    //     try {
    //         $category->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Category deleted successfully'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to delete category',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function destroy(Category $category)
    {
        try {
            // Attempt to delete the category
            $category->delete();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle case where category is not found
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // public function statusUpdate(Request $request, $id)
    // {
    //     // dd($request);
    //     // Validate the incoming status
    //     $request->validate([
    //         'status' => 'required|string' // Adjust allowed values as needed
    //     ]);

    //     // Find the category by ID
    //     $category = Category::findOrFail($id);

    //     // Update the status
    //     $category->status = $request->input('status');
    //     $category->save();

    //     // Return a success response
    //     return response()->json([
    //         'success'=>true,
    //         'message' => 'Category status updated successfully',
    //         'category' => $category
    //     ], 200);
    // }

    public function statusUpdate(Request $request, $id)
    {
        try {
            // Validate the incoming status
            $request->validate([
                'status' => 'required|string' // You can restrict values further if needed
            ]);

            // Find the category by ID
            $category = Category::findOrFail($id);

            // Update the status
            $category->status = $request->input('status');
            $category->save();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Category status updated successfully',
                'category' => $category
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation error response
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return model not found error
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            // Return general error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating category status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
