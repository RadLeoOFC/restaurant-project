@extends('layouts.app')

@section('title', __('messages.reservation_list'))

@section('content')
@php
    use Carbon\Carbon;
    $currentDate = Carbon::parse(request('reservation_date', now()->toDateString()));
    $weekOffset = (int) request()->get('week_offset', 0);
@endphp

<style>
    .reservation-layout {
        display: flex;
        flex-wrap: nowrap;
        align-items: flex-start;
        gap: 20px;
    }

    .map-container {
        flex: 2;
        min-width: 0;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
    }

    .table-container {
        flex: 1;
        min-width: 300px;
    }

    .full-width-btn {
        width: 100%;
    }

    .map-container {
        border: 2px dashed red;
    }

    .flex-nowrap {
        flex-wrap: nowrap !important;
    }
    .overflow-auto {
        overflow-x: auto;
    }

</style>

<div class="container-fluid mt-4">

    <h1 class="mb-4" style="font-size: 30px;">{{ __('messages.reservation_list') }}</h1>

    <div class="d-flex align-items-center mb-3 gap-2 flex-nowrap overflow-auto" id="week-buttons">
        <!-- Кнопки с датами будут вставляться через JavaScript -->
    </div>

    <div class="mb-3">
        <button id="open-reservation-panel" class="btn btn-primary">
            {{ __('messages.add_reservation') }}
        </button>
    </div>

    <div class="reservation-layout">
        {{-- Карта (слева) --}}
        <div class="map-container bg-white p-3 border rounded shadow-sm">
            @include('components.reservation-map')
        </div>

        {{-- Таблица (справа) --}}
        <div class="table-container bg-white p-3 border rounded shadow-sm" style="width: 20%; overflow-x: auto;">
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

{{-- Модальное окно деталей --}}
<div class="modal fade" id="reservation-details-modal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.reservation_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reservation-details-body">
                <!-- AJAX загрузит сюда содержимое -->
            </div>
        </div>
    </div>
</div>

{{-- Модальное окно редактирования --}}
<div class="modal fade" id="edit-reservation-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="edit-reservation-modal-content">
            <!-- AJAX загрузит форму -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(document).on('click', '.open-reservation-details', function () {
            const id = $(this).data('id');
            $.get(`/reservations/${id}/modal`, function (html) {
                $('#reservation-details-body').html(html);
                new bootstrap.Modal(document.getElementById('reservation-details-modal')).show();
            });
        });

        $('#open-reservation-panel').on('click', function () {
            $('#add-reservation-panel').removeClass('d-none');
        });

        $(document).on('click', '.open-reservation-edit', function () {
            const id = $(this).data('id');
            $.get(`/reservations/${id}/edit-modal`, function (html) {
                $('#edit-reservation-modal-content').html(html);
                new bootstrap.Modal(document.getElementById('edit-reservation-modal')).show();
            });
        });

        $(document).ready(function () {
            $('#apply-date-filter').on('click', function () {
                const selectedDate = $('#filter-date').val();
                if (selectedDate) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('reservation_date', selectedDate);
                    window.location.href = url.toString();
                }
            });
        });
    });

    const weekButtonsContainer = document.getElementById('week-buttons');

    let currentDate = new Date("{{ $currentDate->toDateString() }}");
    let today = new Date("{{ now()->toDateString() }}");
    let weekOffset = {{ $weekOffset }};

    function renderWeek() {
        weekButtonsContainer.innerHTML = '';

        // Кнопка назад
        const prevBtn = document.createElement('button');
        prevBtn.className = 'btn btn-outline-dark';
        prevBtn.innerHTML = '&larr;';
        prevBtn.onclick = function() {
            weekOffset -= 1;
            renderWeek();
        };
        weekButtonsContainer.appendChild(prevBtn);

        // Дни недели
        let startOfWeek = new Date(today);
        startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay() + 1 + (weekOffset * 7)); // Понедельник

        for (let i = 0; i < 7; i++) {
            const date = new Date(startOfWeek);
            date.setDate(startOfWeek.getDate() + i);

            const button = document.createElement('button');
            button.className = 'btn';
            button.style.minWidth = '110px';
            button.innerHTML = date.toLocaleDateString('en-GB', { weekday: 'long' }) + "<br>" + date.toISOString().split('T')[0];

            if (date.toDateString() === today.toDateString()) {
                button.classList.add('btn-success'); // сегодня — зелёная
            } else if (date.toDateString() === currentDate.toDateString()) {
                button.classList.add('btn-warning'); // выбранная дата — жёлтая
            } else {
                button.classList.add('btn-dark'); // остальные — чёрные
            }

            button.onclick = function() {
                const selectedDate = date.toISOString().split('T')[0];
                const url = new URL(window.location.href);
                url.searchParams.set('reservation_date', selectedDate);
                url.searchParams.set('week_offset', weekOffset);
                window.location.href = url.toString(); // При выборе даты — перезагрузка
            };

            weekButtonsContainer.appendChild(button);
        }

        // Кнопка вперед
        const nextBtn = document.createElement('button');
        nextBtn.className = 'btn btn-outline-dark';
        nextBtn.innerHTML = '&rarr;';
        nextBtn.onclick = function() {
            weekOffset += 1;
            renderWeek();
        };
        weekButtonsContainer.appendChild(nextBtn);
    }

    renderWeek();

    // Фильтр по конкретной дате
    $('#apply-date-filter').on('click', function () {
        const selectedDate = $('#filter-date').val();
        if (selectedDate) {
            const url = new URL(window.location.href);
            url.searchParams.set('reservation_date', selectedDate);
            window.location.href = url.toString();
        }
    });

</script>
@endpush
