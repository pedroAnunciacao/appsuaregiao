<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaisRegiaoCidade extends Model
{
    protected $table = 'pais_regiao_cidades';
    public $timestamps = false;
    protected $fillable = [
        'id', 'pais_id', 'regiao_id', 'nome', 'uf', 'ativo', 'created_at', 'updated_at'
    ];
}
