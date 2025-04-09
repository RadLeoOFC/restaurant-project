@extends('layouts.app')

@section('title', __('messages.external_desks'))

@section('content')
    <div class="container mt-4">
        <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.external_desks') }}</h1>
        <a href="{{ route('external-desks.create') }}" class="btn btn-primary mb-3">{{ __('messages.add_external_desk') }}</a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.capacity') }}</th>
                    <th>{{ __('messages.coordinate_x') }}</th>
                    <th>{{ __('messages.coordinate_y') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($externalDesks as $desk)
                    <tr>
                        <td>{{ $desk->translated_name }}</td>
                        <td>{{ $desk->capacity }}</td>
                        <td>{{ $desk->coordinates_x }}</td>
                        <td>{{ $desk->coordinates_y }}</td>
                        <td>{{ __('messages.status_' . $desk->status) }}</td>
                        <td>
                            <a href="{{ route('external-desks.edit', $desk) }}" class="btn btn-sm btn-warning">{{ __('messages.edit') }}</a>
                            <form action="{{ route('external-desks.destroy', $desk) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
