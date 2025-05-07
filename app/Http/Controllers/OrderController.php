<?php
namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'paginate_count' => 'nullable|integer|min:1',
        ]);
        $paginate_count = $validated['paginate_count'] ?? 10;

        return response()->json(Order::paginate($paginate_count));
    }

    public function store(StoreOrderRequest $request)
    {
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

}
