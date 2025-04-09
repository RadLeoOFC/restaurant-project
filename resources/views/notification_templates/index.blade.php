@extends('layouts.app')

@section('title', __('messages.notification_templates'))

@section('content')
<div class="container">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.notification_templates') }}</h1>
    <a href="{{ route('notification-templates.create') }}" class="btn btn-primary">{{ __('messages.add_template') }}</a>

    @foreach ($templates as $template)
        <div style="margin-top:20px; border:1px solid #ccc; padding:10px;">
            <strong>{{ $template->key }} ({{ $template->language_code }})</strong><br>
            <em>{{ $template->title }}</em><br>
            {{ $template->body }}<br>
            <a href="{{ route('notification-templates.edit', $template) }}" class="btn btn-warning btn-sm">{{ __('messages.edit') }}</a>
            <form action="{{ route('notification-templates.destroy', $template) }}" method="POST" style="display:inline-block;">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
