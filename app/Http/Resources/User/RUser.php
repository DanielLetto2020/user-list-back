<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RUser extends JsonResource
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
            'scope' => $this->resource->tokens()->first()->abilities,
        ];
    }
}
