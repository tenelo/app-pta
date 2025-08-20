{{-- resources/views/realisations/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails Réalisation')
@section('page-title', 'Détails de la réalisation')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Réalisation T{{ $realisation->trimestre }} {{ $realisation->annee }} - {{ $realisation->activite->numero_activite }}
                    </h5>
                    <span class="badge badge-lg 
                        @if($realisation->statut == 'valide') bg-success
                        @elseif($realisation->statut == 'soumis') bg-warning
                        @elseif($realisation->statut == 'rejete') bg-danger
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($realisation->statut) }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                <!-- Informations sur l'activité associée -->
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading"><i class="fas fa-tasks me-2"></i>Activité associée</h6>
                    <p><strong>{{ $realisation->activite->numero_activite }}</strong> - {{ $realisation->activite->libelle_activite }}</p>
                    <hr>
                    <p class="mb-0">
                        <strong>Hiérarchie :</strong>
                        {{ $realisation->activite->action->produit->effet->numero_effet }} >
                        {{ $realisation->activite->action->produit->numero_produit }} >
                        {{ $realisation->activite->action->numero_action }}
                    </p>
                </div>

                <!-- Objectif vs Réalisation -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-success">Objectif fixé (T{{ $realisation->trimestre }})</h6>
                        @if($realisation->objectif_trimestre)
                        <div class="alert alert-light">
                            {{ $realisation->objectif_trimestre }}
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <em>Aucun objectif spécifique défini pour ce trimestre</em>
                        </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-success">Réalisation effective</h6>
                        @if($realisation->realisation_quantitative)
                        <div class="alert alert-{{ $realisation->taux_realisation >= 80 ? 'success' : ($realisation->taux_realisation >= 50 ? 'warning' : 'danger') }}">
                            {{ $realisation->realisation_quantitative }}
                        </div>
                        @else
                        <div class="alert alert-secondary">
                            <em>Non renseigné</em>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Indicateurs de performance -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-success">Performance quantitative</h6>
                        @if($realisation->taux_realisation)
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-3" style="height: 25px;">
                                <div class="progress-bar bg-{{ $realisation->taux_realisation >= 80 ? 'success' : ($realisation->taux_realisation >= 50 ? 'warning' : 'danger') }}"
                                    style="width: {{ min($realisation->taux_realisation, 100) }}%">
                                    {{ $realisation->taux_realisation }}%
                                </div>
                            </div>
                            <span class="badge bg-{{ $realisation->taux_realisation >= 80 ? 'success' : ($realisation->taux_realisation >= 50 ? 'warning' : 'danger') }}">
                                {{ $realisation->taux_realisation }}%
                            </span>
                        </div>
                        @else
                        <p class="text-muted">Taux de réalisation non renseigné</p>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-success">Performance budgétaire</h6>
                        @if($realisation->budget_execute)
                        <p><strong>Budget exécuté :</strong> {{ number_format($realisation->budget_execute, 2, ',', ' ') }} millions FCFA</p>
                        @if($realisation->taux_execution_budgetaire)
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-3" style="height: 25px;">
                                <div class="progress-bar bg-info"
                                    style="width: {{ min($realisation->taux_execution_budgetaire, 100) }}%">
                                    {{ $realisation->taux_execution_budgetaire }}%
                                </div>
                            </div>
                            <span class="badge bg-info">{{ $realisation->taux_execution_budgetaire }}%</span>
                        </div>
                        @endif
                        @else
                        <p class="text-muted">Budget exécuté non renseigné</p>
                        @endif
                    </div>
                </div>

                <!-- Résultats et difficultés -->
                @if($realisation->resultats_obtenus)
                <div class="mb-4">
                    <h6 class="text-success">Résultats obtenus</h6>
                    <div class="alert alert-success">
                        {{ $realisation->resultats_obtenus }}
                    </div>
                </div>
                @endif

                @if($realisation->difficultes_rencontrees)
                <div class="mb-4">
                    <h6 class="text-success">Difficultés rencontrées</h6>
                    <div class="alert alert-warning">
                        {{ $realisation->difficultes_rencontrees }}
                    </div>
                </div>
                @endif

                @if($realisation->mesures_correctives)
                <div class="mb-4">
                    <h6 class="text-success">Mesures correctives</h6>
                    <div class="alert alert-info">
                        {{ $realisation->mesures_correctives }}
                    </div>
                </div>
                @endif

                @if($realisation->recommandations)
                <div class="mb-4">
                    <h6 class="text-success">Recommandations</h6>
                    <div class="alert alert-primary">
                        {{ $realisation->recommandations }}
                    </div>
                </div>
                @endif

                <!-- Informations de suivi -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-success">Informations de saisie</h6>
                        <p><strong>Saisi par :</strong> {{ $realisation->utilisateur->nom_complet }}</p>
                        <p><strong>Structure :</strong> {{ $realisation->utilisateur->structure->nom_structure }}</p>
                        <p><strong>Date de saisie :</strong> {{ $realisation->date_saisie->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($realisation->validateur)
                        <h6 class="text-success">Validation</h6>
                        <p><strong>Validé par :</strong> {{ $realisation->validateur->nom_complet }}</p>
                        <p><strong>Date :</strong> {{ $realisation->date_validation->format('d/m/Y H:i') }}</p>
                        @if($realisation->commentaires_validation)
                        <p><strong>Commentaires :</strong> {{ $realisation->commentaires_validation }}</p>
                        @endif
                        @endif
                    </div>
                </div>

                <!-- Fichiers joints -->
                @if($realisation->fichiersJoints->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-success">Fichiers justificatifs</h6>
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
                                <a href="{{ $fichier->url }}" class="btn btn-sm btn-outline-success" target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('realisations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                    </a>

                    <div>
                        @if($realisation->statut != 'valide' && (auth()->user()->isAdmin() || $realisation->utilisateur_id == auth()->id()))
                        <a href="{{ route('realisations.edit', $realisation) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                        @endif

                        @if(auth()->user()->canValidate() && $realisation->statut == 'soumis')
                        <button type="button" class="btn btn-success me-2"
                            data-bs-toggle="modal" data-bs-target="#modalValidation">
                            <i class="fas fa-check me-2"></i>Valider
                        </button>
                        @endif

                        <a href="{{ route('activites.show', $realisation->activite) }}"
                            class="btn btn-primary">
                            <i class="fas fa-tasks me-2"></i>Voir l'activité
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de validation -->
@if(auth()->user()->canValidate() && $realisation->statut == 'soumis')
<div class="modal fade" id="modalValidation" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('realisations.valider', $realisation) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Validation de la réalisation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="action" value="valider" required>
                                <label class="form-check-label text-success">
                                    <i class="fas fa-check me-1"></i>Valider
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="action" value="rejeter" required>
                                <label class="form-check-label text-danger">
                                    <i class="fas fa-times me-1"></i>Rejeter
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="commentaires" class="form-label">Commentaires</label>
                        <textarea name="commentaires" id="commentaires"
                            class="form-control" rows="3"
                            placeholder="Observations, recommandations..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Confirmer</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection