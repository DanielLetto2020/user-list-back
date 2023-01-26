<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserPayment
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserPayment newModelQuery()
 * @method static Builder|UserPayment newQuery()
 * @method static Builder|UserPayment query()
 * @method static Builder|UserPayment whereAmount($value)
 * @method static Builder|UserPayment whereCreatedAt($value)
 * @method static Builder|UserPayment whereId($value)
 * @method static Builder|UserPayment whereStatus($value)
 * @method static Builder|UserPayment whereUpdatedAt($value)
 * @method static Builder|UserPayment whereUserId($value)
 * @mixin \Eloquent
 */
class UserPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
    ];
}
