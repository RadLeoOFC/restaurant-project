@extends('layouts.app')

@section('title', __('messages.desks'))

@section('content')
<div class="container mt-4">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.desk_list') }}</h1>

    <a href="{{ route('desks.create') }}" class="btn btn-primary mb-3">{{ __('messages.add_desk') }}</a>
    <a href="{{ route('desks.map') }}" class="btn btn-secondary mb-3">{{ __('messages.view_map') }}</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.capacity') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.coordinates') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($desks as $desk)
                <tr>
                    <td class="align-middle">{{ $desk->translated_name }}</td>
                    <td class="align-middle">{{ $desk->capacity }}</td>
                    <td class="align-middle">{{ __('messages.status_' . $desk->status) }}</td>
                    <td class="align-middle">({{ $desk->coordinates_x }}, {{ $desk->coordinates_y }})</td>
                    <td class="text-nowrap align-middle">
                        <a href="{{ route('desks.edit', $desk) }}" class="btn btn-warning btn-sm">{{ __('messages.edit') }}</a>
                        <form action="{{ route('desks.destroy', $desk) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.are_you_sure') }}')">
                                {{ __('messages.delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
