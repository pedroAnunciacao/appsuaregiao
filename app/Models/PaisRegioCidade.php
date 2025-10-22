<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaisRegioCidade extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pais_regiao_cidades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'regiao_id',
        'nome',
    ];

    /**
     * Get the region that owns the city.
     */
    public function regiao(): BelongsTo
    {
        return $this->belongsTo(PaisesRegiao::class, 'regiao_id');
    }

    /**
     * Get the posts for the city.
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class, 'cidade_id');
    }
}
