<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Models\Language;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function index()
    {
        $translations = Translation::with('language')->get();
        return view('translations.index', compact('translations'));
    }

    public function create()
    {
        $languages = Language::all();
        return view('translations.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        Translation::create($validated);
        return redirect()->route('translations.index')->with('success', __('messages.external_desk_created'));
    }

    public function edit(Translation $translation)
    {
        $languages = Language::all();
        return view('translations.edit', compact('translation', 'languages'));
    }

    public function update(Request $request, Translation $translation)
    {
        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        $translation->update($validated);
        return redirect()->route('translations.index')->with('success', __('messages.external_desk_updated'));
    }

    public function destroy(Translation $translation)
    {
        $translation->delete();
        return redirect()->route('translations.index')->with('success', __('messages.external_desk_deleted'));
    }
}

