<?php

namespace App\Http\Controllers;

use App\Models\ExternalDesk;
use Illuminate\Http\Request;

class ExternalDeskController extends Controller
{
    public function index()
    {
        $externalDesks = ExternalDesk::all();
        return view('external_desks.index', compact('externalDesks'));
    }

    public function create()
    {
        return view('external_desks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,selected',
            'coordinates_x' => 'required|numeric',
            'coordinates_y' => 'required|numeric',
        ]);

        ExternalDesk::create($request->all());
        return redirect()->route('external-desks.index')->with('success', __('messages.external_desk_created'));
    }

    public function show(ExternalDesk $externalDesk)
    {
        return view('external_desks.show', compact('externalDesk'));
    }

    public function edit(ExternalDesk $externalDesk)
    {
        return view('external_desks.edit', compact('externalDesk'));
    }

    public function update(Request $request, ExternalDesk $externalDesk)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,selected',
            'coordinates_x' => 'required|numeric',
            'coordinates_y' => 'required|numeric',
        ]);
    
        $externalDesk->update($request->all());
    
        // 🟢 Возврат правильного ответа для AJAX
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
    
        return redirect()->route('external-desks.index')->with('success', __('messages.external_desk_updated'));
    }
    

    public function destroy(ExternalDesk $externalDesk)
    {
        $externalDesk->delete();
        return redirect()->route('external-desks.index')->with('success', __('messages.external_desk_deleted'));
    }

    public function saveAll(Request $request)
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $data = $request->input('desks', []);

        foreach ($data as $deskData) {
            if (!empty($deskData['id'])) {
                $desk = ExternalDesk::find($deskData['id']);
                if ($desk) {
                    $desk->update([
                        'coordinates_x' => $deskData['coordinates_x'],
                        'coordinates_y' => $deskData['coordinates_y'],
                    ]);
                }
            } else {
                ExternalDesk::create([
                    'name' => $deskData['name'],
                    'capacity' => $deskData['capacity'],
                    'status' => $deskData['status'],
                    'coordinates_x' => $deskData['coordinates_x'],
                    'coordinates_y' => $deskData['coordinates_y'],
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

}
