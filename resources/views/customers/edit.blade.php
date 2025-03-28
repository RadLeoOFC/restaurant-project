@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 style="font-size: 30px; margin-bottom:20px">Edit Customer</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label d-block text-start">Name</label>
            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label d-block text-start">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $customer->email }}" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label d-block text-start">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}" required>
        </div>

        <div class="mb-3">
            <label for="preferred_language" class="form-label d-block text-start">Preferred Language</label>
            <input type="text" name="preferred_language" class="form-control" value="{{ $customer->preferred_language }}">
        </div>

        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back</a>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
