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
        $order = Order::create($request->validated());
        return response()->json($order, 201);
    }

    public function show(Order $order)
    {
        return response()->json($order);
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());
        return response()->json($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully.']);
    }
}
