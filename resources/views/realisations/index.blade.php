{{-- resources/views/realisations/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Réalisations Trimestrielles PTBA')
@section('page-title', 'Réalisations Trimestrielles')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h4 class="text-primary">Réalisations trimestrielles</h4>
        <p class="text-muted">Suivi des réalisations et performances par trimestre</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('realisations.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Nouvelle réalisation
        </a>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('realisations.index') }}" class="row g-3">
            <div class="col-md-2">
                <label for="annee" class="form-label">Année</label>
                <select name="annee" id="annee" class="form-select">
                    <option value="">Toutes</option>
                    @for($year = 2020; $year <= date('Y') + 5; $year++)
                        <option value="{{ $year }}" {{ request(annee) = $year ? selected : '' }}>
                        {{ $year }}
                        </option>
                        @endfor
                </select>
            </div>

            <div class="col-md-2">
                <label for="trimestre" class="form-label">Trimestre</label>
                <select name="trimestre" id="trimestre" class="form-select">
                    <option value="">Tous</option>
                    @for($trim = 1; $trim <= 4; $trim++)
                        <option value="{{ $trim }}" {{ request('trimestre') == $trim ? 'selected' : '' }}>
                        T{{ $trim }}
                        </option>
                        @endfor
                </select>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="col-md-3">
                <label for="structure_id" class="form-label">Structure</label>
                <select name="structure_id" id="structure_id" class="form-select">
                    <option value="">Toutes les structures</option>
                    @foreach(\App\Models\Structure::actives()->get() as $structure)
                    <option value="{{ $structure->id }}" {{ request('structure_id') == $structure->id ? 'selected' : '' }}>
                        {{ $structure->nom_structure }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="col-md-2">
                <label for="statut" class="form-label">Statut</label>
                <select name="statut" id="statut" class="form-select">
                    <option value="">Tous</option>
                    <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                    <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                    <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('realisations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Liste des réalisations -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Activité</th>
                        <th>Période</th>
                        <th>Objectif / Réalisation</th>
                        <th>Taux de réalisation</th>
                        <th>Budget exécuté</th>
                        <th>Statut</th>
                        <th>Saisi par</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($realisations as $realisation)
                    <tr>
                        <td>
                            <div class="fw-medium">{{ $realisation->activite->numero_activite }}</div>
                            <small class="text-muted">
                                {{ Str::limit($realisation->activite->libelle_activite, 50) }}
                            </small>
                        </td>
                        <td>
                            <div class="text-center">
                                <span class="badge bg-info">{{ $realisation->annee }}</span>
                                <span class="badge bg-secondary">T{{ $realisation->trimestre }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                <div><strong>Objectif :</strong> {{ $realisation->objectif_trimestre ?: 'Non défini' }}</div>
                                @if($realisation->realisation_quantitative)
                                <div class="text-success"><strong>Réalisé :</strong> {{ $realisation->realisation_quantitative }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($realisation->taux_realisation)
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar 
                                                @if($realisation->taux_realisation >= 80) bg-success
                                                @elseif($realisation->taux_realisation >= 60) bg-warning
                                                @else bg-danger
                                                @endif"
                                        style="width: {{ min([$realisation->taux_realisation, 100]) }}%">
                                    </div>
                                </div>
                                <span class="fw-bold">{{ number_format($realisation->taux_realisation, 1) }}%</span>
                            </div>
                            @else
                            <span class="text-muted">Non renseigné</span>
                            @endif
                        </td>
                        <td>
                            @if($realisation->budget_execute)
                            <strong>{{ number_format($realisation->budget_execute, 2, ',', ' ') }}</strong>
                            <br><small class="text-muted">millions FCFA</small>
                            @if($realisation->taux_execution_budgetaire)
                            <br><small class="text-info">({{ number_format($realisation->taux_execution_budgetaire, 1) }}%)</small>
                            @endif
                            @else
                            <span class="text-muted">Non renseigné</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge 
                                    @if($realisation->statut == 'valide') bg-success
                                    @elseif($realisation->statut == 'soumis') bg-warning
                                    @elseif($realisation->statut == 'rejete') bg-danger
                                    @else bg-secondary
                                    @endif">
                                {{ ucfirst($realisation->statut) }}
                            </span>
                        </td>
                        <td>
                            <small>
                                {{ $realisation->utilisateur->nom_complet }}<br>
                                <span class="text-muted">{{ $realisation->date_saisie->format('d/m/Y') }}</span>
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('realisations.show', $realisation) }}"
                                    class="btn btn-outline-primary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($realisation->statut != 'valide' && (auth()->user()->isAdmin() || $realisation->utilisateur_id == auth()->id()))
                                <a href="{{ route('realisations.edit', $realisation) }}"
                                    class="btn btn-outline-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

                                @if(auth()->user()->canValidate() && $realisation->statut == 'soumis')
                                <button type="button" class="btn btn-outline-success"
                                    data-bs-toggle="modal" data-bs-target="#modalValidation{{ $realisation->id }}"
                                    title="Valider">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-chart-line fa-2x mb-3 d-block"></i>
                            Aucune réalisation trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($realisations->hasPages())
    <div class="card-footer">
        {{ $realisations->links() }}
    </div>
    @endif
</div>

<!-- Statistiques rapides -->
@if($realisations->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ $realisations->count() }}</h4>
                <small>Réalisations</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ $realisations->where('statut', 'valide')->count() }}</h4>
                <small>Validées</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ number_format($realisations->avg('taux_realisation') ?: 0, 1) }}%</h4>
                <small>Taux moyen</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ number_format($realisations->sum('budget_execute'), 0, ',', ' ') }}</h4>
                <small>Budget exécuté (M FCFA)</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modaux de validation -->
@foreach($realisations as $realisation)
@if(auth()->user()->canValidate() && $realisation->statut == 'soumis')
<div class="modal fade" id="modalValidation{{ $realisation->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('realisations.valider', $realisation) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Validation de la réalisation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Activité :</strong> {{ $realisation->activite->numero_activite }}</p>
                    <p><strong>Période :</strong> {{ $realisation->annee }} - T{{ $realisation->trimestre }}</p>
                    <p><strong>Taux :</strong> {{ $realisation->taux_realisation }}%</p>

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
                        <label for="commentaires{{ $realisation->id }}" class="form-label">Commentaires</label>
                        <textarea name="commentaires" id="commentaires{{ $realisation->id }}"
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
@endforeach
@endsection