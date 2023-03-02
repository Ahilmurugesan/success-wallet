<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Exceptions\CouldNotCreateWallet;
use App\Exceptions\CouldNotUpdateMoney;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * hasOne Relationship between user and wallet
     *
     * @return hasOne
     */
    public function wallet(): hasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Check wallet exists for the given user
     *
     * @param $user
     * @param  null  $type
     * @return bool
     */
    public static function checkWalletExists($user, $type=null): bool
    {
        if(!$user->wallet()->exists())
        {
            throw CouldNotUpdateMoney::walletNotFound($type ?? 'your');
        }
        return true;
    }

    /**
     * @param $user
     * @return bool
     */
    public static function checkWalletAlreadyExists($user): bool
    {
        if($user->wallet)
        {
            throw CouldNotCreateWallet::walletAlreadyExists();
        }
        return true;
    }
}
