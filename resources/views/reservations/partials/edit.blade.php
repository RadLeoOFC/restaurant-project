<form action="{{ route('reservations.update', $reservation) }}" method="POST" id="modal-edit-reservation-form">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">{{ __('messages.desk') }}</label>
        <select name="desk_id" class="form-select" required>
            @foreach ($desks as $desk)
                <option value="{{ $desk->id }}" {{ $reservation->desk_id == $desk->id ? 'selected' : '' }}>
                    {{ $desk->name }} ({{ $desk->status }})
                </option>
            @endforeach
        </select>
    </div>

    @php $user = auth()->user(); @endphp

    @if ($user->hasRole('Admin'))
        <div class="mb-3">
            <label class="form-label">{{ __('messages.customer') }}</label>
            <select name="customer_id" class="form-select" required>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ $reservation->customer_id == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="mb-3">
        <label class="form-label">{{ __('messages.date') }}</label>
        <input type="date" name="reservation_date" class="form-control" value="{{ $reservation->reservation_date }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('messages.time') }}</label>
        <input type="time" name="reservation_time" class="form-control" value="{{ $reservation->reservation_time }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('messages.duration') }}</label>
        <select name="duration_hours" class="form-select" required>
            @for ($i = 2; $i <= 8; $i++)
                <option value="{{ $i }}" {{ $reservation->duration_hours == $i ? 'selected' : '' }}>
                    {{ $i }} {{ __('messages.hours') }}
                </option>
            @endfor
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('messages.status') }}</label>
        <select name="status" class="form-select">
            <option value="new" {{ $reservation->status === 'new' ? 'selected' : '' }}>{{ __('messages.status_new') }}</option>
            <option value="confirmed" {{ $reservation->status === 'confirmed' ? 'selected' : '' }}>{{ __('messages.status_confirmed') }}</option>
            <option value="cancelled" {{ $reservation->status === 'cancelled' ? 'selected' : '' }}>{{ __('messages.status_cancelled') }}</option>
        </select>
    </div>

    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-success">{{ __('messages.update') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
    </div>
</form>

<script>
    $('#modal-edit-reservation-form').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const action = form.attr('action');
        const data = form.serialize();

        $.ajax({
            url: action,
            method: 'POST',
            data: data,
            success: function () {
                location.reload(); // Обновить страницу после успешного обновления
            },
            error: function (xhr) {
                alert('Ошибка при обновлении бронирования');
                console.error(xhr.responseText);
            }
        });
    });
</script>
