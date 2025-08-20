<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Utilisateur extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone', 'matricule', 
        'fonction', 'structure_id', 'role', 'password', 'statut'
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function activitesSaisies()
    {
        return $this->hasMany(Activite::class, 'utilisateur_id');
    }

    public function validations()
    {
        return $this->hasMany(Activite::class, 'validateur_id');
    }

    public function realisationsTrimestrielles()
    {
        return $this->hasMany(RealisationTrimestrielle::class, 'utilisateur_id');
    }

    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDirecteur()
    {
        return in_array($this->role, ['admin', 'directeur']);
    }

    public function canValidate()
    {
        return in_array($this->role, ['admin', 'directeur', 'consultation']);
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }
}