@extends('layouts.app')

@section('title', __('messages.customer_list'))

@section('content')
<div class="container mt-4">
    <h1 style="font-size: 30px; margin-bottom:20px">
        {{ auth()->user()->hasRole('Admin') ? __('messages.customer_list') : __('messages.customer_profile') }}
    </h1>
    <a href="{{ route('customers.create') }}" class="btn btn-primary mb-3">
        {{ auth()->user()->hasRole('Admin') ? __('messages.add_customer') : __('messages.register_profile') }}
    </a>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.email') }}</th>
                <th>{{ __('messages.phone') }}</th>
                <th>{{ __('messages.preferred_language') }}</th>
                @if (auth()->user()->hasRole('Admin'))
                    <th>{{ __('messages.user') }}</th>
                @endif
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->preferred_language }}</td>
                    @if (auth()->user()->hasRole('Admin'))
                        <td>
                            @if ($customer->user)
                                {{ $customer->user->name }} ({{ $customer->user->email }})
                            @else
                                <em>{{ __('messages.no_user_attached') }}</em>
                            @endif
                        </td>
                    @endif
                    <td>
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm">{{ __('messages.edit') }}</a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">{{ auth()->user()->hasRole('Admin') ? __('messages.no_customers') : __('messages.no_profile') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
