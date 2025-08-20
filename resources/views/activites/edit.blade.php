{{-- resources/views/activites/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier Activité PTBA')
@section('page-title', 'Modifier l\'activité')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Modification de l'activité {{ $activite->numero_activite }}
                </h5>
            </div>

            <form action="{{ route('activites.update', $activite) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row">
                        <!-- Informations de base -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="annee" class="form-label">Année <span class="text-danger">*</span></label>
                                <select name="annee" id="annee" class="form-select" required>
                                    @for($year = 2020; $year <= date('Y') + 5; $year++)
                                        <option value="{{ $year }}" {{ $year == $activite->annee ? 'selected' : '' }}>
                                        {{ $year }}
                                        </option>
                                        @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_activite" class="form-label">Numéro d'activité <span class="text-danger">*</span></label>
                                <input type="text" name="numero_activite" id="numero_activite"
                                    class="form-control" placeholder="Ex: 1.1.1.1"
                                    value="{{ old('numero_activite', $activite->numero_activite) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Hiérarchie Effet > Produit > Action -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="effet_id" class="form-label">Effet <span class="text-danger">*</span></label>
                                <select name="effet_id" id="effet_id" class="form-select select2" required>
                                    <option value="">Sélectionner un effet</option>
                                    @foreach($effets as $effet)
                                    <option value="{{ $effet->id }}"
                                        {{ old('effet_id', $activite->action->produit->effet_id) == $effet->id ? 'selected' : '' }}>
                                        {{ $effet->numero_effet }} - {{ Str::limit($effet->libelle_effet, 60) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="produit_id" class="form-label">Produit <span class="text-danger">*</span></label>
                                <select name="produit_id" id="produit_id" class="form-select select2" required>
                                    <option value="">Sélectionner d'abord un effet</option>
                                    @foreach($produits as $produit)
                                    <option value="{{ $produit->id }}"
                                        {{ old('produit_id', $activite->action->produit_id) == $produit->id ? 'selected' : '' }}>
                                        {{ $produit->numero_produit }} - {{ Str::limit($produit->libelle_produit, 60) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="action_id" class="form-label">Action <span class="text-danger">*</span></label>
                                <select name="action_id" id="action_id" class="form-select select2" required>
                                    <option value="">Sélectionner d'abord un produit</option>
                                    @foreach($actions as $action)
                                    <option value="{{ $action->id }}"
                                        {{ old('action_id', $activite->action_id) == $action->id ? 'selected' : '' }}>
                                        {{ $action->numero_action }} - {{ Str::limit($action->libelle_action, 60) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Libellé de l'activité -->
                    <div class="mb-3">
                        <label for="libelle_activite" class="form-label">Libellé de l'activité <span class="text-danger">*</span></label>
                        <textarea name="libelle_activite" id="libelle_activite"
                            class="form-control" rows="3" required
                            placeholder="Description détaillée de l'activité...">{{ old('libelle_activite', $activite->libelle_activite) }}</textarea>
                    </div>

                    <!-- Types de réformes -->
                    <div class="mb-3">
                        <label class="form-label">Classification des réformes</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="reforme_identifiee" id="reforme_identifiee"
                                        {{ old('reforme_identifiee', $activite->reforme_identifiee) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="reforme_identifiee">
                                        <span class="badge bg-info">R</span> Réforme identifiée
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="reforme_2023" id="reforme_2023"
                                        {{ old('reforme_2023', $activite->reforme_2023) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="reforme_2023">
                                        <span class="badge bg-warning">R23</span> Réforme 2023
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="reforme_cle" id="reforme_cle"
                                        {{ old('reforme_cle', $activite->reforme_cle) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="reforme_cle">
                                        <span class="badge bg-danger">RC</span> Réforme clé
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="realisation_majeure" id="realisation_majeure"
                                        {{ old('realisation_majeure', $activite->realisation_majeure) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="realisation_majeure">
                                        <span class="badge bg-success">RM</span> Réalisation majeure
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Indicateurs et objectifs -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="indicateur" class="form-label">Indicateur</label>
                                <input type="text" name="indicateur" id="indicateur"
                                    class="form-control" placeholder="Indicateur de mesure"
                                    value="{{ old('indicateur', $activite->indicateur) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="realisation_2022" class="form-label">Réalisation 2022</label>
                                <input type="text" name="realisation_2022" id="realisation_2022"
                                    class="form-control" placeholder="Base de référence 2022"
                                    value="{{ old('realisation_2022', $activite->realisation_2022) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Objectifs trimestriels -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-calendar me-2"></i>Objectifs trimestriels 2023
                            </h6>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="objectif_trim1" class="form-label">Trimestre 1</label>
                                <input type="text" name="objectif_trim1" id="objectif_trim1"
                                    class="form-control" placeholder="Objectif T1"
                                    value="{{ old('objectif_trim1', $activite->objectif_trim1) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="objectif_trim2" class="form-label">Trimestre 2</label>
                                <input type="text" name="objectif_trim2" id="objectif_trim2"
                                    class="form-control" placeholder="Objectif T2"
                                    value="{{ old('objectif_trim2', $activite->objectif_trim2) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="objectif_trim3" class="form-label">Trimestre 3</label>
                                <input type="text" name="objectif_trim3" id="objectif_trim3"
                                    class="form-control" placeholder="Objectif T3"
                                    value="{{ old('objectif_trim3', $activite->objectif_trim3) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="objectif_trim4" class="form-label">Trimestre 4</label>
                                <input type="text" name="objectif_trim4" id="objectif_trim4"
                                    class="form-control" placeholder="Objectif T4"
                                    value="{{ old('objectif_trim4', $activite->objectif_trim4) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="objectif_annuel" class="form-label">Objectif annuel</label>
                        <input type="text" name="objectif_annuel" id="objectif_annuel"
                            class="form-control" placeholder="Objectif global pour l'année"
                            value="{{ old('objectif_annuel', $activite->objectif_annuel) }}">
                    </div>

                    <!-- Localisation et responsabilité -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="zones_execution" class="form-label">Zones d'exécution</label>
                                <textarea name="zones_execution" id="zones_execution"
                                    class="form-control" rows="2"
                                    placeholder="Localités, régions, départements...">{{ old('zones_execution', $activite->zones_execution) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="structure_responsable" class="form-label">Structure responsable</label>
                                <input type="text" name="structure_responsable" id="structure_responsable"
                                    class="form-control" placeholder="Direction, service responsable"
                                    value="{{ old('structure_responsable', $activite->structure_responsable) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Données budgétaires -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-money-bill-wave me-2"></i>Données budgétaires (en millions FCFA)
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="budget_alloue_2022" class="form-label">Budget alloué 2022</label>
                                <input type="number" name="budget_alloue_2022" id="budget_alloue_2022"
                                    class="form-control" step="0.01" min="0"
                                    value="{{ old('budget_alloue_2022', $activite->budget_alloue_2022) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cout_investissement_2023" class="form-label">Investissements 2023</label>
                                <input type="number" name="cout_investissement_2023" id="cout_investissement_2023"
                                    class="form-control budget-input" step="0.01" min="0"
                                    value="{{ old('cout_investissement_2023', $activite->cout_investissement_2023) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="cout_biens_services_2023" class="form-label">Biens et Services</label>
                                <input type="number" name="cout_biens_services_2023" id="cout_biens_services_2023"
                                    class="form-control budget-input" step="0.01" min="0"
                                    value="{{ old('cout_biens_services_2023', $activite->cout_biens_services_2023) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="cout_transfert_2023" class="form-label">Transferts</label>
                                <input type="number" name="cout_transfert_2023" id="cout_transfert_2023"
                                    class="form-control budget-input" step="0.01" min="0"
                                    value="{{ old('cout_transfert_2023', $activite->cout_transfert_2023) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="cout_personnel_2023" class="form-label">Personnel</label>
                                <input type="number" name="cout_personnel_2023" id="cout_personnel_2023"
                                    class="form-control budget-input" step="0.01" min="0"
                                    value="{{ old('cout_personnel_2023', $activite->cout_personnel_2023) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="cout_total_2023" class="form-label">Total 2023</label>
                                <input type="number" name="cout_total_2023" id="cout_total_2023"
                                    class="form-control" step="0.01" min="0" readonly
                                    value="{{ old('cout_total_2023', $activite->cout_total_2023) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cout_prevu_2024" class="form-label">Coût prévu 2024</label>
                                <input type="number" name="cout_prevu_2024" id="cout_prevu_2024"
                                    class="form-control" step="0.01" min="0"
                                    value="{{ old('cout_prevu_2024', $activite->cout_prevu_2024) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cout_prevu_2025" class="form-label">Coût prévu 2025</label>
                                <input type="number" name="cout_prevu_2025" id="cout_prevu_2025"
                                    class="form-control" step="0.01" min="0"
                                    value="{{ old('cout_prevu_2025', $activite->cout_prevu_2025) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Références -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reference_pnd" class="form-label">Référence PND 2125</label>
                                <input type="text" name="reference_pnd" id="reference_pnd"
                                    class="form-control" placeholder="Référence dans le PND"
                                    value="{{ old('reference_pnd', $activite->reference_pnd) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="programme_dppd" class="form-label">Programme DPPD</label>
                                <input type="text" name="programme_dppd" id="programme_dppd"
                                    class="form-control" placeholder="Programme DPPD associé"
                                    value="{{ old('programme_dppd', $activite->programme_dppd) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Commentaires -->
                    <div class="mb-3">
                        <label for="commentaires" class="form-label">Commentaires</label>
                        <textarea name="commentaires" id="commentaires"
                            class="form-control" rows="3"
                            placeholder="Observations, remarques particulières...">{{ old('commentaires', $activite->commentaires) }}</textarea>
                    </div>

                    <!-- Fichiers joints existants -->
                    @if($activite->fichiersJoints->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Fichiers joints actuels</label>
                        <div class="list-group">
                            @foreach($activite->fichiersJoints as $fichier)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-{{ $fichier->icone }} me-2"></i>
                                    {{ $fichier->nom_original }}
                                    <small class="text-muted">({{ $fichier->taille_formattee }})</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="supprimerFichier('{{ $fichier->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Nouveaux fichiers joints -->
                    <div class="mb-3">
                        <label for="fichiers" class="form-label">Ajouter des fichiers</label>
                        <input type="file" name="fichiers[]" id="fichiers"
                            class="form-control" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <div class="form-text">
                            Formats acceptés : PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Taille max : 10 MB par fichier.
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('activites.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <div>
                            @if($activite->statut != 'valide')
                            <button type="submit" name="statut" value="brouillon" class="btn btn-outline-primary me-2">
                                <i class="fas fa-save me-2"></i>Enregistrer comme brouillon
                            </button>
                            <button type="submit" name="statut" value="soumis" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Enregistrer et soumettre
                            </button>
                            @else
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Calcul automatique du total budgétaire
        $('.budget-input').on('input', function() {
            let total = 0;
            $('.budget-input').each(function() {
                let value = parseFloat($(this).val()) || 0;
                total += value;
            });
            $('#cout_total_2023').val(total.toFixed(2));
        });

        // Gestion des cascades Effet > Produit > Action
        $('#effet_id').on('change', function() {
            let effetId = $(this).val();
            $('#produit_id').empty().append('<option value="">Chargement...</option>');
            $('#action_id').empty().append('<option value="">Sélectionner d\'abord un produit</option>');

            if (effetId) {
                $.get('/api/produits/' + effetId, function(data) {
                    $('#produit_id').empty().append('<option value="">Sélectionner un produit</option>');
                    $.each(data.produits, function(index, produit) {
                        $('#produit_id').append(
                            $('<option></option>').val(produit.id).text(produit.numero_produit + ' - ' + produit.libelle_produit.substring(0, 80))
                        );
                    });
                });
            } else {
                $('#produit_id').empty().append('<option value="">Sélectionner d\'abord un effet</option>');
            }
        });

        $('#produit_id').on('change', function() {
            let produitId = $(this).val();
            $('#action_id').empty().append('<option value="">Chargement...</option>');

            if (produitId) {
                $.get('/api/actions/' + produitId, function(data) {
                    $('#action_id').empty().append('<option value="">Sélectionner une action</option>');
                    $.each(data.actions, function(index, action) {
                        $('#action_id').append(
                            $('<option></option>').val(action.id).text(action.numero_action + ' - ' + action.libelle_action.substring(0, 80))
                        );
                    });
                });
            } else {
                $('#action_id').empty().append('<option value="">Sélectionner d\'abord un produit</option>');
            }
        });
    });

    // Fonction pour supprimer un fichier
    function supprimerFichier(fichierId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?')) {
            fetch(`/fichiers/${fichierId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression du fichier');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression du fichier');
                });
        }
    }
</script>
@endpush