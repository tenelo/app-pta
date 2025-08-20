
{{-- resources/views/realisations/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle Réalisation Trimestrielle')
@section('page-title', 'Nouvelle Réalisation Trimestrielle')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Saisie d'une nouvelle réalisation trimestrielle
                </h5>
            </div>
            
            <form action="{{ route('realisations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                    
                    <!-- Sélection de l'activité et période -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="annee" class="form-label">Année <span class="text-danger">*</span></label>
                                <select name="annee" id="annee" class="form-select" required>
                                    @for($year = 2020; $year <= date('Y') + 5; $year++)
                                        <option value="{{ $year }}" {{ $year == $annee ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="trimestre" class="form-label">Trimestre <span class="text-danger">*</span></label>
                                <select name="trimestre" id="trimestre" class="form-select" required>
                                    @for($trim = 1; $trim <= 4; $trim++)
                                        <option value="{{ $trim }}" {{ $trim == $trimestre ? 'selected' : '' }}>
                                            Trimestre {{ $trim }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="activite_id" class="form-label">Activité <span class="text-danger">*</span></label>
                                <select name="activite_id" id="activite_id" class="form-select select2" required>
                                    <option value="">Sélectionner une activité validée</option>
                                    @foreach($activites as $activite)
                                        <option value="{{ $activite->id }}" 
                                                {{ $activiteSelectionnee && $activiteSelectionnee->id == $activite->id ? 'selected' : '' }}
                                                data-objectif-t1="{{ $activite->objectif_trim1 }}"
                                                data-objectif-t2="{{ $activite->objectif_trim2 }}"
                                                data-objectif-t3="{{ $activite->objectif_trim3 }}"
                                                data-objectif-t4="{{ $activite->objectif_trim4 }}"
                                                data-budget="{{ $activite->cout_total_2023 }}">
                                            {{ $activite->numero_activite }} - {{ Str::limit($activite->libelle_activite, 80) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Affichage de l'objectif du trimestre -->
                    <div id="objectif-info" class="alert alert-info d-none">
                        <h6><i class="fas fa-target me-2"></i>Objectif pour ce trimestre :</h6>
                        <p id="objectif-text" class="mb-0"></p>
                    </div>
                    
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
                                              placeholder="Décrivez ce qui a été concrètement réalisé pendant ce trimestre...">{{ old('realisation_quantitative') }}</textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="taux_realisation" class="form-label">Taux de réalisation (%)</label>
                                    <input type="number" name="taux_realisation" id="taux_realisation" 
                                           class="form-control" step="0.1" min="0" max="200"
                                           placeholder="Ex: 85.5" value="{{ old('taux_realisation') }}">
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
                                           value="{{ old('budget_execute') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taux_execution_budgetaire" class="form-label">Taux d'exécution budgétaire (%)</label>
                                    <input type="number" name="taux_execution_budgetaire" id="taux_execution_budgetaire" 
                                           class="form-control" step="0.1" min="0" max="200" readonly
                                           placeholder="Calculé automatiquement" 
                                           value="{{ old('taux_execution_budgetaire') }}">
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
                                      placeholder="Décrivez les principaux résultats et impacts obtenus...">{{ old('resultats_obtenus') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="difficultes_rencontrees" class="form-label">Difficultés rencontrées</label>
                            <textarea name="difficultes_rencontrees" id="difficultes_rencontrees" 
                                      class="form-control" rows="3"
                                      placeholder="Quelles ont été les principales difficultés ?">{{ old('difficultes_rencontrees') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mesures_correctives" class="form-label">Mesures correctives prises</label>
                            <textarea name="mesures_correctives" id="mesures_correctives" 
                                      class="form-control" rows="3"
                                      placeholder="Quelles mesures ont été prises pour surmonter les difficultés ?">{{ old('mesures_correctives') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="recommandations" class="form-label">Recommandations</label>
                            <textarea name="recommandations" id="recommandations" 
                                      class="form-control" rows="3"
                                      placeholder="Recommandations pour les prochains trimestres...">{{ old('recommandations') }}</textarea>
                        </div>
                    </div>
                    
                    <!-- Fichiers justificatifs -->
                    <div class="mb-3">
                        <label for="fichiers" class="form-label">Fichiers justificatifs</label>
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
                        <a href="{{ route('realisations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <div>
                            <button type="submit" name="statut" value="brouillon" class="btn btn-outline-success me-2">
                                <i class="fas fa-save me-2"></i>Enregistrer comme brouillon
                            </button>
                            <button type="submit" name="statut" value="soumis" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Enregistrer et soumettre
                            </button>
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
    // Afficher l'objectif du trimestre sélectionné
    function afficherObjectif() {
        const activiteId = $('#activite_id').val();
        const trimestre = $('#trimestre').val();
        
        if (activiteId && trimestre) {
            const activiteOption = $('#activite_id option:selected');
            const objectif = activiteOption.data('objectif-t' + trimestre);
            const budget = activiteOption.data('budget');
            
            if (objectif) {
                $('#objectif-text').text(objectif);
                $('#objectif-info').removeClass('d-none');
            } else {
                $('#objectif-info').addClass('d-none');
            }
            
            // Mettre à jour le budget de référence pour le calcul
            if (budget) {
                $('#budget_execute').attr('data-budget-total', budget);
            }
        } else {
            $('#objectif-info').addClass('d-none');
        }
    }
    
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
    
    // Event listeners
    $('#activite_id, #trimestre').on('change', afficherObjectif);
    $('#budget_execute').on('input', calculerTauxExecution);
    
    // Initialiser l'affichage
    afficherObjectif();
    
    // Validation du formulaire
    $('form').on('submit', function() {
        let isValid = true;
        
        // Vérifier les champs obligatoires
        $('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Vérifier qu'il n'y a pas déjà une réalisation pour cette activité/période
        const activiteId = $('#activite_id').val();
        const annee = $('#annee').val();
        const trimestre = $('#trimestre').val();
        
        if (activiteId && annee && trimestre) {
            // Cette vérification se fera côté serveur
        }
        
        if (!isValid) {
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }
    });
    
    // Mise à jour dynamique de la liste des activités selon l'année
    $('#annee').on('change', function() {
        const annee = $(this).val();
        if (annee) {
            // Recharger la page avec la nouvelle année
            window.location.href = '{{ route("realisations.create") }}?annee=' + annee + '&trimestre=' + $('#trimestre').val();
        }
    });
});
</script>
@endpush