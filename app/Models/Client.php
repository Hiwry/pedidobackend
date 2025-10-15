<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone_primary',
        'phone_secondary',
        'email',
        'cpf_cnpj',
        'address',
        'city',
        'state',
        'zip_code',
        'category',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
