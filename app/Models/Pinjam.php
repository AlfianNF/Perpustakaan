<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pinjam extends Model
{
    /** @use HasFactory<\Database\Factories\PinjamFactory> */
    use HasFactory;

    protected $fillable = [
        'id_buku','id_user','tgl_pinjam','tgl_kembali'
    ];

    public function getAllowedFilters()
    {
        return ['id_buku','id_user'];
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereRaw("CAST(id_buku AS TEXT) LIKE ?", ["%{$search}%"])
            ->orWhere('id_user', 'LIKE', "%{$search}%");
        });
    }

    public function scopeIsFilter(Builder $query, array $filters = [])
    {
        $allowedFilters = $this->getAllowedFilters();

        foreach ($filters as $key => $value) {
            if (in_array($key, $allowedFilters) && !empty($value)) {
                if ($key === 'id_buku') {
                    $query->whereRaw("CAST(id_buku AS TEXT) ILIKE ?", ["%{$value}%"]);
                } else {
                    $query->where($key, 'ILIKE', "%{$value}%");
                }
            }
        }

        return $query->orderBy('created_at', 'DESC');
    }



    /**
     * Get the userPinjam that owns the Peminjaman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userPinjam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /**
     * Get the buku that owns the Peminjaman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id');
    }

    /**
     * Get the kembali associated with the Pinjam
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kembali(): HasOne
    {
        return $this->hasOne(Kembali::class, 'id_pinjam', 'id');
    }   

    public function getRelations()
    {
        return [
            'userPinjam' => function ($query) {
                $query->select('id', 'name'); 
            },
            'buku' => function ($query) {
                $query->select('id','isbn', 'author','category','publish_date')
                    ->with([
                        'category:id,name' 
                    ]); 
            }
        ];
    }
}
