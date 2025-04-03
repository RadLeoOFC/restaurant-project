@extends('layouts.app')

@section('title', __('messages.desk_map'))

@section('content')
<div class="container mt-4">
    <h1 style="font-size: 30px; margin-bottom:20px">{{ __('messages.desk_layout') }}</h1>

    <a href="{{ route('desks.create') }}" class="btn btn-primary mb-3">{{ __('messages.add_desk') }}</a>
    <a href="{{ route('desks.index') }}" class="btn btn-secondary mb-3">{{ __('messages.back_to_list') }}</a>
    <a href="{{ route('desks.snapshot') }}" class="btn btn-warning mb-3">{{ __('messages.save_snapshot') }}</a>
    <a href="javascript:void(0);" onclick="resetToTodaySnapshot()" class="btn btn-danger mb-3 ms-2">{{ __('messages.reset_map') }}</a>

    <!-- остальная часть остаётся без изменений -->

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
</div>

<!-- Edit Modal -->
<div id="edit-desk-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-desk-form">
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

<style>
    .zoom-pan-wrapper {
        width: 100%;
        height: 600px;
        overflow: auto;
        border: 2px solid #ccc;
        position: relative;
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
        let scale = 1, panX = 0, panY = 0;
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

        let isPanning = false, startX = 0, startY = 0;
        wrapper.addEventListener('mousedown', e => {
            isPanning = true;
            startX = e.clientX - panX;
            startY = e.clientY - panY;
        });

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

        interact('.desk:not(.external-desk)').draggable({
            listeners: {
                start(event) {
                    isDraggingDesk = true;
                    wrapper.style.pointerEvents = 'none';

                    // Сохраняем изначальные координаты
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

                    const newLeft = currentLeft + dx;
                    const newTop = currentTop + dy;

                    target.style.left = `${newLeft}px`;
                    target.style.top = `${newTop}px`;
                },
                end(event) {
                    isDraggingDesk = false;
                    wrapper.style.pointerEvents = 'auto';

                    const target = event.target;
                    const id = target.dataset.id;
                    const capacity = parseInt(target.dataset.capacity) || 1;
                    const deskWidth = 52 * Math.ceil(capacity / 2);

                    const left = parseFloat(target.style.left);
                    const top = parseFloat(target.style.top);

                    const coordX = Math.round((left + deskWidth / 2) / 10);
                    const coordY = Math.round(top / 10);

                    if (confirm("Save new desk position?")) {
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
                            },
                            error: () => alert("Error saving desk.")
                        });
                    } else {
                        // Return to original position
                        target.style.left = target.dataset.originalLeft;
                        target.style.top = target.dataset.originalTop;
                    }
                }
            }
        });



        document.querySelectorAll('.desk:not(.external-desk)').forEach(desk => {
            desk.addEventListener('click', () => {
                const id = desk.dataset.id;
                const name = desk.dataset.name;
                const capacity = desk.dataset.capacity;
                const status = desk.dataset.status;

                const left = parseFloat(desk.style.left) || 0;
                const top = parseFloat(desk.style.top) || 0;
                const width = 52 * Math.ceil(capacity / 2);
                const coordX = Math.round((left + width / 2) / 10);
                const coordY = Math.round(top / 10);

                $('#edit-desk-id').val(id);
                $('#edit-desk-name').val(name);
                $('#edit-desk-capacity').val(capacity);
                $('#edit-desk-status').val(status);
                $('#edit-desk-coordinates-x').val(coordX);
                $('#edit-desk-coordinates-y').val(coordY);

                new bootstrap.Modal(document.getElementById('edit-desk-modal')).show();
            });
        });

        $('#edit-desk-form').on('submit', function (e) {
            e.preventDefault();

            const id = $('#edit-desk-id').val();
            const data = {
                _token: "{{ csrf_token() }}",
                name: $('#edit-desk-name').val(),
                capacity: $('#edit-desk-capacity').val(),
                status: $('#edit-desk-status').val(),
                coordinates_x: $('#edit-desk-coordinates-x').val(),
                coordinates_y: $('#edit-desk-coordinates-y').val()
            };

            $.ajax({
                url: `/desks/${id}`,
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
                const select = $('<select id="snapshot-date-select" class="form-select mb-3 me-2" style="width:auto; display:inline-block;"></select>');
                select.append('<option disabled selected>{{ __('messages.choose_snapshot_date') }}</option>');

                dates.forEach(item => {
                    select.append(`<option value="${item.snapshot_date}">${item.snapshot_date}</option>`);
                });

                // Insert dropdown before Save button
                $('.btn-warning').before(select);

                // On change, load snapshot by selected date
                select.on('change', function () {
                    const selectedDate = $(this).val();
                    loadSnapshotByDate(selectedDate);
                });
            });
        }

        // Load snapshot data by selected date
        function loadSnapshotByDate(date) {
            $.post('/snapshots/load', {
                _token: "{{ csrf_token() }}",
                snapshot_date: date
            }, function (desks) {
                updateDeskPositions(desks);
            });
        }

        // Apply new coordinates to desks on map
        function updateDeskPositions(desks) {
            desks.forEach(desk => {
                const element = document.querySelector(`.desk[data-id='${desk.desk_id}']`);
                if (element) {
                    // Обновляем координаты
                    const capacity = parseInt(desk.capacity);
                    const deskWidth = 52 * Math.ceil(capacity / 2);
                    const left = desk.coordinates_x * 10 - deskWidth / 2;
                    const top = desk.coordinates_y * 10;

                    element.style.left = `${left}px`;
                    element.style.top = `${top}px`;

                    // Обновляем data-атрибуты
                    element.dataset.capacity = desk.capacity;
                    element.dataset.name = desk.name;
                    element.dataset.status = desk.status ?? 'available'; // значение по умолчанию

                    // Удаляем все возможные классы статуса
                    element.classList.remove('available', 'occupied', 'selected');

                    // Добавляем только корректный класс, если он есть
                    if (['available', 'occupied', 'selected'].includes(desk.status)) {
                        element.classList.add(desk.status);
                    } else {
                        // На случай если статус отсутствует или некорректен
                        element.classList.add('available');
                    }

                    // Обновляем текст (номер стола)
                    element.textContent = desk.name.replace(/[^\d]/g, '');
                }
            });
        }

        // Call snapshot dropdown loader on page load
        loadSnapshotDates();

    });
</script>
@endsection
