<?php

namespace App\Http\Controllers;

use App\Models\RealisationTrimestrielle;
use App\Models\Activite;
use App\Models\FichierJoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RealisationTrimestrielleController extends Controller
{
    public function index(Request $request)
    {
        $query = RealisationTrimestrielle::with(['activite.action.produit.effet', 'utilisateur.structure'])
            ->orderBy('annee', 'desc')
            ->orderBy('trimestre', 'desc');

        // Filtres
        if ($request->filled('annee')) {
            $query->parAnnee($request->annee);
        }

        if ($request->filled('trimestre')) {
            $query->parTrimestre($request->trimestre);
        }

        if ($request->filled('structure_id')) {
            $query->parStructure($request->structure_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Si l'utilisateur n'est pas admin, filtrer par sa structure
        if (!auth()->user()->isAdmin()) {
            $query->parStructure(auth()->user()->structure_id);
        }

        $realisations = $query->paginate(20);

        return view('realisations.index', compact('realisations'));
    }

    public function create(Request $request)
    {
        $activiteId = $request->get('activite_id');
        $trimestre = $request->get('trimestre', ceil(date('n') / 3));
        $annee = $request->get('annee', date('Y'));

        $activites = Activite::with(['action.produit.effet'])
            ->where('statut', 'valide')
            ->parAnnee($annee);

        // Si l'utilisateur n'est pas admin, filtrer par sa structure
        if (!auth()->user()->isAdmin()) {
            $activites->parStructure(auth()->user()->structure_id);
        }

        $activites = $activites->get();

        $activiteSelectionnee = null;
        if ($activiteId) {
            $activiteSelectionnee = $activites->find($activiteId);
        }

        return view('realisations.create', compact('activites', 'activiteSelectionnee', 'trimestre', 'annee'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activite_id' => 'required|exists:activites,id',
            'annee' => 'required|integer|min:2020|max:' . (date('Y') + 5),
            'trimestre' => 'required|integer|min:1|max:4',
            'realisation_quantitative' => 'nullable|string|max:500',
            'taux_realisation' => 'nullable|numeric|min:0|max:100',
            'budget_execute' => 'nullable|numeric|min:0',
            'resultats_obtenus' => 'nullable|string',
            'difficultes_rencontrees' => 'nullable|string',
            'mesures_correctives' => 'nullable|string',
            'recommandations' => 'nullable|string',
            'fichiers.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'
        ]);

        // Vérifier que la réalisation n'existe pas déjà
        $existante = RealisationTrimestrielle::where([
            'activite_id' => $request->activite_id,
            'annee' => $request->annee,
            'trimestre' => $request->trimestre
        ])->first();

        if ($existante) {
            return redirect()->back()
                ->withErrors(['activite_id' => 'Une réalisation existe déjà pour cette activité et ce trimestre.'])
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $realisation = new RealisationTrimestrielle($request->all());
        $realisation->utilisateur_id = auth()->id();
        $realisation->date_saisie = now();

        // Calculer le taux d'exécution budgétaire
        $realisation->calculerTauxExecutionBudgetaire();

        $realisation->save();

        // Gestion des fichiers joints
        if ($request->hasFile('fichiers')) {
            $this->gererFichiersJoints($request->file('fichiers'), $realisation);
        }

        return redirect()->route('realisations.show', $realisation)
            ->with('success', 'La réalisation trimestrielle a été enregistrée avec succès.');
    }

    public function show(RealisationTrimestrielle $realisation)
    {
        $realisation->load([
            'activite.action.produit.effet',
            'utilisateur.structure',
            'validateur',
            'fichiersJoints.utilisateur'
        ]);

        // Vérifier les droits d'accès
        if (
            !auth()->user()->isAdmin() &&
            $realisation->utilisateur->structure_id !== auth()->user()->structure_id
        ) {
            abort(403, 'Accès non autorisé');
        }

        return view('realisations.show', compact('realisation'));
    }

    // ... autres méthodes similaires (edit, update, destroy, valider)

    private function gererFichiersJoints($fichiers, $realisation)
    {
        foreach ($fichiers as $fichier) {
            $nomFichier = Str::uuid() . '.' . $fichier->getClientOriginalExtension();
            $cheminFichier = $fichier->storeAs('ptba/realisations', $nomFichier, 'public');

            FichierJoint::create([
                'attachable_type' => RealisationTrimestrielle::class,
                'attachable_id' => $realisation->id,
                'nom_fichier' => $nomFichier,
                'nom_original' => $fichier->getClientOriginalName(),
                'type_mime' => $fichier->getMimeType(),
                'taille' => $fichier->getSize(),
                'chemin_fichier' => $cheminFichier,
                'utilisateur_id' => auth()->id()
            ]);
        }
    }
}
