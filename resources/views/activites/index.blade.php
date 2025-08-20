{{-- resources/views/activites/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Liste des Activités PTBA')
@section('page-title', 'Activités PTBA')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h4 class="text-primary">Liste des activités</h4>
        <p class="text-muted">Gestion et suivi des activités du Plan de Travail Annuel</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('activites.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle activité
        </a>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('activites.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="annee" class="form-label">Année</label>
                <select name="annee" id="annee" class="form-select">
                    <option value="">Toutes les années</option>
                    @for($year = 2020; $year <= date('Y') + 5; $year++)
                        <option value="{{ $year }}" {{ request('annee') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>
            
            @if(auth()->user()->isAdmin())
            <div class="col-md-3">
                <label for="structure_id" class="form-label">Structure</label>
                <select name="structure_id" id="structure_id" class="form-select">
                    <option value="">Toutes les structures</option>
                    @foreach($structures as $structure)
                        <option value="{{ $structure->id }}" {{ request('structure_id') == $structure->id ? 'selected' : '' }}>
                            {{ $structure->nom_structure }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            
            <div class="col-md-3">
                <label for="statut" class="form-label">Statut</label>
                <select name="statut" id="statut" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                    <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                    <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="effet_id" class="form-label">Effet</label>
                <select name="effet_id" id="effet_id" class="form-select">
                    <option value="">Tous les effets</option>
                    @foreach($effets as $effet)
                        <option value="{{ $effet->id }}" {{ request('effet_id') == $effet->id ? 'selected' : '' }}>
                            {{ $effet->numero_effet }} - {{ Str::limit($effet->libelle_effet, 40) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filtrer
                </button>
                <a href="{{ route('activites.index') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-times me-2"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Liste des activités -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N° Activité</th>
                        <th>Libellé</th>
                        <th>Effet / Action</th>
                        <th>Types</th>
                        <th>Budget 2023</th>
                        <th>Statut</th>
                        <th>Structure</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activites as $activite)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $activite->numero_activite }}</strong>
                                <br>
                                <small class="text-muted">{{ $activite->annee }}</small>
                            </td>
                            <td>
                                <div class="fw-medium">{{ Str::limit($activite->libelle_activite, 60) }}</div>
                                @if($activite->indicateur)
                                    <small class="text-muted">
                                        <i class="fas fa-chart-bar me-1"></i>{{ Str::limit($activite->indicateur, 40) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    <strong>{{ $activite->action->produit->effet->numero_effet }}</strong>
                                    <br>{{ Str::limit($activite->action->numero_action, 20) }}
                                </div>
                            </td>
                            <td>
                                @foreach($activite->types_reformes as $type)
                                    <span class="badge badge-reforme 
                                        @if($type == 'R') bg-info
                                        @elseif($type == 'R23') bg-warning
                                        @elseif($type == 'RC') bg-danger
                                        @elseif($type == 'RM') bg-success
                                        @endif">{{ $type }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($activite->cout_total_2023)
                                    <strong>{{ number_format($activite->cout_total_2023, 2, ',', ' ') }}</strong>
                                    <br><small class="text-muted">millions FCFA</small>
                                @else
                                    <span class="text-muted">Non défini</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge 
                                    @if($activite->statut == 'valide') bg-success
                                    @elseif($activite->statut == 'soumis') bg-warning
                                    @elseif($activite->statut == 'rejete') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($activite->statut) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $activite->utilisateur->structure->nom_structure }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('activites.show', $activite) }}" 
                                       class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($activite->statut != 'valide' && (auth()->user()->isAdmin() || $activite->utilisateur_id == auth()->id()))
                                        <a href="{{ route('activites.edit', $activite) }}" 
                                           class="btn btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->canValidate() && $activite->statut == 'soumis')
                                        <button type="button" class="btn btn-outline-success" 
                                                data-bs-toggle="modal" data-bs-target="#modalValidation{{ $activite->id }}"
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
                                <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                                Aucune activité trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($activites->hasPages())
        <div class="card-footer">
            {{ $activites->links() }}
        </div>
    @endif
</div>

<!-- Modaux de validation -->
@foreach($activites as $activite)
    @if(auth()->user()->canValidate() && $activite->statut == 'soumis')
        <div class="modal fade" id="modalValidation{{ $activite->id }}" tabindex="-1">
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
                                <label for="commentaires{{ $activite->id }}" class="form-label">Commentaires</label>
                                <textarea name="commentaires" id="commentaires{{ $activite->id }}" 
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