<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @var string[]
     */
    protected $casts = [
        'properties' => 'array'
    ];

    /**
     * @param $details
     * @return mixed
     */
    public static function saveLog($details): mixed
    {
        return static::create($details);
    }
}
