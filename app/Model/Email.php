<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $subject
 * @property int    $user_id
 * @property string $body
 */
class Email extends Model
{
    protected $fillable = [
        'subject',
        'user_id',
        'body',
    ];

    /**
     * @var string
     */
    protected $table = 'email';

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
