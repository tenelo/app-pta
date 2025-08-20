{{-- resources/views/activites/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Activité ' . $activite->numero_activite)
@section('page-title', 'Détails de l\'activité')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">
                        <i class="fas fa-eye me-2"></i>
                        Activité {{ $activite->numero_activite }} - {{ $activite->annee }}
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge 
                            @if($activite->statut == 'valide') bg-success
                            @elseif($activite->statut == 'soumis') bg-warning
                            @elseif($activite->statut == 'rejete') bg-danger
                            @else bg-secondary
                            @endif me-2">
                            {{ ucfirst($activite->statut) }}
                        </span>
                        
                        @foreach($activite->types_reformes as $type)
                            <span class="badge badge-reforme 
                                @if($type == 'R') bg-info
                                @elseif($type == 'R23') bg-warning
                                @elseif($type == 'RC') bg-danger
                                @elseif($type == 'RM') bg-success
                                @endif">{{ $type }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="btn-group">
                    <a href="{{ route('activites.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    
                    @if($activite->statut != 'valide' && (auth()->user()->isAdmin() || $activite->utilisateur_id == auth()->id()))
                        <a href="{{ route('activites.edit', $activite) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                    @endif
                    
                    @if(auth()->user()->canValidate() && $activite->statut == 'soumis')
                        <button type="button" class="btn btn-success" 
                                data-bs-toggle="modal" data-bs-target="#modalValidation">
                            <i class="fas fa-check me-2"></i>Valider
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Informations générales -->
                    <div class="col-lg-8">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-info-circle me-2"></i>Informations générales
                        </h6>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">Libellé de l'activité</h6>
                            <p class="text-muted">{{ $activite->libelle_activite }}</p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Hiérarchie</h6>
                                <div class="text-muted small">
                                    <div><strong>Effet :</strong> {{ $activite->action->produit->effet->numero_effet }}</div>
                                    <div><strong>Produit :</strong> {{ $activite->action->produit->numero_produit }}</div>
                                    <div><strong>Action :</strong> {{ $activite->action->numero_action }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Responsabilité</h6>
                                <div class="text-muted small">
                                    <div><strong>Saisi par :</strong> {{ $activite->utilisateur->nom_complet }}</div>
                                    <div><strong>Structure :</strong> {{ $activite->utilisateur->structure->nom_structure }}</div>
                                    <div><strong>Date saisie :</strong> {{ $activite->date_saisie->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        </div>
                        
                        @if($activite->indicateur || $activite->realisation_2022)
                        <div class="row mb-4">
                            @if($activite->indicateur)
                            <div class="col-md-6">
                                <h6 class="fw-bold">Indicateur</h6>
                                <p class="text-muted">{{ $activite->indicateur }}</p>
                            </div>
                            @endif
                            @if($activite->realisation_2022)
                            <div class="col-md-6">
                                <h6 class="fw-bold">Réalisation 2022</h6>
                                <p class="text-muted">{{ $activite->realisation_2022 }}</p>
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Objectifs trimestriels -->
                        @if($activite->objectif_trim1 || $activite->objectif_trim2 || $activite->objectif_trim3 || $activite->objectif_trim4)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-calendar me-2"></i>Objectifs trimestriels
                            </h6>
                            <div class="row">
                                @if($activite->objectif_trim1)
                                <div class="col-md-6 mb-2">
                                    <strong>T1 :</strong> {{ $activite->objectif_trim1 }}
                                </div>
                                @endif
                                @if($activite->objectif_trim2)
                                <div class="col-md-6 mb-2">
                                    <strong>T2 :</strong> {{ $activite->objectif_trim2 }}
                                </div>
                                @endif
                                @if($activite->objectif_trim3)
                                <div class="col-md-6 mb-2">
                                    <strong>T3 :</strong> {{ $activite->objectif_trim3 }}
                                </div>
                                @endif
                                @if($activite->objectif_trim4)
                                <div class="col-md-6 mb-2">
                                    <strong>T4 :</strong> {{ $activite->objectif_trim4 }}
                                </div>
                                @endif
                            </div>
                            @if($activite->objectif_annuel)
                            <div class="mt-3">
                                <strong>Objectif annuel :</strong> {{ $activite->objectif_annuel }}
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Localisation -->
                        @if($activite->zones_execution || $activite->structure_responsable)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Localisation et responsabilité
                            </h6>
                            @if($activite->zones_execution)
                            <div class="mb-2">
                                <strong>Zones d'exécution :</strong> {{ $activite->zones_execution }}
                            </div>
                            @endif
                            @if($activite->structure_responsable)
                            <div class="mb-2">
                                <strong>Structure responsable :</strong> {{ $activite->structure_responsable }}
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Références -->
                        @if($activite->reference_pnd || $activite->programme_dppd)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-link me-2"></i>Références
                            </h6>
                            @if($activite->reference_pnd)
                            <div class="mb-2">
                                <strong>Référence PND 2125 :</strong> {{ $activite->reference_pnd }}
                            </div>
                            @endif
                            @if($activite->programme_dppd)
                            <div class="mb-2">
                                <strong>Programme DPPD :</strong> {{ $activite->programme_dppd }}
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Commentaires -->
                        @if($activite->commentaires)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-comments me-2"></i>Commentaires
                            </h6>
                            <p class="text-muted">{{ $activite->commentaires }}</p>
                        </div>
                        @endif
                        
                        <!-- Validation -->
                        @if($activite->validateur_id)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-stamp me-2"></i>Validation
                            </h6>
                            <div class="text-muted small">
                                <div><strong>Validé par :</strong> {{ $activite->validateur->nom_complet }}</div>
                                <div><strong>Date validation :</strong> {{ $activite->date_validation->format('d/m/Y H:i') }}</div>
                                @if($activite->commentaires_validation)
                                    <div><strong>Commentaires :</strong> {{ $activite->commentaires_validation }}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Sidebar droite -->
                    <div class="col-lg-4">
                        <!-- Données budgétaires -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-money-bill-wave me-2"></i>Budget (millions FCFA)
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($activite->budget_alloue_2022)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Budget 2022 :</span>
                                    <strong>{{ number_format($activite->budget_alloue_2022, 2, ',', ' ') }}</strong>
                                </div>
                                @endif
                                
                                <div class="border-top pt-2">
                                    <h6 class="text-primary mb-2">Budget 2023</h6>
                                    
                                    @if($activite->cout_investissement_2023)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Investissements :</span>
                                        <span>{{ number_format($activite->cout_investissement_2023, 2, ',', ' ') }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($activite->cout_biens_services_2023)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Biens & Services :</span>
                                        <span>{{ number_format($activite->cout_biens_services_2023, 2, ',', ' ') }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($activite->cout_transfert_2023)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Transferts :</span>
                                        <span>{{ number_format($activite->cout_transfert_2023, 2, ',', ' ') }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($activite->cout_personnel_2023)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Personnel :</span>
                                        <span>{{ number_format($activite->cout_personnel_2023, 2, ',', ' ') }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($activite->cout_total_2023)
                                    <div class="d-flex justify-content-between border-top pt-2">
                                        <strong>Total 2023 :</strong>
                                        <strong class="text-primary">{{ number_format($activite->cout_total_2023, 2, ',', ' ') }}</strong>
                                    </div>
                                    @endif
                                </div>
                                
                                @if($activite->cout_prevu_2024 || $activite->cout_prevu_2025)
                                <div class="border-top pt-2 mt-2">
                                    <h6 class="text-muted mb-2">Prévisions</h6>
                                    @if($activite->cout_prevu_2024)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">2024 :</span>
                                        <span>{{ number_format($activite->cout_prevu_2024, 2, ',', ' ') }}</span>
                                    </div>
                                    @endif
                                    @if($activite->cout_prevu_2025)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">2025 :</span>
                                        <span>{{ number_format($activite->cout_prevu_2025, 2, ',', ' ') }}</span>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Taux d'exécution -->
                        @if($activite->realisationsTrimestrielles->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Performance
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Taux d'exécution :</span>
                                    <strong class="text-info">{{ $activite->taux_execution }}%</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Taux de réalisation :</span>
                                    <strong class="text-success">{{ $activite->taux_realisation }}%</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Réalisations :</span>
                                    <strong>{{ $activite->realisationsTrimestrielles->count() }}/4 trimestres</strong>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Fichiers joints -->
                        @if($activite->fichiersJoints->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-paperclip me-2"></i>Fichiers joints
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach($activite->fichiersJoints as $fichier)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <i class="fas fa-{{ $fichier->icone }} me-2"></i>
                                            <a href="{{ $fichier->url }}" target="_blank" class="text-decoration-none">
                                                {{ $fichier->nom_original }}
                                            </a>
                                            <br>
                                            <small class="text-muted">
                                                {{ $fichier->taille_formattee }} - 
                                                {{ $fichier->utilisateur->nom_complet }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de validation -->
@if(auth()->user()->canValidate() && $activite->statut == 'soumis')
<div class="modal fade" id="modalValidation" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('activites.valider', $activite) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Validation de l'activité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Activité :</strong> {{ $activite->numero_activite }}</p>
                    <p>{{ Str::limit($activite->libelle_activite, 100) }}</p>
                    
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