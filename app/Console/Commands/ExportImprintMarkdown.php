<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\VCardHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\GlobalController;
use Illuminate\Support\Facades\Schema;

class ExportImprintMarkdown extends Command
{
    protected $signature = 'export:imprint-markdown';
    protected $description = 'Exportiert die privacy-Tabelle als Markdown-Datei mit Inhaltsverzeichnis und Anchors';

    public function handle()
    {
        GlobalController::SetDomain();
        $tabs = ["ab_mcsl"
        ,"chh","dag_mcsl","mfx_mcsl","pna_mcsl"
        ];
        foreach($tabs as $table)
        {


        $sda = $table;
        $sdd = str_replace("_mcsl",'',$sda);
        $channel = '';
        if($sdd != "ab")
        {
            $channel = "_".$sdd;
        }
        if(!Schema::connection("mariadb")->hasTable("impressum"))
        {
            continue;
        }
        $entries = DB::connection("mariadb")->table('impressum')->where("pub", "1")->orderBy('id',"DESC")->get();

        if ($entries->isEmpty()) {
            $this->error('Keine Einträge in der Tabelle "privacy" gefunden.');
            return;
        }

        $markdown = '';

        // Inhaltsverzeichnis aufbauen
        $o = 1;
        // foreach ($entries as $entry) {
        //     $anchor = $entry->slug ?? Str::slug($entry->headline);
        //     $markdown .= "- [$o)&nbsp; $entry->headline](#{$anchor})<br />";
        //     $o++;
        // }

        // $markdown .= "\n---\n\n";

        // Abschnitte
        $i = 1;
        foreach ($entries as $entry) {
            $markdown .= "\n## {$entry->name}\n\n";
            $markdown .= ($this->convertToMarkdown($entry->details,$sdd)) . "";
            $markdown .= "\n";
            $markdown = $this->noemtyli($markdown);
            $i++;
        }
    // Datei speichern
        Storage::disk('md')->put('imprint_'.$sdd.'.md', $markdown);
        $this->info("Markdown-Datei wurde unter ressources/markdown/imprint_".$sdd.".md gespeichert.");
        }

    }
    protected function convertToMarkdown(string $details,$sdd): string
    {
        // Optional: HTML zu Markdown konvertieren
        // Hier sehr einfach gehalten – kann bei Bedarf z. B. mit `league/html-to-markdown` ersetzt werden

        $text = strip_tags($details, '<br><ul><div><ol><li><strong><h3><h4><h2><em><b><i><a>');

        $text = str_replace(['<br>', '<br/>', '<br />'], "\n", $text);
        $text = str_replace("{{ vcard }}",$this->vcard($sdd),$text);
        $text = str_replace("fx_impr_mcs_alt()",impr_mcs_alt($sdd),$text);
        $text = str_replace("fx_impr_mcs()",impr_mcs($sdd),$text);
        $text = str_replace("fx_gen_impr_head()",gen_impr_head($sdd),$text);


        return $text;
    }
    function vcard($dom)
    {
        $data = DB::table("genxlo.kontaktdaten")->where("dom",$dom)->first();
        $xx = VCardHelper::buildVCard((array) $data);


    return $xx;
    }
    function noemtyli($str)
    {
        return preg_replace('#<li>[\s\:marker<br\s*/?>]*</li>#i', '', $str);
    }
}
