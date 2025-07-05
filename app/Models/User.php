<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'utype',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /** Role helpers **/

    public function isAdmin(): bool
    {
        return $this->utype === 'ADM';
    }

    public function isStore(): bool
    {
        return $this->utype === 'STR';
    }

    public function isCustomer(): bool
    {
        return $this->utype === 'USR';
    }

    /** Relations **/

    // Super-admin membuat store, tapi tidak "punya" store
    // Store owner (STR) yang punya store via owner_id di tabel stores
    public function store()
    {
        return $this->hasOne(Store::class, 'owner_id');
    }

    // Jika user customer beli order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // **Addresses relation**
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // The default address (isdefault = true)
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)
            ->where('isdefault', true);
    }

    public function transactions()
    {
        return $this->hasManyThrough(
            Transaction::class,
            Order::class,
            'user_id',    // foreign key on orders
            'order_id',   // foreign key on transactions
            'id',         // local key on users
            'id'          // local key on orders
        );
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class)->withDefault();
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->profile->avatar_url;
    }
}
