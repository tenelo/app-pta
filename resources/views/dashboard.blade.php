@extends('layouts.app')

@section('title', 'Tableau de bord PTBA')
@section('page-title', 'Tableau de bord')

@section('content')
<!-- Statistiques principales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card card-stats text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_activites'] }}</h3>
                        <p class="mb-0">Activités {{ $annee }}</p>
                        <small>{{ $stats['activites_validees'] }} validées</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tasks fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_realisations'] }}</h3>
                        <p class="mb-0">Réalisations</p>
                        <small>{{ $stats['realisations_validees'] }} validées</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ number_format($stats['budget_total_prevu'], 0, ',', ' ') }}</h3>
                        <p class="mb-0">Budget prévu</p>
                        <small>millions FCFA</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        @php
                            $tauxExecution = $stats['budget_total_prevu'] > 0 
                                ? ($stats['budget_total_execute'] / $stats['budget_total_prevu']) * 100 
                                : 0;
                        @endphp
                        <h3 class="mb-0">{{ number_format($tauxExecution, 1) }}%</h3>
                        <p class="mb-0">Taux d'exécution</p>
                        <small>{{ number_format($stats['budget_total_execute'], 0, ',', ' ') }} M exécutés</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-percentage fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Actions rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('activites.create') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="fas fa-plus fa-2x mb-2 d-block"></i>
                            Nouvelle activité
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('realisations.create') }}" class="btn btn-outline-success btn-lg w-100">
                            <i class="fas fa-chart-line fa-2x mb-2 d-block"></i>
                            Saisir réalisation
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('rapports.synthese') }}" class="btn btn-outline-info btn-lg w-100">
                            <i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
                            Rapport de synthèse
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('rapports.tableau-bord') }}" class="btn btn-outline-warning btn-lg w-100">
                            <i class="fas fa-tachometer-alt fa-2x mb-2 d-block"></i>
                            Tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activités récentes -->
@if($activitesRecentes->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>Activités récentes
                </h5>
                <a href="{{ route('activites.index') }}" class="btn btn-sm btn-outline-primary">
                    Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($activitesRecentes as $activite)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $activite->numero_activite }}</h6>
                                    <p class="mb-1 text-muted small">
                                        {{ Str::limit($activite->libelle_activite, 80) }}
                                    </p>
                                    <small class="text-muted">
                                        {{ $activite->action->produit->effet->numero_effet }} - 
                                        {{ $activite->utilisateur->structure->nom_structure }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge 
                                        @if($activite->statut == 'valide') bg-success
                                        @elseif($activite->statut == 'soumis') bg-warning
                                        @elseif($activite->statut == 'rejete') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($activite->statut) }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $activite->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection