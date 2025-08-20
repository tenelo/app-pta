<?php

namespace App\Models;

use App\Models\FichierJoint;
use App\Models\Activite;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisationTrimestrielle extends Model
{
    use HasFactory;
    protected $table = 'realisations_trimestrielles';
    protected $fillable = [
        'activite_id',
        'utilisateur_id',
        'annee',
        'trimestre',
        'realisation_quantitative',
        'taux_realisation',
        'budget_execute',
        'taux_execution_budgetaire',
        'resultats_obtenus',
        'difficultes_rencontrees',
        'mesures_correctives',
        'recommandations',
        'statut',
        'commentaires_validation',
        'validateur_id',
        'date_validation',
        'date_saisie'
    ];

    protected $casts = [
        'taux_realisation' => 'decimal:2',
        'budget_execute' => 'decimal:2',
        'taux_execution_budgetaire' => 'decimal:2',
        'date_validation' => 'datetime',
        'date_saisie' => 'datetime',
    ];

    public function activite()
    {
        return $this->belongsTo(Activite::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function validateur()
    {
        return $this->belongsTo(Utilisateur::class, 'validateur_id');
    }

    public function fichiersJoints()
    {
        return $this->morphMany(FichierJoint::class, 'attachable');
    }

    // Calculer automatiquement le taux d'exécution budgétaire
    public function calculerTauxExecutionBudgetaire()
    {
        if ($this->activite && $this->activite->cout_total_2023 > 0) {
            $budgetTrimestre = $this->activite->cout_total_2023 / 4;
            $this->taux_execution_budgetaire = ($this->budget_execute / $budgetTrimestre) * 100;
        }
        return $this->taux_execution_budgetaire;
    }

    // Méthodes pour obtenir les informations sur l'objectif du trimestre
    public function getObjectifTrimestreAttribute()
    {
        $activite = $this->activite;
        if (!$activite) return null;

        switch ($this->trimestre) {
            case 1:
                return $activite->objectif_trim1;
            case 2:
                return $activite->objectif_trim2;
            case 3:
                return $activite->objectif_trim3;
            case 4:
                return $activite->objectif_trim4;
            default:
                return null;
        }
    }

    // Scopes
    public function scopeParAnnee($query, $annee)
    {
        return $query->where('annee', $annee);
    }

    public function scopeParTrimestre($query, $trimestre)
    {
        return $query->where('trimestre', $trimestre);
    }

    public function scopeParStructure($query, $structureId)
    {
        return $query->whereHas('utilisateur', function ($q) use ($structureId) {
            $q->where('structure_id', $structureId);
        });
    }

    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
    }
}
