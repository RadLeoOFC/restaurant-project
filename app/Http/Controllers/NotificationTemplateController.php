<?php

namespace App\Http\Controllers;

use App\Models\NotificationTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index() {
        $templates = NotificationTemplate::all();
        return view('notification_templates.index', compact('templates'));
    }
    
    public function create() {
        return view('notification_templates.create');
    }
    
    public function store(Request $request) {
        $request->validate([
            'key' => 'required',
            'language_code' => 'required',
            'title' => 'nullable',
            'body' => 'required',
        ]);
    
        NotificationTemplate::create($request->all());
    
        return redirect()->route('notification-templates.index')->with('success', __('messages.template_created'));
    }
    
    public function edit(NotificationTemplate $notificationTemplate) {
        return view('notification_templates.edit', compact('notificationTemplate'));
    }
    
    public function update(Request $request, NotificationTemplate $notificationTemplate) {
        $request->validate([
            'key' => 'required',
            'language_code' => 'required',
            'title' => 'nullable',
            'body' => 'required',
        ]);
    
        $notificationTemplate->update($request->all());
    
        return redirect()->route('notification-templates.index')->with('success', __('messages.template_updated'));
    }
    
    public function destroy(NotificationTemplate $notificationTemplate) {
        $notificationTemplate->delete();
        return redirect()->route('notification-templates.index')->with('success', __('messages.template_deleted'));
    }  
    
}
