<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kembali extends Model
{
    /** @use HasFactory<\Database\Factories\KembaliFactory> */
    use HasFactory;
    protected $fillable=[
        'id_buku',
        'id_user',
        'id_pinjam',
        'tgl_kembali',
        'denda'
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
     * Get the peminjaman that owns the Kembali
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjam::class, 'id_pinjam','id');
    }

    public function getRelations()
    {
        return [
            'peminjaman' => function ($query) {
                $query->select('id', 'id_buku', 'id_user','tgl_kembali')
                    ->with([
                        'userPinjam:id,name',
                        'buku:id,title,category'
                    ]); 
            }
        ];
    }
}
