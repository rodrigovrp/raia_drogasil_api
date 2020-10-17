<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => (($this->category->name) ?? '' ),
            'brand_id' => $this->brand_id,
            'brand_name' => (($this->brand->name) ?? '' ),
            'name' => $this->name,
            'description' => $this->description,
            'code' => $this->code,
            'ean' => $this->ean,
            'weight' => $this->weight,
            'weight_type' => $this->weight_type,
            'quantity' => $this->quantity,
            'quantity_type' => $this->quantity_type,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
