{{-- resources/views/rapports/synthese.blade.php --}}
@extends('layouts.app')

@section('title', 'Synthèse Annuelle PTBA')
@section('page-title', 'Synthèse Annuelle')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-contract me-2"></i>
                        Rapport de Synthèse Annuelle {{ $annee }}
                    </h5>
                    <div>
                        <button class="btn btn-light btn-sm me-2" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Imprimer
                        </button>
                        <a href="{{ route('rapports.synthese', ['annee' => $annee, 'format' => 'pdf']) }}"
                            class="btn btn-light btn-sm">
                            <i class="fas fa-download me-1"></i>PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Statistiques générales -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h4 class="text-info mb-4">Statistiques Générales {{ $annee }}</h4>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3>{{ $donnees['statistiques']['nombre_effets'] }}</h3>
                                <p class="mb-0">Effets</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3>{{ $donnees['statistiques']['nombre_activites'] }}</h3>
                                <p class="mb-0">Activités</p>
                                <small>{{ $donnees['statistiques']['activites_validees'] }} validées</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3>{{ number_format($donnees['statistiques']['budget_total'] / 1000000, 1) }}Md</h3>
                                <p class="mb-0">Budget Total</p>
                                <small>FCFA</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                @php
                                $tauxGlobal = $donnees['statistiques']['budget_total'] > 0
                                ? ($donnees['statistiques']['budget_execute'] / $donnees['statistiques']['budget_total']) * 100
                                : 0;
                                @endphp
                                <h3>{{ number_format($tauxGlobal, 1) }}%</h3>
                                <p class="mb-0">Taux d'Exécution</p>
                                <small>{{ number_format($donnees['statistiques']['budget_execute'] / 1000000, 1) }}Md exécutés</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Répartition par effet -->
                <div class="row">
                    <div class="col-12">
                        <h4 class="text-info mb-4">Répartition par Effet</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Effet</th>
                                        <th>Libellé</th>
                                        <th class="text-center">Activités</th>
                                        <th class="text-end">Budget Prévu</th>
                                        <th class="text-end">Budget Exécuté</th>
                                        <th class="text-center">Taux d'Exécution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($donnees['repartition_effets'] as $item)
                                    @php
                                    $taux_execution = $item['budget_prevu'] > 0 ? ($item['budget_execute'] / $item['budget_prevu']) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $item['effet']->numero_effet }}</strong></td>
                                        <td>{{ Str::limit($item['effet']->libelle_effet, 80) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $item['nombre_activites'] }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($item['budget_prevu'], 2, ',', ' ') }} M</td>
                                        <td class="text-end">{{ number_format($item['budget_execute'], 2, ',', ' ') }} M</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $taux_execution >= 80 ? 'success' : ($taux_execution >= 50 ? 'warning' : 'danger') }}">
                                                {{ number_format($taux_execution, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td colspan="2"><strong>TOTAL</strong></td>
                                        <td class="text-center">
                                            <strong>{{ $donnees['statistiques']['nombre_activites'] }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ number_format($donnees['statistiques']['budget_total'], 2, ',', ' ') }} M</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ number_format($donnees['statistiques']['budget_execute'], 2, ',', ' ') }} M</strong>
                                        </td>
                                        <td class="text-center">
                                            <strong>
                                                <span class="badge bg-{{ $tauxGlobal >= 80 ? 'success' : ($tauxGlobal >= 50 ? 'warning' : 'danger') }}">
                                                    {{ number_format($tauxGlobal, 1) }}%
                                                </span>
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Conclusions -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-lightbulb me-2"></i>Conclusions et Recommandations
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-success">Points forts :</h6>
                                        <ul>
                                            <li>{{ $donnees['statistiques']['activites_validees'] }} activités validées sur {{ $donnees['statistiques']['nombre_activites'] }}</li>
                                            <li>Taux d'exécution budgétaire de {{ number_format($tauxGlobal, 1) }}%</li>
                                            <li>{{ $donnees['statistiques']['nombre_effets'] }} effets en cours de réalisation</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-warning">Recommandations :</h6>
                                        <ul>
                                            <li>Améliorer le suivi trimestriel des réalisations</li>
                                            <li>Renforcer la coordination entre les structures</li>
                                            <li>Optimiser l'exécution budgétaire</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Rapport généré le {{ now()->format('d/m/Y à H:i') }}
                    </small>
                    <a href="{{ route('rapports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour aux rapports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection