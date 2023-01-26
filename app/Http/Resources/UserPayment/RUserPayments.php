<?php

namespace App\Http\Resources\UserPayment;

use App\Models\UserPayment;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RUserPayments extends JsonResource
{
    /**
     * @var UserPayment
     */
    public $resource;

    final public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'amount' => $this->resource->amount,
            'status' => $this->resource->status,
            'createdAt' => $this->resource->created_at,
            'createdAtText' => Carbon::createFromTimestamp($this->resource->created_at)->format('d.m.Y'),
        ];
    }
}
