@props(['desks', 'externalDesks', 'reservedDeskIds' => []])

<div class="zoom-pan-wrapper" id="zoom-wrapper">
    <div class="desk-map-container" id="desk-map-container">
        @php
            $maxX = $desks->max('coordinates_x');
            $maxY = $desks->max('coordinates_y');
        @endphp
        <div id="desk-canvas" style="width: {{ ($maxX + 10) * 10 }}px; height: {{ ($maxY + 10) * 10 }}px;">

            {{-- Internal desks --}}
            @foreach($desks as $desk)
                @php
                    $unitCount = ceil($desk->capacity / 2);
                    $unitSize = 50;
                    $spacing = 5;
                    $leftStart = $desk->coordinates_x * 10 - (($unitSize + $spacing) * $unitCount - $spacing) / 2;
                    $top = $desk->coordinates_y * 10;
                    $isReserved = in_array($desk->id, $reservedDeskIds);
                @endphp

                <div class="desk desk-group {{ $isReserved ? 'occupied' : 'available' }}" style="left: {{ $leftStart }}px; top: {{ $top }}px;"
                    data-id="{{ $desk->id }}"
                    data-name="{{ $desk->name }}"
                    data-capacity="{{ $desk->capacity }}"
                    data-status="{{ $desk->status }}">
                    @for ($i = 0; $i < $unitCount; $i++)
                        <div class="desk-unit {{ $isReserved ? 'occupied' : 'available' }}">
                            @if ($i == 0)
                                <span class="desk-label">{{ preg_replace('/[^0-9]/', '', $desk->name) }}</span>
                            @endif
                        </div>
                    @endfor
                </div>
            @endforeach


            {{-- External desks --}}
            @foreach($externalDesks as $desk)
                @php
                    $unitCount = ceil($desk->capacity / 2);
                    $unitSize = 50;
                    $spacing = 5;
                    $leftStart = $desk->coordinates_x * 10 - (($unitSize + $spacing) * $unitCount - $spacing) / 2;
                    $top = $desk->coordinates_y * 10;
                    $isReserved = in_array($desk->id, $reservedDeskIds);
                @endphp

                <div class="desk desk-group {{ $isReserved ? 'occupied' : 'available' }}" style="left: {{ $leftStart }}px; top: {{ $top }}px;"
                    data-id="{{ $desk->id }}"
                    data-name="{{ $desk->name }}"
                    data-capacity="{{ $desk->capacity }}"
                    data-status="{{ $desk->status }}">
                    @for ($i = 0; $i < $unitCount; $i++)
                        <div class="desk-unit external-desk-unit {{ $isReserved ? 'occupied' : 'available' }}">
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

<!-- Reservation Modal (for Users) -->
<div id="user-reservation-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="user-reservation-form" method="POST" action="{{ route('reservations.store') }}">
                @csrf
                <input type="hidden" name="desk_ids" id="user-reservation-desk-ids">
                <input type="hidden" name="status" value="new">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.reserve_desk') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong class="desk-name"></strong></p>
                    <div class="mb-3">
                        <label>{{ __('messages.date') }}</label>
                        <input type="date" name="reservation_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.time') }}</label>
                        <input type="time" name="reservation_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.duration') }}</label>
                        <select name="duration_hours" class="form-select">
                            @for ($i = 2; $i <= 8; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ __('messages.hours') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div id="user-warning" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">{{ __('messages.reserve') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Admin Reservation Modal -->
<div id="admin-reservation-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="admin-reservation-form" method="POST" action="{{ route('reservations.store') }}">
                @csrf
                <input type="hidden" name="desk_ids" id="admin-reservation-desk-ids">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.reserve_desk') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>{{ __('messages.customer') }}</label>
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
                        <label>{{ __('messages.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="new">{{ __('messages.status_new') }}</option>
                            <option value="confirmed">{{ __('messages.status_confirmed') }}</option>
                            <option value="cancelled">{{ __('messages.status_cancelled') }}</option>
                        </select>
                    </div>
                    <div id="admin-warning" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">{{ __('messages.reserve') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>



<style>
    .zoom-pan-wrapper {
        width: 100%;
        height: 80vh;
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
        z-index: 1;
        cursor: pointer;
        background: transparent !important;
        width: auto;
        height: auto;
        color: inherit;
        font-size: inherit;
        font-weight: inherit;
        border: none;
        box-shadow: none;
        text-align: inherit;
        line-height: normal;
    }

    .desk-group {
        position: absolute;
        display: flex;
        gap: 0px;
        cursor: pointer;
    }

    /* Внутренние столы (SVG с цветом по статусу) */
    .desk-unit {
        width: 60px;
        height: 60px;
        border: none;
        background-color: transparent;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
    }

    .desk-unit.available {
        background-image: url('/images/desk-green.png');
    }

    .desk-unit.occupied {
        background-image: url('/images/desk-red.png');
    }

    .desk-unit.selected {
        background-image: url('/images/desk-orange.png');
    }

    /* Внешние столы (SVG с цветом по статусу) */
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

    /* Номер стола */
    .desk-label {
        position: absolute;
        top: -18px;
        left: 0;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        background: #fff;
        border-radius: 3px;
    }

    /* Подсветка статуса для .desk (если применимо) */
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

    /* Старые внешние столы — больше не используются, оставлены на случай fallback'а */
    .external-desk {
        background-color: #2196F3 !important;
        border: 6px dashed white;
        cursor: default !important;
    }
    .external-desk.available { border-color: #4CAF50 !important; }
    .external-desk.occupied { border-color: #F44336 !important; }
    .external-desk.selected { border-color: #FF9800 !important; }

    /* Для модального окна */
    .modal.left-aligned .modal-dialog {
        margin-left: 0;
        margin-right: auto;
    }

</style>


<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
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

        document.getElementById('open-reservation-panel')?.addEventListener('click', () => {
            document.getElementById('add-reservation-panel').classList.remove('d-none');
        });

        document.getElementById('close-reservation-panel')?.addEventListener('click', () => {
            document.getElementById('add-reservation-panel').classList.add('d-none');
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

        document.getElementById('open-reservation-panel')?.addEventListener('click', () => {
            reservationPanelActive = true;
            new bootstrap.Modal(document.getElementById('admin-reservation-modal')).show();
        });

        document.querySelectorAll('.desk-group').forEach(group => {
            group.addEventListener('click', () => {
                const id = group.dataset.id;
                const name = group.dataset.name;
                const reservationPanelVisible = !document.getElementById('add-reservation-panel')?.classList.contains('d-none');

                if (isAdmin && reservationPanelVisible) {
                    const select = document.querySelector('#add-reservation-form select[name="desk_id"]');
                    if (select) {
                        select.value = id;
                    }
                } else if (isAdmin) {
                    const capacity = group.dataset.capacity;
                    const status = group.dataset.status;
                    const left = parseFloat(group.style.left) || 0;
                    const top = parseFloat(group.style.top) || 0;
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
                } else {
                    // ✅ Обычный пользователь — открыть форму бронирования
                    $('#user-reservation-desk-ids').val(id);

                    const number = name.replace(/[^\d]/g, '');
                    const translatedName = `{{ __('messages.desk_number') }} №${number}`;
                    $('#user-reservation-modal .desk-name').text(translatedName);

                    $('#user-warning').addClass('d-none').text('');
                    new bootstrap.Modal(document.getElementById('user-reservation-modal')).show();
                }
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
    });

</script>