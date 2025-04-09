@extends('layouts.app')

@section('title', __('messages.languages'))

@section('content')
    <div class="container">
        <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.languages') }}</h1>

        <a href="{{ route('languages.create') }}" class="btn btn-primary mb-3">{{ __('messages.add_language') }}</a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('messages.code') }}</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($languages as $language)
                    <tr>
                        <td>{{ $language->code }}</td>
                        <td>{{ $language->name }}</td>
                        <td>
                            <a href="{{ route('languages.edit', $language) }}" class="btn btn-sm btn-warning">{{ __('messages.edit') }}</a>
                            <form action="{{ route('languages.destroy', $language) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
