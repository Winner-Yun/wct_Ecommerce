<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
         {
        $user = $request->user();

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $productsToUpdate = [];

            // First, validate all products and calculate total price
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if (!$product) {
                    DB::rollBack();
                    return response()->json(['message' => 'Product not found'], 404);
                }

                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json(['message' => "Insufficient stock for product {$product->name}"], 400);
                }

                $totalPrice += $product->price * $item['quantity'];
                $productsToUpdate[] = ['product' => $product, 'quantity' => $item['quantity']];
            }

            // Create the order with total price
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_price' => $totalPrice,
            ]);

            // Create order items and decrement stock
            foreach ($productsToUpdate as $entry) {
                $product = $entry['product'];
                $quantity = $entry['quantity'];

                $product->decrement('stock', $quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => new OrderResource($order->load('items.product')),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
        {
            $user = auth()->user();

            $order = Order::with('items')->where('id', $id)->where('user_id', $user->id)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found or unauthorized'], 404);
            }

            DB::beginTransaction();

            try {
        
                foreach ($order->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }

                // Delete order items
                $order->items()->delete();


                $order->delete();

                DB::commit();

                return response()->json(['message' => 'Order deleted successfully']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Failed to delete order',
                    'error' => $e->getMessage()
                ], 500);
            }
        }


}
