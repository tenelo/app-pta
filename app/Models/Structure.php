<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_structure', 'code_structure', 'type_structure', 
        'description', 'responsable', 'email', 'telephone', 'statut'
    ];

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class);
    }

    public function effets()
    {
        return $this->hasMany(Effet::class);
    }

    public function scopeActives($query)
    {
        return $query->where('statut', 'actif');
    }
}