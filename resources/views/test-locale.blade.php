@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>{{ __('messages.admin_panel') }}</h1>
    <h2>Current locale: {{ app()->getLocale() }}</h2>
    <p>Session locale: {{ session('app_locale') }}</p>

    <p>Available languages:</p>
    <ul>
        @foreach(\App\Models\Language::all() as $lang)
            <li>{{ $lang->code }} - {{ $lang->name }}</li>
        @endforeach
    </ul>
</div>
@endsection
