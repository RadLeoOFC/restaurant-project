@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 style="font-size: 30px; margin-bottom:20px">{{ __('messages.add_report_template') }}</h2>
    <form action="{{ route('report-templates.store') }}" method="POST">
        @csrf

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>{{ __('messages.input_error') }}</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('messages.template_name') }}</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
            <label for="filters" class="form-label">{{ __('messages.filters_json_hint') }}</label>
            <textarea class="form-control" id="filters" name="filters" rows="4" placeholder='{
                "status": "confirmed"
                }'>{{ old('filters') }}</textarea>
        </div>

        <div>
            <a href="{{ route('report-templates.index') }}" class="btn btn-secondary">{{ __('messages.back_to_list') }}</a>
            <button type="submit" class="btn btn-success">{{ __('messages.create') }}</button>
        </div>
    </form>
</div>
@endsection
