<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /*
     * A helper method to quickly retrieve a wallet by uuid.
     */
    public static function uuid(string $uuid): self
    {
        return static::where('uuid', $uuid)->first();
    }

    /**
     * belongsTo Relationship between user and wallet
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
