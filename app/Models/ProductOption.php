<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $fillable = [
        'type',
        'name',
        'price',
        'parent_type',
        'parent_id',
        'active',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function children()
    {
        return $this->hasMany(ProductOption::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(ProductOption::class, 'parent_id');
    }

    // Relacionamento muitos-para-muitos (múltiplos pais)
    public function parents()
    {
        return $this->belongsToMany(
            ProductOption::class,
            'product_option_relations',
            'option_id',
            'parent_id'
        )->withTimestamps();
    }

    // Relacionamento muitos-para-muitos (múltiplos filhos)
    public function relatedChildren()
    {
        return $this->belongsToMany(
            ProductOption::class,
            'product_option_relations',
            'parent_id',
            'option_id'
        )->withTimestamps();
    }
}
