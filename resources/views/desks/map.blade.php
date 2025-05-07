@extends('layouts.app')

@section('title', __('messages.desk_map'))

@section('content')

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=yes">

<div class="container mt-4">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.desk_layout') }}</h1>

    <a href="{{ route('desks.index') }}" class="btn btn-secondary mb-3">{{ __('messages.back_to_list') }}</a>
    @if(auth()->user()->hasRole('Admin'))
        <a href="{{ route('desks.snapshot') }}" class="btn btn-warning mb-3">{{ __('messages.save_snapshot') }}</a>
        <a href="javascript:void(0);" onclick="resetToTodaySnapshot()" class="btn btn-danger mb-3 ms-2">{{ __('messages.reset_map') }}</a>
    @endif

    <div class="row">
        <div class="col-md-9"> <!-- Сначала карта -->
            <div class="zoom-pan-wrapper" id="zoom-wrapper">
                <div class="desk-map-container" id="desk-map-container">
                    @php
                        $maxX = $desks->max('coordinates_x');
                        $maxY = $desks->max('coordinates_y');
                    @endphp
                    <div id="desk-canvas" style="width: {{ ($maxX + 10) * 10 }}px; height: {{ ($maxY + 10) * 10 }}px;">
                        @foreach($desks as $desk)
                            @php
                                $unitCount = ceil($desk->capacity / 2);
                                $unitSize = 60;
                                $spacing = 5;
                                $leftStart = $desk->coordinates_x * 10 - (($unitSize + $spacing) * $unitCount - $spacing) / 2;
                                $top = $desk->coordinates_y * 10;
                                $statusClass = in_array($desk->status, ['occupied', 'selected']) ? $desk->status : 'available';
                            @endphp

                            <div class="desk-group {{ $statusClass }}"
                                style="left: {{ $leftStart }}px; top: {{ $top }}px;"
                                data-id="{{ $desk->id }}"
                                data-type="desk"
                                data-name="{{ $desk->name }}"
                                data-capacity="{{ $desk->capacity }}"
                                data-status="{{ $desk->status }}">
                                @for ($i = 0; $i < $unitCount; $i++)
                                    <div class="desk-unit {{ $statusClass }}">
                                        @if ($i == 0)
                                            <span class="desk-label">{{ preg_replace('/[^0-9]/', '', $desk->name) }}</span>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        @endforeach

                        @foreach($externalDesks as $desk)
                            @php
                                $unitCount = ceil($desk->capacity / 2);
                                $unitSize = 60;
                                $spacing = 5;
                                $leftStart = $desk->coordinates_x * 10 - (($unitSize + $spacing) * $unitCount - $spacing) / 2;
                                $top = $desk->coordinates_y * 10;
                                $statusClass = in_array($desk->status, ['occupied', 'selected']) ? $desk->status : 'available';
                            @endphp

                            <div class="desk-group {{ $statusClass }}"
                                style="left: {{ $leftStart }}px; top: {{ $top }}px;"
                                data-id="{{ $desk->id }}"
                                data-type="external"
                                data-name="{{ $desk->name }}"
                                data-capacity="{{ $desk->capacity }}"
                                data-status="{{ $desk->status }}">
                                @for ($i = 0; $i < $unitCount; $i++)
                                    <div class="desk-unit external-desk-unit {{ $statusClass }}">
                                        @if ($i == 0)
                                            <span class="desk-label">{{ preg_replace('/[^0-9]/', '', $desk->name) }}</span>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->hasRole('Admin'))
            <div class="col-md-3"> <!-- Потом форма -->
                <div id="desk-form-panel" class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('messages.desk_details') }}</h5>
                        <form id="desk-form" method="POST">
                            @csrf
                            <input type="hidden" name="desk_id" id="desk-id">
                            <div class="mb-2">
                                <label>{{ __('messages.name') }}</label>
                                <input type="text" name="name" id="desk-name" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label>{{ __('messages.capacity') }}</label>
                                <input type="number" name="capacity" id="desk-capacity" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label>{{ __('messages.status') }}</label>
                                <select name="status" id="desk-status" class="form-select">
                                    <option value="available">{{ __('messages.status_available') }}</option>
                                    <option value="occupied">{{ __('messages.status_occupied') }}</option>
                                    <option value="selected">{{ __('messages.status_selected') }}</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>{{ __('messages.type') }}</label>
                                <div class="custom-select-container">
                                    <div class="custom-select-display" id="select-display">
                                        <img id="selected-icon" src="/images/desk-green.png" alt="Selected Icon" width="24" height="24">
                                        <span id="selected-text">{{ __('messages.normal_desk') }}</span>
                                    </div>
                                    <ul class="custom-select-options" id="options-list" style="display: none;">
                                        <li data-value="normal" data-icon="/images/desk-green.png">
                                            <img src="/images/desk-green.png" width="24" height="24">
                                            {{ __('messages.normal_desk') }}
                                        </li>
                                        <li data-value="external" data-icon="/images/external_desk-green.png">
                                            <img src="/images/external_desk-green.png" width="24" height="24">
                                            {{ __('messages.external_desk') }}
                                        </li>
                                    </ul>
                                    <input type="hidden" name="type" id="desk-type" value="normal">
                                </div>
                            </div>
                            <button type="button" id="add-desk-btn" class="btn btn-primary">{{ __('messages.add_desk') }}</button>
                            <button type="button" id="save-desk-btn" class="btn btn-success">{{ __('messages.save') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Edit Modal -->
<div id="edit-desk-modal" class="modal fade left-aligned" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-desk-form" data-external="false">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.edit_desk') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-desk-id">
                    <input type="hidden" id="edit-desk-coordinates-x">
                    <input type="hidden" id="edit-desk-coordinates-y">

                    <label for="edit-desk-name">{{ __('messages.name') }}:</label>
                    <input type="text" id="edit-desk-name" class="form-control">

                    <label for="edit-desk-capacity">{{ __('messages.capacity') }}:</label>
                    <input type="number" id="edit-desk-capacity" class="form-control">

                    <label for="edit-desk-status">{{ __('messages.status') }}:</label>
                    <select id="edit-desk-status" class="form-control">
                        <option value="available">{{ __('messages.status_available') }}</option>
                        <option value="occupied">{{ __('messages.status_occupied') }}</option>
                        <option value="selected">{{ __('messages.status_selected') }}</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
                    <button type="button" id="delete-desk-btn" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reservation Modal (for Users) -->
<div id="reservation-modal" class="modal fade left-aligned" tabindex="-1">
    <div class="modal-dialog">
        <form id="map-reservation-form" action="{{ route('reservations.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="desk_id" id="reservation-desk-id">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ __('messages.reserve_desk') }} <span id="reservation-desk-name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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

            <div class="modal-footer">
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
                <label>{{ __('messages.type') }}</label>
                <div class="custom-select-container">
                    <div class="custom-select-display">
                        <img id="selected-icon" src="/images/desk-green.png" alt="Selected Icon" width="24" height="24">
                        <span id="selected-text">{{ __('messages.normal_desk') }}</span>
                    </div>
                    <ul class="custom-select-options">
                        <li data-value="normal" data-icon="/images/desk-green.png">{{ __('messages.normal_desk') }}</li>
                        <li data-value="external" data-icon="/images/external_desk-green.png">{{ __('messages.external_desk') }}</li>
                    </ul>
                    <input type="hidden" name="type" id="desk-type" value="normal">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button type="button" id="close-reservation-panel" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-success">{{ __('messages.reserve') }}</button>
            </div>
        </form>
    </div>
</div>


<style>
        /* Обертка и холст */
        .zoom-pan-wrapper {
            width: 100%;
            height: 80vh;
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
        #desk-canvas {
            position: relative;
        }

        /* Группа столов */
        .desk-group {
            position: absolute;
            display: flex;
            gap: 0;
            cursor: pointer;
            min-height: 60px;
        }

        /* Визуальный блок каждого места */
        .desk-unit {
            width: 60px;
            height: 60px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
        }

        /* Цвета статусов через изображения */
        .desk-unit.available {
            background-image: url('/images/desk-green.png');
        }
        .desk-unit.occupied {
            background-image: url('/images/desk-red.png');
        }
        .desk-unit.selected {
            background-image: url('/images/desk-orange.png');
        }

        /* Метка номера стола */
        .desk-label {
            position: absolute;
            top: -18px;
            left: 0;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            background: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            box-shadow: 0 0 2px rgba(0,0,0,0.1);
        }

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

        .external-desk-unit {
            width: 50px;
            height: 60px;
            border: none;
            background-color: transparent;
            background-size: 100% 100%; /* или cover */
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        .external-desk-unit.available {
            background-image: url('/images/external_desk-green.png');
        }
        .external-desk-unit.occupied {
            background-image: url('/images/external_desk-red.png');
        }
        .external-desk-unit.selected {
            background-image: url('/images/external_desk-orange.png');
        }

        .modal.left-aligned .modal-dialog {
            margin-left: 0;
            margin-right: auto;
        }

        .desk.selected {
            background-color: orange !important;
        }

        .desk-type-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: url('/images/desk-green.png') no-repeat 5px center;
            background-size: 24px;
            padding-left: 35px;
            height: 40px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding-right: 10px;
        }

        .desk-type-select option[value="external"] {
            background: url('/images/external_desk-green.png') no-repeat 5px center;
            background-size: 24px;
        }

        .custom-select-container {
            position: relative;
            width: 100%;
            cursor: pointer;
            user-select: none;
        }

        .custom-select-display {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            padding: 8px;
            background-color: #fff;
            border-radius: 4px;
            justify-content: space-between;
            cursor: pointer;
        }

        .custom-select-options {
            display: none; /* скрыто по умолчанию */
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 5px;
            padding: 0;
            list-style-type: none;
            z-index: 1000;
        }

        .custom-select-options li {
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .custom-select-options li:hover {
            background-color: #f0f0f0;
        }

        .custom-select-options li img {
            width: 24px;
            height: 24px;
        }

</style>

<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

    // Reset current map to today's snapshot
    function resetToTodaySnapshot() {
        $.post('/snapshots/reset', {
            _token: "{{ csrf_token() }}"
        })
        .done(function (res) {
            console.log('✅ Server response:', res);

            if (res.success) {
                alert("✔️ Today's snapshot has been successfully applied.\n\nPlease refresh the page (F5 or Ctrl+R) to see the updated desk layout.");
            } else {
                alert("❌ Reset failed (server error).");
            }
        })
        .fail(function (xhr, status, error) {
            console.error('❌ Reset error:', status, error);
            console.log('Server response:', xhr.responseText);
            alert("An error occurred while trying to reset the configuration.");
        });
    }



    document.addEventListener('DOMContentLoaded', () => {
        const isAdmin = @json(auth()->user()->hasRole('Admin'));

        let scale = 1, panX = 0, panY = 0;
        let isPanning = false, startX = 0, startY = 0;

        const wrapper = document.getElementById('zoom-wrapper');
        const mapContainer = document.getElementById('desk-map-container');

        function updateTransform() {
            mapContainer.style.transform = `translate(${panX}px, ${panY}px) scale(${scale})`;
        }

        wrapper.addEventListener('wheel', e => {
            e.preventDefault();
            const delta = e.deltaY < 0 ? 0.1 : -0.1;
            scale = Math.max(0.3, scale + delta);
            updateTransform();
        });

        wrapper.addEventListener('touchstart', e => {
            if (!isDraggingDesk) {
                isPanning = true;
                startX = e.touches[0].clientX - panX;
                startY = e.touches[0].clientY - panY;
            }
        });


        let initialPinchDistance = null;
        let lastScale = 1;

        wrapper.addEventListener('touchstart', function (e) {
            if (e.touches.length === 1 && !isDraggingDesk) {
                isPanning = true;
                startX = e.touches[0].clientX - panX;
                startY = e.touches[0].clientY - panY;
            } else if (e.touches.length === 2) {
                isPanning = false;
                initialPinchDistance = null;
            }
        }, { passive: false });

        wrapper.addEventListener('touchmove', function (e) {
            if (e.touches.length === 2) {
                // 👇 pinch zoom
                e.preventDefault();

                const dx = e.touches[0].clientX - e.touches[1].clientX;
                const dy = e.touches[0].clientY - e.touches[1].clientY;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (initialPinchDistance === null) {
                    initialPinchDistance = distance;
                } else {
                    const scaleChange = distance / initialPinchDistance;
                    scale = Math.max(0.3, Math.min(3, lastScale * scaleChange));
                    updateTransform();
                }
            } else if (e.touches.length === 1 && isPanning && !isDraggingDesk) {
                // 👇 pan
                panX = e.touches[0].clientX - startX;
                panY = e.touches[0].clientY - startY;
                updateTransform();
            }
        }, { passive: false });

        wrapper.addEventListener('touchend', function (e) {
            if (e.touches.length === 0) {
                isPanning = false;
                lastScale = scale;
                initialPinchDistance = null;
            }
        });

        wrapper.addEventListener('touchend', () => isPanning = false);


        wrapper.addEventListener('mousemove', e => {
            if (!isPanning) return;
            panX = e.clientX - startX;
            panY = e.clientY - startY;
            updateTransform();
        });

        wrapper.addEventListener('mouseup', () => isPanning = false);
        wrapper.addEventListener('mouseleave', () => isPanning = false);

        let isDraggingDesk = false;

        wrapper.addEventListener('mousedown', e => {
            if (!isDraggingDesk) {
                isPanning = true;
                startX = e.clientX - panX;
                startY = e.clientY - panY;
            }
        });

        let pendingDeskMove = null;

        if (isAdmin) {
            interact('.desk-group').draggable({
                listeners: {
                    start(event) {
                        isDraggingDesk = true;
                        wrapper.style.pointerEvents = 'none';

                        const target = event.target;
                        target.dataset.originalLeft = target.style.left;
                        target.dataset.originalTop = target.style.top;
                    },
                    move(event) {
                        const target = event.target;
                        const dx = event.dx / scale;
                        const dy = event.dy / scale;

                        const currentLeft = parseFloat(target.style.left) || 0;
                        const currentTop = parseFloat(target.style.top) || 0;

                        target.style.left = `${currentLeft + dx}px`;
                        target.style.top = `${currentTop + dy}px`;
                    },
                    end(event) {
                        isDraggingDesk = false;
                        wrapper.style.pointerEvents = 'auto';

                        const t = event.target;
                        const id = t.dataset.id;
                        const left = parseFloat(t.style.left);
                        const top = parseFloat(t.style.top);
                        const width = parseFloat(t.style.width);
                        const coordX = Math.round((left + width / 2) / 10);
                        const coordY = Math.round(top / 10);

                        pendingDeskMove = {
                            element: t,
                            id,
                            coordX,
                            coordY,
                            originalLeft: t.dataset.originalLeft,
                            originalTop: t.dataset.originalTop
                        };

                        $('#desk-move-panel').removeClass('d-none');
                    }
                }
            });
        }

        const openReservationPanelButton = document.getElementById('open-reservation-panel');
        if (openReservationPanelButton) {
            openReservationPanelButton.addEventListener('click', () => {
                document.getElementById('add-reservation-panel').classList.remove('d-none');
            });
        }

        const closeReservationPanelButton = document.getElementById('close-reservation-panel');
        if (closeReservationPanelButton) {
            closeReservationPanelButton.addEventListener('click', () => {
                document.getElementById('add-reservation-panel').classList.add('d-none');
            });
        }

        // Закрытие панели
        $('#close-reservation-panel').on('click', function () {
            $('#add-reservation-panel').addClass('d-none');

            // Убрать выделение при закрытии модалки
            $('.desk').removeClass('selected');
        });

        $('#save-desk-move').on('click', () => {
            if (!pendingDeskMove) return;

            const { id, coordX, coordY } = pendingDeskMove;

            $.ajax({
                url: `/desks/${id}`,
                type: 'PUT',
                data: {
                    _token: "{{ csrf_token() }}",
                    coordinates_x: coordX,
                    coordinates_y: coordY
                },
                success: res => {
                    if (!res.success) alert("Failed to save desk.");
                    $('#desk-move-panel').addClass('d-none');
                    pendingDeskMove = null;
                    clearTimeout(window._autoResetTimeout);
                },
                error: () => alert("Error saving desk.")
            });
        });


        $('#cancel-desk-move').on('click', () => {
            if (!pendingDeskMove) return;

            const { element, originalLeft, originalTop } = pendingDeskMove;
            element.style.left = originalLeft;
            element.style.top = originalTop;

            $('#desk-move-panel').addClass('d-none');
            pendingDeskMove = null;
        });

        document.querySelectorAll('.desk-group').forEach(desk => {
            desk.addEventListener('click', () => {
                const isExternal = desk.classList.contains('external-desk');
                const id = desk.dataset.id;
                const name = desk.dataset.name;
                const capacity = desk.dataset.capacity;
                const status = desk.dataset.status;
                const left = parseFloat(desk.style.left) || 0;
                const top = parseFloat(desk.style.top) || 0;
                const width = parseFloat(desk.style.width) || 52;
                const coordX = Math.round((left + width / 2) / 10);
                const coordY = Math.round(top / 10);

                if (isAdmin) {
                    const reservationPanelVisible = !document.getElementById('add-reservation-panel')?.classList.contains('d-none');

                    if (reservationPanelVisible) {
                        const select = document.querySelector('#add-reservation-form select[name="desk_id"]');
                        if (select) select.value = id;
                    } else {
                        // Админ редактирует любой стол
                        $('#edit-desk-id').val(id);
                        $('#edit-desk-name').val(name);
                        $('#edit-desk-capacity').val(capacity);
                        $('#edit-desk-status').val(status);
                        $('#edit-desk-coordinates-x').val(coordX);
                        $('#edit-desk-coordinates-y').val(coordY);

                        new bootstrap.Modal(document.getElementById('edit-desk-modal')).show();
                    }
                } else {
                    if (isExternal) return; // ❌ обычному пользователю запрещено

                    // ✅ пользователь бронирует обычный стол
                    $('#reservation-desk-id').val(id);

                    const number = name.replace(/[^\d]/g, '');
                    const translatedName = `{{ __('messages.desk_number') }} №${number}`;
                    $('#reservation-desk-name').text(translatedName);

                    $.post('/desks/select', {
                        _token: '{{ csrf_token() }}',
                        desk_id: id
                    });

                    new bootstrap.Modal(document.getElementById('reservation-modal')).show();
                }
            });
        });

        document.getElementById('add-desk-btn').addEventListener('click', () => {
            const name = document.getElementById('desk-name').value.trim();
            const capacity = parseInt(document.getElementById('desk-capacity').value);
            const status = document.getElementById('desk-status').value;
            const type = document.getElementById('desk-type').value;

            if (!name || isNaN(capacity)) {
                alert('Введите имя и вместимость');
                return;
            }

            const unitSize = 60;
            const spacing = 5;
            const unitCount = Math.ceil(capacity / 2);
            const deskWidth = (unitSize + spacing) * unitCount - spacing;

            const deskDiv = document.createElement('div');
            deskDiv.className = `desk-group ${status}`;
            deskDiv.style.position = 'absolute';
            deskDiv.style.left = '0px';
            deskDiv.style.top = '0px';
            deskDiv.style.width = `${deskWidth}px`;

            // ✅ различаем по типу
            deskDiv.dataset.name = name;
            deskDiv.dataset.capacity = capacity;
            deskDiv.dataset.status = status;
            deskDiv.dataset.type = type;
            deskDiv.dataset.new = "true";

            if (type === 'external') {
                deskDiv.dataset.external = "true";
            } else {
                deskDiv.dataset.normal = "true";
            }

            for (let i = 0; i < unitCount; i++) {
                const unitDiv = document.createElement('div');
                unitDiv.className = `${type === 'external' ? 'external-desk-unit' : 'desk-unit'} ${status}`;

                if (i === 0) {
                    const label = document.createElement('span');
                    label.className = 'desk-label';
                    label.innerText = name.replace(/[^\d]/g, '') || 'N';
                    unitDiv.appendChild(label);
                }

                deskDiv.appendChild(unitDiv);
            }

            document.getElementById('desk-map-container').appendChild(deskDiv);
            enableDrag(deskDiv);
        });

        // Включение перетаскивания
        function enableDrag(el) {
            interact(el).draggable({
                listeners: {
                    move(event) {
                        const dx = event.dx / scale;
                        const dy = event.dy / scale;
                        const left = parseFloat(el.style.left || 0);
                        const top = parseFloat(el.style.top || 0);
                        el.style.left = `${left + dx}px`;
                        el.style.top = `${top + dy}px`;
                    }
                }
            });
        }

        // Включить перетаскивание для всех текущих столов
        document.querySelectorAll('.desk-group').forEach(enableDrag);

        // Сохранение всех столов
        document.getElementById('save-desk-btn').addEventListener('click', () => {
            const desks = document.querySelectorAll('.desk-group');
            const normalDesks = [];
            const externalDesks = [];

            desks.forEach(desk => {
                const left = parseFloat(desk.style.left);
                const top = parseFloat(desk.style.top);
                const width = parseFloat(desk.offsetWidth);
                const coordX = Math.round((left + width / 2) / 10);
                const coordY = Math.round(top / 10);

                const payload = {
                    name: desk.dataset.name,
                    capacity: desk.dataset.capacity,
                    status: desk.dataset.status,
                    coordinates_x: coordX,
                    coordinates_y: coordY
                };

                // ✅ теперь ID привязаны к типу
                const type = desk.dataset.type;

                if (desk.dataset.id) {
                    payload.id = desk.dataset.id;
                    if (type === 'external') {
                        externalDesks.push(payload);
                    } else {
                        normalDesks.push(payload);
                    }
                } else {
                    if (type === 'external') {
                        externalDesks.push(payload);
                    } else {
                        normalDesks.push(payload);
                    }
                }
            });

            Promise.all([
                fetch('/desks/save-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ desks: normalDesks })
                }),
                fetch('/external-desks/save-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ desks: externalDesks })
                })
            ])
            .then(() => location.reload())
            .catch(err => {
                console.error('Ошибка при сохранении столов:', err);
                alert('Ошибка при сохранении.');
            });
        });

        function updateMapForFutureTime() {
            const date = $('input[name="reservation_date"]').val();
            const time = $('input[name="reservation_time"]').val();
            const duration = $('select[name="duration_hours"]').val();

            if (!date || !time || !duration) return;

            $.get('/desks/future-statuses', {
                reservation_date: date,
                reservation_time: time,
                duration_hours: duration
            }, function (response) {
                response.forEach(({ id, status }) => {
                    const desk = document.querySelector(`.desk[data-id="${id}"]`);
                    if (!desk) return;

                    desk.classList.remove('available', 'occupied', 'selected');
                    desk.classList.add(status);
                    desk.dataset.status = status;
                });
            });
        }

        // Подключить обработчик:
        $('input[name="reservation_date"], input[name="reservation_time"], select[name="duration_hours"]').on('change', updateMapForFutureTime);


        $('#edit-desk-form').on('submit', function (e) {
            e.preventDefault();

            const id = $('#edit-desk-id').val();
            const isExternal = String($('#edit-desk-form').data('external')) === 'true'; 
            const url = isExternal ? `/external-desks/${id}?_method=PUT` : `/desks/${id}`;

            const data = {
                _token: "{{ csrf_token() }}",
                name: $('#edit-desk-name').val(),
                capacity: $('#edit-desk-capacity').val(),
                status: $('#edit-desk-status').val(),
                coordinates_x: $('#edit-desk-coordinates-x').val(),
                coordinates_y: $('#edit-desk-coordinates-y').val()
            };

            $.ajax({
                url,
                type: 'PUT',
                data,
                success: res => res.success ? location.reload() : alert("Failed to update."),
                error: () => alert("Error saving desk.")
            });
        });

        $('#delete-desk-btn').on('click', function () {
            const id = $('#edit-desk-id').val();
            if (confirm("Are you sure to delete this desk?")) {
                $.ajax({
                    url: `/desks/${id}`,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: res => res.success ? location.reload() : alert("Failed to delete."),
                    error: () => alert("Error deleting desk.")
                });
            }
        });

        // Load all available snapshot dates and populate dropdown
        function loadSnapshotDates() {
            $.get('/snapshots/list', function (dates) {
                // Удалить предыдущий селект, если он есть
                $('#snapshot-date-select').remove();

                const select = $('<select id="snapshot-date-select" class="form-select mb-3 me-2" style="width:auto; display:inline-block;"></select>');
                select.append('<option disabled value="" selected>{{ __('messages.choose_snapshot_date') }}</option>');

                dates.forEach(item => {
                    select.append(`<option value="${item.snapshot_date}">${item.snapshot_date}</option>`);
                });

                // Вставка: один раз перед .btn-warning
                $('.btn-warning').first().before(select);

                let snapshotLoadedManually = false;

                // Обработчик при выборе даты
                select.on('change', function () {
                    const selectedDate = $(this).val();
                    if (selectedDate) {
                        loadSnapshotByDate(selectedDate);
                    }
                });

            });
        }


        // Load snapshot data by selected date
        function loadSnapshotByDate(date) {
            $.post('/snapshots/load', {
                _token: "{{ csrf_token() }}",
                snapshot_date: date
            }, function (desks) {
                // 👇 Откладываем отрисовку, чтобы DOM успел завершить предыдущий ререндер
                setTimeout(() => {
                    updateDeskPositions(desks);
                }, 0);
            });
        }


        // Apply new coordinates to desks on map
        function updateDeskPositions(desks) {
            if (typeof desk.coordinates_x !== 'number' || typeof desk.coordinates_y !== 'number') return;
            desks.forEach(desk => {
                const elements = document.querySelectorAll(`#desk-canvas .desk[data-id='${desk.desk_id}']`);

                elements.forEach(element => {
                    const capacity = Math.max(1, parseInt(desk.capacity) || 1); //  обязательно минимум 1
                    const deskWidth = 52 * Math.ceil(capacity / 2); // гарантируем > 0

                    // Корректный пересчет
                    const left = desk.coordinates_x * 10 - deskWidth / 2;
                    const top = desk.coordinates_y * 10;

                    element.style.left = `${left}px`;
                    element.style.top = `${top}px`;

                    // Обновление атрибутов
                    element.dataset.capacity = capacity;
                    element.dataset.name = desk.name;

                    if (desk.status) {
                        element.dataset.status = desk.status;
                        element.classList.remove('available', 'occupied', 'selected');
                        element.classList.add(desk.status);
                    }

                    element.textContent = desk.name.replace(/[^\d]/g, '');
                });
            });

        }


        // Call snapshot dropdown loader on page load
        loadSnapshotDates();

        // ✅ Проверка конфликта бронирования при отправке формы
        $('#map-reservation-form').on('submit', function (e) {
            e.preventDefault();

            const $form = $(this);
            const deskId = $('#reservation-desk-id').val();
            const date = $('input[name="reservation_date"]').val();
            const time = $('input[name="reservation_time"]').val();
            const duration = $('select[name="duration_hours"]').val();
            const token = '{{ csrf_token() }}';

            if (!date || !time || !duration) {
                $('#reservation-warning').removeClass('d-none').text('{{ __("messages.fill_all_fields") }}');
                return;
            }

            // Предварительная проверка конфликта
            $.get('/reservations/check-conflict', {
                desk_id: deskId,
                reservation_date: date,
                reservation_time: time,
                duration_hours: duration
            }).done(function (res) {
                if (res.conflict) {
                    $('#reservation-warning')
                        .removeClass('d-none')
                        .text("{{ __('messages.desk_already_reserved') }}");
                } else {
                    // Отправляем AJAX POST на бронирование
                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: {
                            _token: token,
                            desk_id: deskId,
                            reservation_date: date,
                            reservation_time: time,
                            duration_hours: duration,
                            status: 'new'
                        },
                        success: function (response) {
                            $('#reservation-warning').addClass('d-none');
                            location.reload(); // обновляем карту
                        },
                        error: function (xhr) {
                            let msg = 'Ошибка бронирования.';
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            $('#reservation-warning')
                                .removeClass('d-none')
                                .text(msg);
                        }
                    });
                }
            }).fail(function () {
                $('#reservation-warning')
                    .removeClass('d-none')
                    .text('Ошибка при проверке.');
            });
        });

        // Показать форму добавления
        document.getElementById('open-add-desk-panel').addEventListener('click', () => {
            document.getElementById('add-desk-panel').classList.remove('d-none');
        });

        // Скрыть форму добавления
        document.getElementById('close-add-desk-panel').addEventListener('click', () => {
            document.getElementById('add-desk-panel').classList.add('d-none');
        });

        // Обработка формы
        document.getElementById('add-desk-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            fetch("{{ route('desks.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(async response => {
                if (response.ok) {
                    try {
                        const data = await response.json();
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('❌ Desk creation failed. Server responded with success: false.');
                        }
                    } catch (parseErr) {
                        console.warn('⚠️ JSON parsing failed, but response was OK. Reloading anyway.');
                        location.reload(); // fallback если JSON был пуст или невалидный
                    }
                } else {
                    const errorText = await response.text();
                    throw new Error(errorText || 'Server error');
                }
            })
            .catch(err => {
                console.error('💥 Error during desk creation:', err);
                alert('❌ Error creating desk. Please check the console for details.');
            });
        });

        document.querySelectorAll('.external-desk').forEach(desk => {
            desk.addEventListener('click', () => {
                if (!isAdmin) return; // ✅ Только админ может редактировать

                const id = desk.dataset.id;
                const name = desk.dataset.name;
                const capacity = desk.dataset.capacity;
                const status = desk.dataset.status;
                const left = parseFloat(desk.style.left) || 0;
                const top = parseFloat(desk.style.top) || 0;
                const width = parseFloat(desk.style.width) || 52;
                const coordX = Math.round((left + width / 2) / 10);
                const coordY = Math.round(top / 10);

                // Заполнение формы
                $('#edit-desk-id').val(id);
                $('#edit-desk-name').val(name);
                $('#edit-desk-capacity').val(capacity);
                $('#edit-desk-status').val(status);
                $('#edit-desk-coordinates-x').val(coordX);
                $('#edit-desk-coordinates-y').val(coordY);

                // ВАЖНО: установить data-атрибут в HTML, чтобы его правильно прочитали через .attr()
                $('#edit-desk-form').attr('data-external', 'true');

                // Открыть модалку
                new bootstrap.Modal(document.getElementById('edit-desk-modal')).show();
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const selectDisplay = document.getElementById('select-display');
        const optionsList = document.getElementById('options-list');
        const selectedText = document.getElementById('selected-text');
        const selectedIcon = document.getElementById('selected-icon');
        const deskTypeInput = document.getElementById('desk-type');

        // Переключение видимости списка при клике
        selectDisplay.addEventListener('click', function (e) {
            e.stopPropagation();
            optionsList.style.display = optionsList.style.display === 'block' ? 'none' : 'block';
        });

        // Обработка выбора элемента
        optionsList.addEventListener('click', function (e) {
            const selectedOption = e.target.closest('li');
            if (selectedOption) {
                const value = selectedOption.getAttribute('data-value');
                const icon = selectedOption.getAttribute('data-icon');
                const text = selectedOption.textContent.trim();

                deskTypeInput.value = value;
                selectedIcon.src = icon;
                selectedText.textContent = text;
                optionsList.style.display = 'none';
            }
        });

        // Закрытие списка при клике вне
        document.addEventListener('click', function (e) {
            if (!selectDisplay.contains(e.target) && !optionsList.contains(e.target)) {
                optionsList.style.display = 'none';
            }
        });
    });

</script>
@endsection
