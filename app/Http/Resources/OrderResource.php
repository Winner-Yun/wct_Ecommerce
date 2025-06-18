<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'items' => $this->items->map(function ($item) {
                return [
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->price,
                    ],
                    'quantity' => $item->quantity,
                    'total_price' => $item->price * $item->quantity,
                ];
            }),
        ];
    }
}
