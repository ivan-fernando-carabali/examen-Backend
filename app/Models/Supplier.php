<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'address'];

    // Relaciones
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
