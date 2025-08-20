<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Effet extends Model
{
    use HasFactory;

    protected $fillable = [
        'annee', 'numero_effet', 'libelle_effet', 'description',
        'structure_id', 'budget_total_prevu', 'budget_total_execute', 'statut'
    ];

    protected $casts = [
        'budget_total_prevu' => 'decimal:2',
        'budget_total_execute' => 'decimal:2',
    ];

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    public function getActivitesAttribute()
    {
        return Activite::whereIn('action_id', 
            Action::whereIn('produit_id', $this->produits->pluck('id'))
                ->pluck('id')
        )->get();
    }

    public function calculerBudgetTotal()
    {
        $this->budget_total_prevu = $this->produits->sum('budget_total_prevu');
        $this->budget_total_execute = $this->produits->sum('budget_total_execute');
        $this->save();
    }

    public function scopeParAnnee($query, $annee)
    {
        return $query->where('annee', $annee);
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }
}