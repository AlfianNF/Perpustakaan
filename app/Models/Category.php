<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Database\Query\Builder;


class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    
    // protected $is_add = true;
    protected $fillable = ['name','description'];

    /**
     * Get all of the buku for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class, 'category');
    }

    // public function scopeIsFilter(Builder $query){
    //     return $query->orderBy('name', 'ASC');
    // }

    // public function scopeSearch(Builder $query,$search){
    //     return $query->where('name', 'LIKE', '%' . $search . '%');
    // }

    public function getAllowedFilters()
    {
        return ['name'];
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$search}%"]);
        });
    }


    public function scopeIsFilter(Builder $query, array $filters = [])
    {
        $allowedFilters = $this->getAllowedFilters(); // Ambil filter yang diperbolehkan dari method

        foreach ($filters as $key => $value) {
            if (in_array($key, $allowedFilters) && !empty($value)) {
                $query->where($key, 'ILIKE', "%{$value}%");
            }

            return $query->orderBy('created_at', 'DESC');
        }
    }

}
