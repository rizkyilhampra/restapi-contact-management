<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    public $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
    ];

    public $hidden = [
        'user_id',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'user_id', 'id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'contact_id', 'id');
    }
}
