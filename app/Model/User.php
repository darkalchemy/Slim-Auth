<?php

declare(strict_types=1);

namespace App\Model;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string email
 * @property string username
 * @property string password
 * @property string permissions
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
    public function email(): HasMany
    {
        return $this->hasMany(Email::class);
    }
}
