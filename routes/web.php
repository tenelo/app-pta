
<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\RealisationTrimestrielleController;
use App\Http\Controllers\RapportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Activités PTBA
    Route::resource('activites', ActiviteController::class);
    Route::post('activites/{activite}/valider', [ActiviteController::class, 'valider'])->name('activites.valider');
    
    // Réalisations trimestrielles
    Route::resource('realisations', RealisationTrimestrielleController::class);
    Route::post('realisations/{realisation}/valider', [RealisationTrimestrielleController::class, 'valider'])->name('realisations.valider');
    
    // Rapports
    Route::prefix('rapports')->name('rapports.')->group(function () {
        Route::get('/', [RapportController::class, 'index'])->name('index');
        Route::get('/synthese-annuelle', [RapportController::class, 'syntheseAnnuelle'])->name('synthese');
        Route::get('/rapport-trimestriel', [RapportController::class, 'rapportTrimestriel'])->name('trimestriel');
        Route::get('/tableau-bord', [RapportController::class, 'tableauBord'])->name('tableau-bord');
    });
    
    // API routes pour les cascades
    Route::prefix('api')->group(function () {
        Route::get('produits/{effet}', [ActiviteController::class, 'getProduits']);
        Route::get('actions/{produit}', [ActiviteController::class, 'getActions']);
    });
    
    // Gestion des fichiers
    Route::delete('fichiers/{fichier}', [ActiviteController::class, 'supprimerFichier'])->name('fichiers.supprimer');
});

// Routes d'authentification
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (auth()->guard('web')->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Les informations de connexion ne correspondent pas.',
    ]);
})->name('login.post');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');