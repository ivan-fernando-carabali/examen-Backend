<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'capacity'];

    protected $casts = [
        'capacity' => 'integer',
    ];

    // Relaciones
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
