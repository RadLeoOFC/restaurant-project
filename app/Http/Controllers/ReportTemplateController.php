<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;
use Illuminate\Http\Request;

class ReportTemplateController extends Controller
{
    public function index()
    {
        $templates = ReportTemplate::all();
        return view('report_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('report_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'filters' => 'nullable|json',
        ]);
    
        ReportTemplate::create([
            'name' => $request->name,
            'filters' => json_decode($request->filters, true), // декодируем строку в массив
        ]);
    
        return redirect()->route('report-templates.index')->with('success', 'Template created');
    }
    

    public function edit(ReportTemplate $reportTemplate)
    {
        return view('report_templates.edit', compact('reportTemplate'));
    }

    public function update(Request $request, ReportTemplate $reportTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'filters' => 'nullable|json',
        ]);
    
        $reportTemplate->update([
            'name' => $request->name,
            'filters' => json_decode($request->filters, true), // тоже декодируем
        ]);
    
        return redirect()->route('report-templates.index')->with('success', 'Template updated');
    }    

    public function destroy(ReportTemplate $reportTemplate)
    {
        $reportTemplate->delete();
        return redirect()->route('report-templates.index')->with('success', 'Template deleted');
    }
}

