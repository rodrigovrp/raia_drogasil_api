<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FundNetworkResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_name' => (($this->product->name) ?? '' ),
            'network_id' => $this->network_id,
            'network_name' => (($this->network->name) ?? '' ),
            'action_type_id' => $this->action_type_id,
            'action_type_name' => (($this->action_type->name) ?? '' ),
            'year' => $this->year,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
        ];
    }
}
