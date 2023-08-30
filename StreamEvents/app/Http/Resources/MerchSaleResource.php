<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'item_name' => $this->item_name,
            'amount' => $this->amount,
            'price' => (float) $this->price,
            'currency' => $this->currency
        ];
    }
}
