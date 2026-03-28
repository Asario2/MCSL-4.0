<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use Inertia\Inertia; // <-- Wichtig!


class FontController extends Controller
{
    public function index()
    {
        $path = public_path('toolz/fontz');

        $files = collect(glob($path . '/*.ttf'))
            ->map(fn($file) => basename($file))
            ->sort()
            ->values();

        return response()->json($files);
    }

    public function zip(Request $request)
    {
        $fonts = $request->input('fonts', []);

        if (empty($fonts)) {
            return response()->json(['error' => 'Keine Fonts'], 422);
        }

        $zipName = 'fonts_' . time() . '_' . uniqid() . '.zip';
        $zipPath = public_path('tmp/' . $zipName);

        if (!is_dir(public_path('tmp'))) {
            mkdir(public_path('tmp'), 0755, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            abort(500, 'ZIP Fehler');
        }

        $basePath = public_path('toolz/fontz/');

        foreach ($fonts as $font) {
            $safeFont = basename($font);
            $fullPath = $basePath . $safeFont;

            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, $safeFont);
            }
        }

        $zip->close();

        return response()->json([
            'url' => '/tmp/' . $zipName
        ]);
    }
    public function show()
    {
        // Lädt die Vue-Komponente 'FontPreview'
        return Inertia::render('Admin/FontPreview');
    }

}
