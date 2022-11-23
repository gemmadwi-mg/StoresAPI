<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'details'
    ];

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'brand_store');
    }

    
}
