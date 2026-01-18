<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'montant',
        'date_paiement',
        'statut',
        'collecteur_id',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'date_enregistrement' => 'datetime',
        'montant' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collecteur()
    {
        return $this->belongsTo(User::class, 'collecteur_id');
    }
}
