<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pais extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paises';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'continente_id',
        'nome',
        'nome_pt',
        'sigla',
        'bacen',
    ];

    /**
     * Get the continent that owns the country.
     */
    public function continente(): BelongsTo
    {
        return $this->belongsTo(PaisesContinente::class, 'continente_id');
    }

    /**
     * Get the regions for the country.
     */
    public function regioes(): HasMany
    {
        return $this->hasMany(PaisesRegiao::class, 'pais_id');
    }
}
