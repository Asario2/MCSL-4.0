<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index($table, $id = 0)
    {
        $selectedCategoryId = null;
        $selectedMediumId = null;

        if ($id) {
            if ($table === 'types') {
                $res = DB::table('types')
                    ->where('id', $id)
                    ->select('categorie_id')
                    ->first();

                $selectedCategoryId = $res?->categorie_id;
            } else {
                $res = DB::table($table)
                    ->where('id', $id)
                    ->select('categorie_id', 'type_id')
                    ->first();

                if ($res) {
                    $selectedCategoryId = $res->categorie_id;
                    $selectedMediumId = $res->type_id;
                }
            }
        }

        $categories = DB::table('categories')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                $category->types = DB::table('types')
                    ->where('categorie_id', $category->id)
                    ->orderBy('name')
                    ->get();

                return $category;
            });

        return response()->json([
            'categories' => $categories,
            'selected_categorie_id' => $selectedCategoryId,
            'selected_medium_id' => $selectedMediumId,
        ]);
    }
}   
