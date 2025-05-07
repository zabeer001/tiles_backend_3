<?php
namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;


class OrderController extends Controller
{

    public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['store', 'update', 'destroy','statusUpdate']);
    }


    public function index(Request $request)
    {
        // Validate query parameters
        $validated = $request->validate([
            'paginate_count' => 'nullable|integer|min:1',
            'query' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        // Get query parameters
        $paginate_count = $validated['paginate_count'] ?? 10;
        $query = $validated['query'] ?? null;
        $status = $validated['status'] ?? null;

        try {
            // Build the query
            $orderQuery = Order::query();

            // Apply search filter
            if ($query) {
                $orderQuery->where(function ($q) use ($query) {
                    $q->where('phone_number', 'like', $query . '%')
                        ->orWhere('email', 'like', $query . '%');
                });
            }


            if ($status) {
                $orderQuery->where('status', 'like', $status . '%');
            }

            // Paginate the result
            $orders = $orderQuery->paginate($paginate_count);

            // Check if any data was returned
            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No orders found',
                    'data' => [],
                ], 404);
            }

            // Return with pagination meta
            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => $orders,
                'current_page' => $orders->currentPage(),
                'total_pages' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function store(StoreOrderRequest $request)
    {
        dd($request);
        try {
            $order = Order::create($request->validated());

            return $this->responseSuccess(
                $order, // You can use $order->load([...]) if you want related models
                'Order created successfully',
                201
            );
        } catch (\Exception $e) {
            \Log::error('Error creating order: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError(
                'Something went wrong while creating the order',
                $e->getMessage(),
                500
            );
        }
    }


    public function show(Order $order)
    {
        try {
            return $this->responseSuccess($order, 'Order retrieved successfully');
        } catch (\Exception $e) {
            \Log::error('Error fetching order: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Failed to retrieve order', $e->getMessage(), 500);
        }
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        try {
            $order->update($request->validated());

            return $this->responseSuccess($order, 'Order updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating order: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Failed to update order', $e->getMessage(), 500);
        }
    }

    public function destroy(Order $order)
    {
        try {
            $order->delete();

            return $this->responseSuccess(null, 'Order deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Error deleting order: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Failed to delete order', $e->getMessage(), 500);
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
        $order = Order::findOrFail($id);

        // Update the status
        $order->status = $request->input('status');
        $order->save();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Category status updated successfully',
            'tile' => $order
        ], 200);
    }

}
