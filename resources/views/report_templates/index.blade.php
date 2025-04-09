@extends('layouts.app')

@section('title', __('messages.report_templates'))

@section('content')
<div class="container mt-4">
    <h2 style="font-size: 30px; margin-bottom:20px">{{ __('messages.report_templates') }}</h2>
    <a href="{{ route('report-templates.create') }}" class="btn btn-primary mb-3">{{ __('messages.create_new_template') }}</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('messages.template_name') }}</th>
                <th>{{ __('messages.filters') }}</th>
                <th>{{ __('messages.created_at') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($templates as $template)
                <tr>
                    <td>{{ \App\Models\Translation::getValue('confirmed_reservations' . strtolower($template->template_name)) }}</td>
                    <td><pre class="mb-0">{{ json_encode($template->filters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></td>
                    <td>{{ $template->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('report-templates.edit', $template) }}" class="btn btn-sm btn-warning">{{ __('messages.edit') }}</a>
                        <form action="{{ route('report-templates.destroy', $template) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')">{{ __('messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">{{ __('messages.no_templates_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
