<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id', 'numero_action', 'libelle_action', 'description',
        'budget_total_prevu', 'budget_total_execute', 'statut'
    ];

    protected $casts = [
        'budget_total_prevu' => 'decimal:2',
        'budget_total_execute' => 'decimal:2',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function activites()
    {
        return $this->hasMany(Activite::class);
    }

    public function calculerBudgetTotal()
    {
        $this->budget_total_prevu = $this->activites->sum('cout_total_2023');
        $this->budget_total_execute = $this->activites->sum(function($activite) {
            return $activite->realisationsTrimestrielles->sum('budget_execute');
        });
        $this->save();
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }
}