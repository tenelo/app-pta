<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FichierJoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type', 'attachable_id', 'nom_fichier', 'nom_original',
        'type_mime', 'taille', 'chemin_fichier', 'description', 'utilisateur_id'
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->chemin_fichier);
    }

    public function getTailleFormatteeAttribute()
    {
        $bytes = $this->taille;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIconeAttribute()
    {
        $extension = strtolower(pathinfo($this->nom_original, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf': return 'file-pdf';
            case 'doc':
            case 'docx': return 'file-word';
            case 'xls':
            case 'xlsx': return 'file-excel';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif': return 'file-image';
            default: return 'file';
        }
    }
}