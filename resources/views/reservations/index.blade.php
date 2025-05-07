@extends('layouts.app')

@section('title', __('messages.reservation_list'))

@section('content')
@php
    use Carbon\Carbon;
    $currentDate = Carbon::parse(request('reservation_date', now()->toDateString()));
@endphp

<style>
    .reservation-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .map-container {
        flex: 2;
        min-width: 300px;
        overflow: auto;
        border: 2px dashed red;
    }

    .table-container {
        flex: 1;
        min-width: 300px;
        overflow: auto;
    }

    .reservation-form-panel {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 20px;
        margin-bottom: 20px;
        display: none;
    }

    .reservation-form-panel.active {
        display: block;
    }

    .week-scroll-container {
        position: relative;
        background: #fff;
    }

    .week-buttons-wrapper {
        overflow-x: auto;
        width: 100%;
    }

    .week-button-wrapper {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
        min-width: 700px;
        padding: 0 30px;
    }

    .week-button {
        height: 60px;
        background-color: #343a40;
        color: white;
        border: none;
        font-size: 13px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .week-button.today { background-color: #198754 !important; }
    .week-button.selected { background-color: #ffc107 !important; color: #000 !important; }

    .arrow-icon {
        position: absolute;
        top: 10px;
        width: 30px;
        height: 60px;
        background: #fff;
        border: 1px solid #000;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
    }

    .arrow-left { left: 5px; }
    .arrow-right { right: 5px; }

    @media (max-width: 768px) {
        .week-button-wrapper {
            display: flex;
            min-width: auto;
        }

        .week-button { min-width: 120px; }
        .arrow-icon { height: 50px; top: 5px; }
    }
</style>

<div class="container-fluid mt-4">
    <h1 class="mb-4" style="font-size: 30px;">{{ __('messages.reservation_list') }}</h1>

    <div class="week-scroll-container mb-3">
        <div class="arrow-icon arrow-left" onclick="shiftWeek(-1)">&#8592;</div>
        <div class="arrow-icon arrow-right" onclick="shiftWeek(1)">&#8594;</div>
        <div class="week-buttons-wrapper">
            <div id="week-buttons" class="week-button-wrapper"></div>
        </div>
    </div>

    <button id="toggle-reservation-form" class="btn btn-success mb-3">{{ __('messages.add_reservation') }}</button>

    <div id="reservation-form-panel" class="reservation-form-panel">
        <form action="{{ route('reservations.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('messages.customer') }}</label>
                    <select name="customer_id" class="form-select" required>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('messages.date') }}</label>
                    <input type="date" name="reservation_date" class="form-control" value="{{ request('reservation_date', now()->toDateString()) }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('messages.time') }}</label>
                    <input type="time" name="reservation_time" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('messages.duration') }}</label>
                    <select name="duration_hours" class="form-select">
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }} hour{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('messages.status') }}</label>
                    <select name="status" class="form-select">
                        <option value="new">New</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <label class="form-label">{{ __('messages.desk') }}</label>
                <input type="hidden" name="desk_id" id="selected-desk-id">
                <input type="text" class="form-control" id="selected-desk-name" placeholder="{{ __('messages.select_desk') }}" readonly>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="submit">{{ __('messages.save') }}</button>
                <button class="btn btn-secondary" type="button" id="cancel-reservation-form">{{ __('messages.cancel') }}</button>
            </div>
        </form>
    </div>

    <div class="reservation-layout">
        <div class="map-container bg-white p-3 border rounded shadow-sm">
            @include('components.reservation-map', [
                'desks' => $desks,
                'externalDesks' => $externalDesks,
                'reservedDeskIds' => $reservedDeskIds
            ])
        </div>

        <div class="table-container bg-white p-3 border rounded shadow-sm">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.customer') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.time') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservations as $reservation)
                            <tr>
                                <td>
                                    <button class="btn btn-link text-primary p-0 open-reservation-details" data-id="{{ $reservation->id }}">
                                        {{ $reservation->customer->name }}
                                    </button>
                                </td>
                                <td>{{ $reservation->reservation_date }}</td>
                                <td>{{ $reservation->reservation_time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">{{ __('messages.no_reservations_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
<div class="modal fade" id="reservation-details-modal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.reservation_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reservation-details-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-reservation-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="edit-reservation-modal-content"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const weekButtonsContainer = document.getElementById('week-buttons');
    let currentDate = new Date("{{ $currentDate->toDateString() }}");
    const today = new Date("{{ now()->toDateString() }}");
    let selectedDate = new URLSearchParams(window.location.search).get('reservation_date') || formatDate(today);
    const modalId = "{{ auth()->user()->hasRole('Admin') ? '#admin-reservation-modal' : '#user-reservation-modal' }}";

    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    function renderWeek() {
        weekButtonsContainer.innerHTML = '';
        const start = new Date(currentDate);
        start.setDate(currentDate.getDate() - 3);

        for (let i = 0; i < 7; i++) {
            const date = new Date(start);
            date.setDate(start.getDate() + i);
            const formatted = formatDate(date);

            const btn = document.createElement('button');
            btn.classList.add('week-button', 'btn');
            btn.innerHTML = `${date.toLocaleDateString('en-GB', { weekday: 'long' })}<br>${formatted}`;

            if (formatted === formatDate(today)) btn.classList.add('today');
            if (formatted === selectedDate) btn.classList.add('selected');

            btn.onclick = () => {
                selectedDate = formatted;
                loadReservations(formatted);
            };

            weekButtonsContainer.appendChild(btn);
        }
    }

    function shiftWeek(days) {
        currentDate.setDate(currentDate.getDate() + days);
        renderWeek();
    }

    function isFormVisible() {
        return document.querySelector('#reservation-form-panel')?.classList.contains('active');
    }

    function initDeskClickHandlers() {
        document.querySelector('.map-container')?.addEventListener('click', function (e) {
            const desk = e.target.closest('.desk.available');
            if (!desk) return;

            const deskId = desk.dataset.id;

            if (isFormVisible()) {
                // Снять выделение со всех
                document.querySelectorAll('.desk').forEach(d => d.classList.remove('selected'));
                // Выделить текущий
                desk.classList.add('selected');
                // Установить значение в hidden
                document.getElementById('selected-desk-id').value = deskId;
                document.getElementById('selected-desk-name').value = desk.dataset.name || `Desk #${deskId}`;
            } else {
                $(modalId).find('input[name="desk_id"]').val(deskId);
                new bootstrap.Modal(document.querySelector(modalId)).show();
            }
        });
    }

    function loadReservations(dateStr) {
        const scrollX = document.querySelector('.map-container').scrollLeft;
        const scrollY = document.querySelector('.map-container').scrollTop;

        fetch(`/reservations?reservation_date=${dateStr}`)
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                document.querySelector('.map-container').innerHTML = doc.querySelector('.map-container').innerHTML;
                document.querySelector('.table-container').innerHTML = doc.querySelector('.table-container').innerHTML;
                document.querySelector('.map-container').scrollLeft = scrollX;
                document.querySelector('.map-container').scrollTop = scrollY;

                currentDate = new Date(dateStr);
                selectedDate = dateStr;
                renderWeek();
                initDeskClickHandlers();
                history.replaceState(null, '', `?reservation_date=${dateStr}`);
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        renderWeek();
        initDeskClickHandlers();

        document.querySelector('#toggle-reservation-form').addEventListener('click', () => {
            document.querySelector('#reservation-form-panel').classList.add('active');
        });

        document.querySelector('#cancel-reservation-form').addEventListener('click', () => {
            document.querySelector('#reservation-form-panel').classList.remove('active');
            document.getElementById('selected-desk-id').value = '';
            document.querySelectorAll('.desk').forEach(d => d.classList.remove('selected'));
        });

        $(document).on('click', '.open-reservation-details', function () {
            const id = $(this).data('id');
            $.get(`/reservations/${id}/modal`, function (html) {
                $('#reservation-details-body').html(html);
                new bootstrap.Modal(document.getElementById('reservation-details-modal')).show();
            });
        });

        $(document).on('click', '.open-reservation-edit', function () {
            const id = $(this).data('id');
            $.get(`/reservations/${id}/edit-modal`, function (html) {
                $('#edit-reservation-modal-content').html(html);
                new bootstrap.Modal(document.getElementById('edit-reservation-modal')).show();
            });
        });
    });
</script>
@endpush
