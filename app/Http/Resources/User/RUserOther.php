<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RUserOther extends JsonResource
{
    /**
     * @var User
     */
    public $resource;

    final public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'payments' => $this->whenLoaded('payments'),
        ];
    }
}
