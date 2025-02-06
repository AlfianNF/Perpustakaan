<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buku extends Model
{
    /** @use HasFactory<\Database\Factories\BukuFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'author',
        'publish_date',
        'is_pinjam',
        'category'
    ];

    // public function scopeIsFilter(Builder $query){
    //     return $query->with(['category:id,name,description'],['peminjaman:id,'])
    //             ->orderBy('created_at', 'DESC')
    //             ->where('is_pinjam',false);
    // }

    public function getAllowedFilters()
    {
        return ['title', 'isbn', 'author', 'category'];
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereRaw("LOWER(title) LIKE LOWER(?)", ["%{$search}%"])
            ->orWhere('isbn', 'LIKE', "%{$search}%")
            ->orWhere('author', 'LIKE', "%{$search}%");
        });
    }


    public function scopeIsFilter(Builder $query, array $filters = [])
    {
        $allowedFilters = $this->allowedFilters;

        foreach ($filters as $key => $value) {
            if (in_array($key, $allowedFilters) && !empty($value)) {
                if ($key === 'title') {
                    $query->whereRaw("LOWER(title) LIKE LOWER(?)", ["%{$value}%"]);
                } else {
                    $query->where($key, 'LIKE', "%{$value}%");
                }
            }
        }

        $query->orderBy('created_at', 'DESC')
            ->where('is_pinjam', false);    
    }


    /**
     * Get the category that owns the Buku
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category');
    }


    /**
     * Get all of the peminjaman for the Buku
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Pinjam::class, 'id_buku', 'id');
    }

    public function getRelations()
    {
        return [
            'category' => function ($query) {
                $query->select('id', 'name'); 
            },
            'peminjaman' => function ($query) {
                $query->select('id', 'id_buku', 'id_user')
                    ->with([
                        'userPinjam:id,name'
                    ]); 
            }
        ];
    }
}
