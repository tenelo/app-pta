{{-- resources/views/realisations/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier Réalisation')
@section('page-title', 'Modifier la réalisation')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Modification de la réalisation T{{ $realisation->trimestre }} {{ $realisation->annee }}
                </h5>
            </div>

            <form action="{{ route('realisations.update', $realisation) }}" method="POST" enctype="multipart/form-data">
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

                    <!-- Informations sur l'activité (non modifiables) -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading"><i class="fas fa-tasks me-2"></i>Activité concernée</h6>
                        <p><strong>{{ $realisation->activite->numero_activite }}</strong> - {{ $realisation->activite->libelle_activite }}</p>
                        <p class="mb-0">
                            <strong>Période :</strong> {{ $realisation->annee }} - Trimestre {{ $realisation->trimestre }}
                        </p>
                    </div>

                    <!-- Affichage de l'objectif du trimestre -->
                    @if($realisation->objectif_trimestre)
                    <div class="alert alert-success mb-4">
                        <h6><i class="fas fa-target me-2"></i>Objectif pour ce trimestre :</h6>
                        <p class="mb-0">{{ $realisation->objectif_trimestre }}</p>
                    </div>
                    @endif

                    <!-- Réalisations quantitatives -->
                    <div class="mb-4">
                        <h6 class="text-success border-bottom pb-2 mb-3">
                            <i class="fas fa-chart-bar me-2"></i>Réalisations quantitatives
                        </h6>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="realisation_quantitative" class="form-label">Réalisation quantitative</label>
                                    <textarea name="realisation_quantitative" id="realisation_quantitative"
                                        class="form-control" rows="2"
                                        placeholder="Décrivez ce qui a été concrètement réalisé pendant ce trimestre...">{{ old('realisation_quantitative', $realisation->realisation_quantitative) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="taux_realisation" class="form-label">Taux de réalisation (%)</label>
                                    <input type="number" name="taux_realisation" id="taux_realisation"
                                        class="form-control" step="0.1" min="0" max="200"
                                        placeholder="Ex: 85.5" value="{{ old('taux_realisation', $realisation->taux_realisation) }}">
                                    <div class="form-text">
                                        Pourcentage d'atteinte de l'objectif trimestriel
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Réalisations financières -->
                    <div class="mb-4">
                        <h6 class="text-success border-bottom pb-2 mb-3">
                            <i class="fas fa-money-bill-wave me-2"></i>Réalisations financières
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="budget_execute" class="form-label">Budget exécuté (millions FCFA)</label>
                                    <input type="number" name="budget_execute" id="budget_execute"
                                        class="form-control" step="0.01" min="0"
                                        placeholder="Montant effectivement dépensé"
                                        value="{{ old('budget_execute', $realisation->budget_execute) }}"
                                        data-budget-total="{{ $realisation->activite->cout_total_2023 }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taux_execution_budgetaire" class="form-label">Taux d'exécution budgétaire (%)</label>
                                    <input type="number" name="taux_execution_budgetaire" id="taux_execution_budgetaire"
                                        class="form-control" step="0.1" min="0" max="200" readonly
                                        placeholder="Calculé automatiquement"
                                        value="{{ old('taux_execution_budgetaire', $realisation->taux_execution_budgetaire) }}">
                                    <div class="form-text">
                                        Calculé par rapport au budget trimestriel prévu
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations qualitatives -->
                    <div class="mb-4">
                        <h6 class="text-success border-bottom pb-2 mb-3">
                            <i class="fas fa-comments me-2"></i>Informations qualitatives
                        </h6>

                        <div class="mb-3">
                            <label for="resultats_obtenus" class="form-label">Résultats obtenus</label>
                            <textarea name="resultats_obtenus" id="resultats_obtenus"
                                class="form-control" rows="3"
                                placeholder="Décrivez les principaux résultats et impacts obtenus...">{{ old('resultats_obtenus', $realisation->resultats_obtenus) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="difficultes_rencontrees" class="form-label">Difficultés rencontrées</label>
                            <textarea name="difficultes_rencontrees" id="difficultes_rencontrees"
                                class="form-control" rows="3"
                                placeholder="Quelles ont été les principales difficultés ?">{{ old('difficultes_rencontrees', $realisation->difficultes_rencontrees) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="mesures_correctives" class="form-label">Mesures correctives prises</label>
                            <textarea name="mesures_correctives" id="mesures_correctives"
                                class="form-control" rows="3"
                                placeholder="Quelles mesures ont été prises pour surmonter les difficultés ?">{{ old('mesures_correctives', $realisation->mesures_correctives) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="recommandations" class="form-label">Recommandations</label>
                            <textarea name="recommandations" id="recommandations"
                                class="form-control" rows="3"
                                placeholder="Recommandations pour les prochains trimestres...">{{ old('recommandations', $realisation->recommandations) }}</textarea>
                        </div>
                    </div>

                    <!-- Fichiers joints existants -->
                    @if($realisation->fichiersJoints->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Fichiers justificatifs actuels</label>
                        <div class="list-group">
                            @foreach($realisation->fichiersJoints as $fichier)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-{{ $fichier->icone }} me-2"></i>
                                    {{ $fichier->nom_original }}
                                    <small class="text-muted">({{ $fichier->taille_formattee }})</small>
                                    @if($fichier->description)
                                    <br><small class="text-muted">{{ $fichier->description }}</small>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ $fichier->url }}" class="btn btn-sm btn-outline-primary me-2" target="_blank">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="supprimerFichier('{{ $fichier->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Nouveaux fichiers justificatifs -->
                    <div class="mb-3">
                        <label for="fichiers" class="form-label">Ajouter des fichiers justificatifs</label>
                        <input type="file" name="fichiers[]" id="fichiers"
                            class="form-control" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <div class="form-text">
                            Joignez vos justificatifs (rapports, factures, photos, etc.).
                            Formats acceptés : PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Taille max : 10 MB par fichier.
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('realisations.show', $realisation) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour aux détails
                        </a>
                        <div>
                            @if($realisation->statut != 'valide')
                            <button type="submit" name="statut" value="brouillon" class="btn btn-outline-success me-2">
                                <i class="fas fa-save me-2"></i>Enregistrer comme brouillon
                            </button>
                            <button type="submit" name="statut" value="soumis" class="btn btn-success">
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
        // Calculer automatiquement le taux d'exécution budgétaire
        function calculerTauxExecution() {
            const budgetExecute = parseFloat($('#budget_execute').val()) || 0;
            const budgetTotal = parseFloat($('#budget_execute').attr('data-budget-total')) || 0;

            if (budgetTotal > 0) {
                const budgetTrimestre = budgetTotal / 4; // Budget trimestriel = budget annuel / 4
                const taux = (budgetExecute / budgetTrimestre) * 100;
                $('#taux_execution_budgetaire').val(taux.toFixed(2));
            }
        }

        // Event listener pour le calcul automatique
        $('#budget_execute').on('input', calculerTauxExecution);

        // Calcul initial au chargement de la page
        calculerTauxExecution();

        // Validation du formulaire
        $('form').on('submit', function() {
            let isValid = true;

            // Vérifier que au moins une information a été saisie
            const champsPrincipaux = [
                '#realisation_quantitative',
                '#taux_realisation',
                '#budget_execute',
                '#resultats_obtenus'
            ];

            let auMoinsUnChampRempli = false;
            champsPrincipaux.forEach(function(champ) {
                if ($(champ).val() && $(champ).val().trim() !== '') {
                    auMoinsUnChampRempli = true;
                }
            });

            if (!auMoinsUnChampRempli) {
                alert('Veuillez renseigner au moins une information (réalisation quantitative, taux, budget ou résultats).');
                isValid = false;
            }

            // Vérifier la cohérence du taux de réalisation
            const tauxRealisation = parseFloat($('#taux_realisation').val());
            if (tauxRealisation && (tauxRealisation < 0 || tauxRealisation > 200)) {
                alert('Le taux de réalisation doit être compris entre 0 et 200%.');
                $('#taux_realisation').focus();
                isValid = false;
            }

            // Vérifier la cohérence du budget
            const budgetExecute = parseFloat($('#budget_execute').val());
            if (budgetExecute && budgetExecute < 0) {
                alert('Le budget exécuté ne peut pas être négatif.');
                $('#budget_execute').focus();
                isValid = false;
            }

            return isValid;
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