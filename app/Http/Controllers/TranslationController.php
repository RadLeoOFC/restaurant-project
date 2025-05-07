<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Models\Language;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $query = Translation::with('language');

        if ($request->filled('search')) {
            $query->where('key', 'LIKE', '%' . $request->search . '%');
        }

        $translations = $query->get();

        $groupedTranslations = $translations->groupBy('key')->map(function ($group, $key) {
            return [
                'key' => $key,
                'description' => optional($group->first())->description ?? '',
                'translations' => $group->mapWithKeys(function ($t) {
                    return [$t->language->code => $t->value];
                }),
            ];
        })->values();        

        return view('translations.index', compact('groupedTranslations'));
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->input('translations', []);
        $languages = Language::pluck('id', 'code'); // ['en' => 1, 'ru' => 2, ...]

        foreach ($data as $key => $item) {
            foreach ($languages as $code => $langId) {
                $value = $item[$code] ?? null;
                if ($value === null) continue;

                Translation::updateOrCreate(
                    ['key' => $key, 'language_id' => $langId],
                    ['value' => $value]
                );
            }
        }

        return redirect()->route('translations.index')->with('success', 'Translations saved.');
    }

    public function destroyKey($key)
    {
        Translation::where('key', $key)->delete();
        return redirect()->route('translations.index')->with('success', 'Translations deleted.');
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

