<?php

declare(strict_types=1);

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class User.
 */
class User extends EloquentUser
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'username',
        'password',
        'permissions',
    ];

    /**
     * @return HasMany
     */
    public function email()
    {
        return $this->hasMany(Email::class);
    }
}
