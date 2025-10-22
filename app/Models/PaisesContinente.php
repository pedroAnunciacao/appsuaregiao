<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaisesContinente extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paises_continentes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
    ];

    /**
     * Get the countries for the continent.
     */
    public function paises(): HasMany
    {
        return $this->hasMany(Pais::class, 'continente_id');
    }
}
