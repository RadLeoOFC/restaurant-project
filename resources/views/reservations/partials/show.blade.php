<div class="mb-3">
    <strong>{{ __('messages.customer') }}:</strong> {{ $reservation->customer->name }}<br>
    <strong>{{ __('messages.phone') }}:</strong> {{ $reservation->customer->phone ?? '—' }}<br>
    <strong>{{ __('messages.date') }}:</strong> {{ $reservation->reservation_date }}<br>
    <strong>{{ __('messages.time') }}:</strong> {{ $reservation->reservation_time }}<br>
    <strong>{{ __('messages.duration') }}:</strong> {{ $reservation->duration_hours }} {{ __('messages.hours') }}<br>
    <strong>{{ __('messages.status') }}:</strong> {{ __('messages.status_' . $reservation->status) }}<br>
</div>

<div class="d-flex justify-content-between">
    <button class="btn btn-primary open-reservation-edit" data-id="{{ $reservation->id }}">
        {{ __('messages.edit_reservation') }}
    </button>


    <form method="POST" action="{{ route('reservations.destroy', $reservation) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('messages.are_you_sure') }}')">
            {{ __('messages.delete') }}
        </button>
    </form>
</div>
