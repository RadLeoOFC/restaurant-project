@props(['desks', 'externalDesks'])



<div class="zoom-pan-wrapper" id="zoom-wrapper">
    <div class="desk-map-container" id="desk-map-container">
        @php
            $maxX = $desks->max('coordinates_x');
            $maxY = $desks->max('coordinates_y');
        @endphp
        <div id="desk-canvas" style="width: {{ ($maxX + 10) * 10 }}px; height: {{ ($maxY + 10) * 10 }}px;">
            @foreach($desks as $desk)
                @php
                    $scale = ceil($desk->capacity / 2);
                    $unitSize = 52;
                    $deskWidth = $unitSize * $scale;
                    $left = $desk->coordinates_x * 10 - ($deskWidth / 2);
                    $top = $desk->coordinates_y * 10;
                @endphp
                <div class="desk {{ $desk->status }}"
                    data-id="{{ $desk->id }}"
                    data-name="{{ $desk->name }}"
                    data-capacity="{{ $desk->capacity }}"
                    data-status="{{ $desk->status }}"
                    style="width: {{ $deskWidth }}px; left: {{ $left }}px; top: {{ $top }}px;">
                    {{ preg_replace('/[^0-9]/', '', $desk->name) }}
                </div>
            @endforeach
            @foreach($externalDesks as $desk)
                @php
                    $scale = ceil($desk->capacity / 2);
                    $unitSize = 52;
                    $deskWidth = $unitSize * $scale;
                    $left = $desk->coordinates_x * 10 - ($deskWidth / 2);
                    $top = $desk->coordinates_y * 10;
                @endphp
                <div class="desk external-desk {{ $desk->status }}"
                    data-id="{{ $desk->id }}"
                    data-name="{{ $desk->name }}"
                    data-capacity="{{ $desk->capacity }}"
                    data-status="{{ $desk->status }}"
                    style="width: {{ $deskWidth }}px; left: {{ $left }}px; top: {{ $top }}px;">
                    {{ preg_replace('/[^0-9]/', '', $desk->name) }}
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Reservation Modal (for Users) -->
<div id="reservation-modal" class="card shadow-sm position-fixed end-0 top-0 m-3 d-none" tabindex="-1">
    <div class="card-body">
        <form id="map-reservation-form" action="{{ route('reservations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="desk_id" id="reservation-desk-id">
            <div class="card-header">
                <h5 class="modal-title">
                    {{ __('messages.reserve_desk') }} <span id="reservation-desk-name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.date') }}</label>
                    <input type="date" name="reservation_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.time') }}</label>
                    <input type="time" name="reservation_time" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.duration') }}</label>
                    <select name="duration_hours" class="form-select">
                        @for ($i = 2; $i <= 8; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ __('messages.hours') }}</option>
                        @endfor
                    </select>
                </div>
                <input type="hidden" name="status" value="new">
            </div>
            <div id="reservation-warning" class="alert alert-danger d-none mt-2">
                {{ __('messages.desk_already_reserved') }}
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">{{ __('messages.reserve') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Admin Reservation Modal -->
<div id="add-reservation-panel" class="card position-fixed end-0 top-0 m-3 d-none shadow-lg" style="z-index: 1050; width: 320px;">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.reserve_desk') }}</h5>
        <form id="add-reservation-form" action="{{ route('reservations.store') }}" method="POST">
            @csrf
            <div class="mb-2">
                <label class="form-label">{{ __('messages.customer') }}</label>
                <select name="customer_id" class="form-select" required>
                    @foreach (\App\Models\Customer::all() as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>{{ __('messages.date') }}</label>
                <input type="date" name="reservation_date" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>{{ __('messages.time') }}</label>
                <input type="time" name="reservation_time" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>{{ __('messages.duration') }}</label>
                <select name="duration_hours" class="form-select">
                    @for ($i = 2; $i <= 8; $i++)
                        <option value="{{ $i }}">{{ $i }} {{ __('messages.hours') }}</option>
                    @endfor
                </select>
            </div>
            <div class="mb-2">
                <label>{{ __('messages.desk') }}</label>
                <select name="desk_id" class="form-select">
                    @foreach (\App\Models\Desk::all() as $desk)
                        <option value="{{ $desk->id }}">{{ $desk->name }} ({{ $desk->status }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>{{ __('messages.status') }}</label>
                <select name="status" class="form-select">
                    <option value="new">{{ __('messages.status_new') }}</option>
                    <option value="confirmed">{{ __('messages.status_confirmed') }}</option>
                    <option value="cancelled">{{ __('messages.status_cancelled') }}</option>
                </select>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button type="button" id="close-reservation-panel" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.reserve') }}</button>
            </div>
        </form>
    </div>
</div>


<style>
    .zoom-pan-wrapper {
        width: 100%;
        height: 80vh; /* Вместо фиксированных 600px */
        overflow: auto;
        border: 2px solid #ccc;
        position: relative;
        touch-action: pinch-zoom;
    }

    .desk-map-container {
        transform-origin: 0 0;
        position: absolute;
        top: 0;
        left: 0;
    }

    .desk {
        position: absolute;
        height: 52px;
        color: white;
        font-weight: bold;
        text-align: center;
        line-height: 52px;
        cursor: pointer;
        z-index: 1;
    }

    .desk.available { background: green; }
    .desk.occupied { background: red; }
    .desk.selected { background: orange; }

    .desk::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0);
            transition: background 0.3s ease;
            z-index: -1;
        }


        .desk:hover::before {
            background: rgba(255, 255, 255, 0.5);
        }


        .desk.active {
            z-index: 100;
        }

        .external-desk {
            background-color: #2196F3 !important; /* синий */
            border: 6px dashed white;
            cursor: default !important;
        }

        /* Сохраняем основной синий, но добавляем цветную рамку по статусу */
        .external-desk.available { border-color: #4CAF50 !important; }
        .external-desk.occupied { border-color: #F44336 !important; }
        .external-desk.selected { border-color: #FF9800 !important; }

        .modal.left-aligned .modal-dialog {
            margin-left: 0;
            margin-right: auto;
        }
</style>