<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\RealisationTrimestrielle;
use App\Models\Structure;
use App\Models\Utilisateur;
use App\Models\Effet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $annee = $request->get('annee', date('Y'));

        // Correction : Récupération sécurisée de l'utilisateur
        $user = Auth::user();
        $structureId = $user && $user->isAdmin() ? null : ($user ? $user->structure_id : null);

        // Statistiques générales
        $stats = $this->getStatistiquesGenerales($annee, $structureId);

        // Données pour les graphiques
        $graphiques = $this->getDonneesGraphiques($annee, $structureId);

        // Activités récentes
        $activitesRecentes = $this->getActivitesRecentes($structureId);

        // Réalisations en attente de validation
        $realisationsEnAttente = $this->getRealisationsEnAttente($structureId);

        return view('dashboard', compact(
            'stats',
            'graphiques',
            'activitesRecentes',
            'realisationsEnAttente',
            'annee'
        ));
    }

    private function getStatistiquesGenerales($annee, $structureId = null)
    {
        $queryActivites = Activite::parAnnee($annee);
        $queryRealisations = RealisationTrimestrielle::parAnnee($annee);

        if ($structureId) {
            $queryActivites->parStructure($structureId);
            $queryRealisations->parStructure($structureId);
        }

        return [
            'total_activites' => (clone $queryActivites)->count(),
            'activites_validees' => (clone $queryActivites)->valides()->count(),
            'total_realisations' => (clone $queryRealisations)->count(),
            'realisations_validees' => (clone $queryRealisations)->valides()->count(),
            'budget_total_prevu' => (clone $queryActivites)->sum('cout_total_2023'),
            'budget_total_execute' => $queryRealisations->sum('budget_execute'),
            'reformes_identifiees' => (clone $queryActivites)->reformesIdentifiees()->count(),
            'realisations_majeures' => (clone $queryActivites)->realisationsMajeures()->count(),
        ];
    }

    private function getDonneesGraphiques($annee, $structureId = null)
    {
        // Évolution des réalisations par trimestre
        $evolutionTrimestrielle = RealisationTrimestrielle::parAnnee($annee)
            ->when($structureId, function ($q) use ($structureId) {
                $q->parStructure($structureId);
            })
            ->select('trimestre', DB::raw('AVG(taux_realisation) as taux_moyen'))
            ->groupBy('trimestre')
            ->orderBy('trimestre')
            ->get();

        // Répartition budgétaire par effet
        $repartitionBudgetaire = Effet::parAnnee($annee)
            ->when($structureId, function ($q) use ($structureId) {
                $q->where('structure_id', $structureId);
            })
            ->select('numero_effet', 'libelle_effet', 'budget_total_prevu')
            ->get();

        return [
            'evolution_trimestrielle' => $evolutionTrimestrielle,
            'repartition_budgetaire' => $repartitionBudgetaire,
        ];
    }

    private function getActivitesRecentes($structureId = null)
    {
        return Activite::with(['action.produit.effet', 'utilisateur'])
            ->when($structureId, function ($q) use ($structureId) {
                $q->parStructure($structureId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getRealisationsEnAttente($structureId = null)
    {
        return RealisationTrimestrielle::with(['activite.action.produit.effet', 'utilisateur'])
            ->where('statut', 'soumis')
            ->when($structureId, function ($q) use ($structureId) {
                $q->parStructure($structureId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}
