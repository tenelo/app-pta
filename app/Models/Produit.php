<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'effet_id', 'numero_produit', 'libelle_produit', 'description',
        'indicateur_principal', 'realisation_2022', 'cible_2023',
        'budget_total_prevu', 'budget_total_execute', 'statut'
    ];

    protected $casts = [
        'budget_total_prevu' => 'decimal:2',
        'budget_total_execute' => 'decimal:2',
    ];

    public function effet()
    {
        return $this->belongsTo(Effet::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function getActivitesAttribute()
    {
        return Activite::whereIn('action_id', $this->actions->pluck('id'))->get();
    }

    public function calculerBudgetTotal()
    {
        $this->budget_total_prevu = $this->actions->sum('budget_total_prevu');
        $this->budget_total_execute = $this->actions->sum('budget_total_execute');
        $this->save();
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }
}