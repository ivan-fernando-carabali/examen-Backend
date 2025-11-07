<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'supplier_id'
    ];

    // Relaciones que se pueden incluir dinámicamente
    protected $allowIncluded = ['category', 'supplier'];

    // Campos que se pueden filtrar
    protected $allowFilter = ['name', 'category_id', 'supplier_id'];

    // Campos que se pueden ordenar
    protected $allowSort = ['id', 'name', 'price', 'created_at', 'updated_at'];

    /**
     * Relación con categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con proveedor
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Scope para incluir relaciones dinámicamente.
     * Ejemplo: ?included=category,supplier
     */
    public function scopeIncluded(Builder $query)
    {
        if (empty($this->allowIncluded) || empty(request('included'))) {
            return;
        }

        $relations = explode(',', request('included'));
        $allowed = collect($this->allowIncluded);

        foreach ($relations as $key => $relation) {
            if (!$allowed->contains($relation)) {
                unset($relations[$key]);
            }
        }

        $query->with($relations);
    }

    /**
     * Scope para filtrar dinámicamente.
     * Ejemplo: ?filter[name]=laptop&filter[min_price]=10000&filter[max_price]=50000
     */
    public function scopeFilter(Builder $query)
    {
        if (empty(request('filter'))) {
            return;
        }

        $filters = request('filter');

        foreach ($filters as $field => $value) {

            // Filtrado por rango de precio
            if ($field === 'min_price') {
                $query->where('price', '>=', $value);
                continue;
            }

            if ($field === 'max_price') {
                $query->where('price', '<=', $value);
                continue;
            }

            // Filtros normales para columnas permitidas
            if (in_array($field, $this->allowFilter)) {
                if (is_numeric($value)) {
                    $query->where($field, $value);
                } else {
                    $query->where($field, 'LIKE', '%' . $value . '%');
                }
            }
        }
    }

    /**
     * Scope para ordenar dinámicamente.
     * Ejemplo: ?sort=-price,name
     */
    public function scopeSort(Builder $query)
    {
        if (empty(request('sort'))) {
            return;
        }

        $sortFields = explode(',', request('sort'));

        foreach ($sortFields as $sortField) {
            $direction = 'asc';

            if (substr($sortField, 0, 1) === '-') {
                $direction = 'desc';
                $sortField = substr($sortField, 1);
            }

            if (in_array($sortField, $this->allowSort)) {
                $query->orderBy($sortField, $direction);
            }
        }
    }

    /**
     * Scope para decidir entre paginar o traer todo.
     * Ejemplo: ?perPage=10 o sin perPage (trae todo)
     */
    public function scopeGetOrPaginate(Builder $query)
    {
        $perPage = intval(request('perPage') ?? 15);

        if ($perPage > 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }
}
