<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaisesRegiao extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paises_regioes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pais_id',
        'nome',
        'sigla',
    ];

    /**
     * Get the country that owns the region.
     */
    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    /**
     * Get the cities for the region.
     */
    public function cidades(): HasMany
    {
        return $this->hasMany(PaisRegioCidade::class, 'regiao_id');
    }
}
