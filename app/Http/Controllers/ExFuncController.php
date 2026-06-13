<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExFuncController extends Controller
{
    public function premium_fx()
    {
        return '<div class="border border-gray-300 dark:border-gray-700 text-center max-w-xl mx-auto my-8 p-8"> ⭐⭐⭐ <strong>MCSL-Points.</strong> ⭐⭐⭐<br> Registrierte Nutzer sammeln Punkte, indem sie lesen, bleiben und wiederkommen. Die Punkte sind kein Spielzeug. Sie haben einen Zweck. Man kann sie gegen gemalte Bilder eintauschen. Echte Arbeiten. Kein Zufall.<br><br> Punkte bekommst du z. B. durch: <ul class="list-disc list-inside mt-4 text-left"><li>Bilder bewerten <b>(1 Punkt)</b></li><li>Kommentare schreiben <b>(3 Punkte)</b></li><li>Wörter für Shortpoems vorschlagen <b>(5 Punkte)</b></li><li>Newsletter lesen <b>(8 Punkte)</b></li></ul> Wer mehr wissen will, findet weitere Informationen über diesen <a class="as font-semibold" href="/about/mcs-points">link</a>. </div>';
    }
}
