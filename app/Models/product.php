<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    //asignacion masiva
    protected $fillable = [
        'name',
        'price',
        'category_id',
        'supplier_id'
    ];

    // Listas blancas para relaciones, filtros y orden.
    protected $allowIncluded = ['category', 'supplier'];
    protected $allowFilter   = ['id', 'name', 'price', 'category_id', 'supplier_id'];
    protected $allowSort     = ['id', 'name', 'price', 'category_id', 'supplier_id'];

    //relaciones
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Scope: incluir relaciones dinámicamente (?include=category,supplier)
    public function scopeIncluded(Builder $query)
    {
        $includes = request('include');
        if (!$includes || empty($this->allowIncluded)) {
            return $query;
        }

        $relations = array_filter(explode(',', $includes), function ($relation) {
            return in_array($relation, $this->allowIncluded);
        });

        return $query->with($relations);
    }

   
    public function scopeFilter(Builder $query)
    {
        $filters = request('filter', []); // formato tipo JSON:API
        $directFilters = request()->except(['filter', 'include', 'sort', 'perPage']); // formato directo

        // Unimos ambos tipos de filtros
        $filters = array_merge($filters, $directFilters);

        if (empty($filters) || empty($this->allowFilter)) {
            return $query;
        }

        $allowFilter = collect($this->allowFilter);

        // Rango de precios
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Filtros normales
        foreach ($filters as $field => $value) {
            if ($allowFilter->contains($field) && $value !== null && $value !== '') {
                if (is_numeric($value)) {
                    $query->where($field, $value);
                } else {
                    $query->where($field, 'like', '%' . $value . '%');
                }
            }
        }

        return $query;
    }

    /**
     * Scope: ordenar dinámicamente (?sort=-price,name)
     */
    public function scopeSort(Builder $query)
    {
        $sortFields = request('sort');
        if (!$sortFields || empty($this->allowSort)) {
            return $query;
        }

        $sortArray = explode(',', $sortFields);
        $allowSort = collect($this->allowSort);

        foreach ($sortArray as $sortField) {
            $direction = 'asc';
            if (substr($sortField, 0, 1) === '-') {
                $direction = 'desc';
                $sortField = substr($sortField, 1);
            }

            if ($allowSort->contains($sortField)) {
                $query->orderBy($sortField, $direction);
            }
        }

        return $query;
    }

    /**
     * Scope: paginar o traer todos (?perPage=10)
     */
    public function scopeGetOrPaginate(Builder $query)
    {
        $perPage = intval(request('perPage', 0));
        return $perPage > 0
            ? $query->paginate($perPage)
            : $query->get();
    }
}
