<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Action;
use App\Models\Effet;
use App\Models\Produit;
use App\Models\Structure;
use App\Models\FichierJoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ActiviteController extends Controller
{
    public function index(Request $request)
    {
        $query = Activite::with(['action.produit.effet', 'utilisateur.structure'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('annee')) {
            $query->parAnnee($request->annee);
        }

        if ($request->filled('structure_id')) {
            $query->parStructure($request->structure_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('effet_id')) {
            $query->whereHas('action.produit', function ($q) use ($request) {
                $q->where('effet_id', $request->effet_id);
            });
        }

        // Si l'utilisateur n'est pas admin, filtrer par sa structure
        if (!auth()->user()->isAdmin()) {
            $query->parStructure(auth()->user()->structure_id);
        }

        $activites = $query->paginate(20);
        $structures = Structure::actives()->get();
        $effets = Effet::actifs()->get();

        return view('activites.index', compact('activites', 'structures', 'effets'));
    }

    public function create()
    {
        $effets = Effet::with(['produits.actions'])->actifs()->get();
        $structures = Structure::actives()->get();
        $anneeActuelle = date('Y');

        return view('activites.create', compact('effets', 'structures', 'anneeActuelle'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action_id' => 'required|exists:actions,id',
            'annee' => 'required|integer|min:2020|max:' . (date('Y') + 5),
            'numero_activite' => 'required|string|max:50',
            'libelle_activite' => 'required|string|max:1000',
            'indicateur' => 'nullable|string|max:500',
            'realisation_2022' => 'nullable|string|max:255',
            'objectif_trim1' => 'nullable|string|max:255',
            'objectif_trim2' => 'nullable|string|max:255',
            'objectif_trim3' => 'nullable|string|max:255',
            'objectif_trim4' => 'nullable|string|max:255',
            'objectif_annuel' => 'nullable|string|max:255',
            'zones_execution' => 'nullable|string|max:500',
            'structure_responsable' => 'nullable|string|max:255',
            'budget_alloue_2022' => 'nullable|numeric|min:0',
            'cout_investissement_2023' => 'nullable|numeric|min:0',
            'cout_biens_services_2023' => 'nullable|numeric|min:0',
            'cout_transfert_2023' => 'nullable|numeric|min:0',
            'cout_personnel_2023' => 'nullable|numeric|min:0',
            'cout_prevu_2024' => 'nullable|numeric|min:0',
            'cout_prevu_2025' => 'nullable|numeric|min:0',
            'reference_pnd' => 'nullable|string|max:100',
            'programme_dppd' => 'nullable|string|max:100',
            'commentaires' => 'nullable|string',
            'fichiers.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $activite = new Activite($request->all());
        $activite->utilisateur_id = auth()->id();
        $activite->date_saisie = now();

        // Traitement des checkboxes de réformes
        $activite->reforme_identifiee = $request->has('reforme_identifiee');
        $activite->reforme_2023 = $request->has('reforme_2023');
        $activite->reforme_cle = $request->has('reforme_cle');
        $activite->realisation_majeure = $request->has('realisation_majeure');

        // Calculer le coût total automatiquement
        $activite->calculerCoutTotal();

        $activite->save();

        // Gestion des fichiers joints
        if ($request->hasFile('fichiers')) {
            $this->gererFichiersJoints($request->file('fichiers'), $activite);
        }

        return redirect()->route('activites.show', $activite)
            ->with('success', 'L\'activité a été enregistrée avec succès.');
    }

    public function show(Activite $activite)
    {
        $activite->load([
            'action.produit.effet',
            'utilisateur.structure',
            'validateur',
            'fichiersJoints.utilisateur',
            'realisationsTrimestrielles'
        ]);

        // Vérifier les droits d'accès
        if (
            !auth()->user()->isAdmin() &&
            $activite->utilisateur->structure_id !== auth()->user()->structure_id
        ) {
            abort(403, 'Accès non autorisé');
        }

        return view('activites.show', compact('activite'));
    }

    public function edit(Activite $activite)
    {
        // Vérifier les droits d'accès
        if (
            !auth()->user()->isAdmin() &&
            $activite->utilisateur_id !== auth()->id()
        ) {
            abort(403, 'Accès non autorisé');
        }

        // Ne pas permettre la modification si validé
        if ($activite->statut === 'valide') {
            return redirect()->route('activites.show', $activite)
                ->with('error', 'Impossible de modifier une activité déjà validée.');
        }

        $effets = Effet::with(['produits.actions'])->actifs()->get();
        $structures = Structure::actives()->get();

        return view('activites.edit', compact('activite', 'effets', 'structures'));
    }

    public function update(Request $request, Activite $activite)
    {
        // Vérifications identiques à edit()
        if (!auth()->user()->isAdmin() && $activite->utilisateur_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        if ($activite->statut === 'valide') {
            return redirect()->route('activites.show', $activite)
                ->with('error', 'Impossible de modifier une activité déjà validée.');
        }

        $validator = Validator::make($request->all(), [
            'action_id' => 'required|exists:actions,id',
            'annee' => 'required|integer|min:2020|max:' . (date('Y') + 5),
            'numero_activite' => 'required|string|max:50',
            'libelle_activite' => 'required|string|max:1000',
            // ... autres règles de validation identiques
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $activite->fill($request->all());

        // Traitement des checkboxes de réformes
        $activite->reforme_identifiee = $request->has('reforme_identifiee');
        $activite->reforme_2023 = $request->has('reforme_2023');
        $activite->reforme_cle = $request->has('reforme_cle');
        $activite->realisation_majeure = $request->has('realisation_majeure');

        // Recalculer le coût total
        $activite->calculerCoutTotal();

        $activite->save();

        // Gestion des nouveaux fichiers
        if ($request->hasFile('fichiers')) {
            $this->gererFichiersJoints($request->file('fichiers'), $activite);
        }

        return redirect()->route('activites.show', $activite)
            ->with('success', 'L\'activité a été mise à jour avec succès.');
    }

    public function destroy(Activite $activite)
    {
        // Vérifications de droits
        if (!auth()->user()->isAdmin() && $activite->utilisateur_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        if ($activite->statut === 'valide') {
            return redirect()->route('activites.index')
                ->with('error', 'Impossible de supprimer une activité validée.');
        }

        // Supprimer les fichiers physiques
        foreach ($activite->fichiersJoints as $fichier) {
            Storage::delete($fichier->chemin_fichier);
        }

        $activite->delete();

        return redirect()->route('activites.index')
            ->with('success', 'L\'activité a été supprimée avec succès.');
    }

    public function valider(Request $request, Activite $activite)
    {
        if (!auth()->user()->canValidate()) {
            abort(403, 'Vous n\'avez pas les droits pour valider.');
        }

        $validator = Validator::make($request->all(), [
            'action' => 'required|in:valider,rejeter',
            'commentaires' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $activite->statut = $request->action === 'valider' ? 'valide' : 'rejete';
        $activite->commentaires = $request->commentaires;
        $activite->validateur_id = auth()->id();
        $activite->date_validation = now();
        $activite->save();

        $message = $request->action === 'valider'
            ? 'L\'activité a été validée avec succès.'
            : 'L\'activité a été rejetée.';

        return redirect()->route('activites.show', $activite)
            ->with('success', $message);
    }

    public function getProduits(Effet $effet)
    {
        $produits = $effet->produits()->actifs()->get();

        return response()->json([
            'produits' => $produits->map(function ($produit) {
                return [
                    'id' => $produit->id,
                    'numero_produit' => $produit->numero_produit,
                    'libelle_produit' => $produit->libelle_produit
                ];
            })
        ]);
    }

    public function getActions(Produit $produit)
    {
        $actions = $produit->actions()->actifs()->get();

        return response()->json([
            'actions' => $actions->map(function ($action) {
                return [
                    'id' => $action->id,
                    'numero_action' => $action->numero_action,
                    'libelle_action' => $action->libelle_action
                ];
            })
        ]);
    }

    private function gererFichiersJoints($fichiers, $activite)
    {
        foreach ($fichiers as $fichier) {
            $nomFichier = Str::uuid() . '.' . $fichier->getClientOriginalExtension();
            $cheminFichier = $fichier->storeAs('ptba/activites', $nomFichier, 'public');

            FichierJoint::create([
                'attachable_type' => Activite::class,
                'attachable_id' => $activite->id,
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
