@extends('layouts.app')

@section('title', __('messages.reports'))

@section('content')
<div class="container mt-4">
    <h2 class="mb-4" style="font-size: 30px;">{{ __('messages.reservation_analytics') }}</h2>

    <div class="mb-3">
        <label for="chartType" class="form-label">{{ __('messages.select_period') }}</label>
        <select id="chartType" class="form-select w-auto">
            <option value="daily" {{ $type === 'daily' ? 'selected' : '' }}>{{ __('messages.daily') }}</option>
            <option value="weekly" {{ $type === 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
            <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
            <option value="yearly" {{ $type === 'yearly' ? 'selected' : '' }}>{{ __('messages.yearly') }}</option>
        </select>
    </div>

    <canvas id="reservationChart"></canvas>

    <style>
        #reservationChart {
            width: 100% !important;
            max-width: 1000px;
            height: 400px !important;
            margin: auto;
            display: block;
        }
    </style>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chart;
    window.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('reservationChart').getContext('2d');
        function loadChart(type = 'daily') {
            fetch(`/api/chart-data?type=${type}`)
                .then(res => res.json())
                .then(({ labels, data }) => {
                    if (chart) chart.destroy();
                    chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: '{{ __("messages.reservations_label") }}',
                                data: data,
                                fill: true,
                                tension: 0.3,
                                borderWidth: 2,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: true, position: 'top' }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: '{{ __("messages.date") }}'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: '{{ __("messages.number_of_reservations") }}'
                                    }
                                }
                            }
                        }
                    });
                });
        }
        document.getElementById('chartType').addEventListener('change', function () {
            loadChart(this.value);
        });
        loadChart('{{ $type ?? "daily" }}');
    });
</script>
@endsection
