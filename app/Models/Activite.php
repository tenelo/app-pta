<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_id', 'utilisateur_id', 'annee', 'numero_activite', 'libelle_activite',
        'reforme_identifiee', 'reforme_2023', 'reforme_cle', 'realisation_majeure',
        'indicateur', 'realisation_2022', 'objectif_trim1', 'objectif_trim2',
        'objectif_trim3', 'objectif_trim4', 'objectif_annuel', 'zones_execution',
        'structure_responsable', 'budget_alloue_2022', 'cout_investissement_2023',
        'cout_biens_services_2023', 'cout_transfert_2023', 'cout_personnel_2023',
        'cout_total_2023', 'cout_prevu_2024', 'cout_prevu_2025', 'reference_pnd',
        'programme_dppd', 'statut', 'commentaires', 'difficultes', 'recommandations',
        'validateur_id', 'date_validation', 'date_saisie'
    ];

    protected $casts = [
        'reforme_identifiee' => 'boolean',
        'reforme_2023' => 'boolean',
        'reforme_cle' => 'boolean',
        'realisation_majeure' => 'boolean',
        'budget_alloue_2022' => 'decimal:2',
        'cout_investissement_2023' => 'decimal:2',
        'cout_biens_services_2023' => 'decimal:2',
        'cout_transfert_2023' => 'decimal:2',
        'cout_personnel_2023' => 'decimal:2',
        'cout_total_2023' => 'decimal:2',
        'cout_prevu_2024' => 'decimal:2',
        'cout_prevu_2025' => 'decimal:2',
        'date_validation' => 'datetime',
        'date_saisie' => 'date',
    ];

    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function validateur()
    {
        return $this->belongsTo(Utilisateur::class, 'validateur_id');
    }

    public function realisationsTrimestrielles()
    {
        return $this->hasMany(RealisationTrimestrielle::class);
    }

    public function fichiersJoints()
    {
        return $this->morphMany(FichierJoint::class, 'attachable');
    }

    // Calculer automatiquement le coût total
    public function calculerCoutTotal()
    {
        $this->cout_total_2023 = ($this->cout_investissement_2023 ?? 0) +
                                 ($this->cout_biens_services_2023 ?? 0) +
                                 ($this->cout_transfert_2023 ?? 0) +
                                 ($this->cout_personnel_2023 ?? 0);
        return $this->cout_total_2023;
    }

    // Calculer le taux d'exécution global
    public function getTauxExecutionAttribute()
    {
        if ($this->cout_total_2023 > 0) {
            $totalExecute = $this->realisationsTrimestrielles->sum('budget_execute');
            return round(($totalExecute / $this->cout_total_2023) * 100, 2);
        }
        return 0;
    }

    // Calculer le taux de réalisation global
    public function getTauxRealisationAttribute()
    {
        $realisations = $this->realisationsTrimestrielles;
        if ($realisations->count() > 0) {
            return round($realisations->avg('taux_realisation'), 2);
        }
        return 0;
    }

    // Accessor pour les types de réformes
    public function getTypesReformesAttribute()
    {
        $types = [];
        if ($this->reforme_identifiee) $types[] = 'R';
        if ($this->reforme_2023) $types[] = 'R23';
        if ($this->reforme_cle) $types[] = 'RC';
        if ($this->realisation_majeure) $types[] = 'RM';
        return $types;
    }

    // Scopes
    public function scopeParAnnee($query, $annee)
    {
        return $query->where('annee', $annee);
    }

    public function scopeParStructure($query, $structureId)
    {
        return $query->whereHas('utilisateur', function($q) use ($structureId) {
            $q->where('structure_id', $structureId);
        });
    }

    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
    }

    public function scopeReformesIdentifiees($query)
    {
        return $query->where('reforme_identifiee', true);
    }

    public function scopeRealisationsMajeures($query)
    {
        return $query->where('realisation_majeure', true);
    }
}