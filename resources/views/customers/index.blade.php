@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 style="font-size: 30px; margin-bottom:20px">Customer List</h1>
    <a href="{{ route('customers.create') }}" class="btn btn-primary mb-3">Add Customer</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Preferred Language</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->preferred_language }}</td>
                    <td>
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No customers found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
