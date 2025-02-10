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

    protected static $is_add = ['name', 'description'];
    protected static $is_edit = ['name', 'description']; 
    protected static $is_delete = ['name', 'description'];
    protected static $is_search = ['name'];


    public static function getAllowedFields($type)
    {
        return match ($type) {
            'add' => self::$is_add,
            'edit' => self::$is_edit,
            'delete' => self::$is_delete,
            'search' => self::$is_search,
            default => [],
        };
    }

    // public function scopeSearch(Builder $query, $search)
    // {
    //     return $query->where(function ($q) use ($search) {
    //         $q->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$search}%"]);
    //     });
    // }


    // public function scopeIsFilter(Builder $query, array $filters = [])
    // {
    //     $allowedFilters = $this->getAllowedFilters(); // Ambil filter yang diperbolehkan dari method

    //     foreach ($filters as $key => $value) {
    //         if (in_array($key, $allowedFilters) && !empty($value)) {
    //             $query->where($key, 'ILIKE', "%{$value}%");
    //         }

    //         return $query->orderBy('created_at', 'DESC');
    //     }
    // }

}
