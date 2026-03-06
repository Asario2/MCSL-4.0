<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // Tabelle explizit, falls nicht standardmäßig 'contacts'
    protected $table = 'contacts';

    // Felder, die massenweise zuweisbar sind
    protected $fillable = [
        'Name',
        'Vorname',
        'Nachname',
        'Email',
        'Telefon',
        'Handy',
        'Gruppe',
        'Kommentar',
        'Adresse',
        'Geburtsdatum',
        'ripdate',
        'us_poster',
    ];

    // Optional: Datum-Felder als Carbon behandeln
    protected $dates = [
        'Geburtsdatum',
        'ripdate',
        'created_at',
        'updated_at',
    ];

    // Primärschlüssel explizit, falls nötig
    // protected $primaryKey = 'id';

    // Optional: wenn kein auto-increment oder kein timestamps
    // public $incrementing = true;
    // public $timestamps = true;
}
