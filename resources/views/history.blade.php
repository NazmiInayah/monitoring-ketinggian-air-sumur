@extends('layouts.app')

@section('title', 'History')

@section('content')
    <div class="container" style="margin-top: 20px;">
        <div class="card" style="margin-bottom: 40px">
            <div class="card-header"
                style="font-size: 30px; font-weight: bold; background-color: cornflowerblue; color: white;">
                <h3>Data History</h3>
            </div>
            
            <div class="card-body">
                <!-- Search Form -->
                <div class="card text-dark bg-light mb-3 "style="margin-top: 5px;>
                <form method="GET" action="{{ route('history') }}" class="mb-3">
                    
                    <div class="row" style="margin-left: 5px; margin-bottom: 5px">
                        
                        <div class="col-md-3 mt-3">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            Date
                        </div>
                        <div class="col-md-3  mt-3">
                            <input type="time" name="start_time" class="form-control" value="{{ request('start_time') }}"
                                step="1">Start Time
                        </div>
                        <div class="col-md-3  mt-3">
                            <input type="time" name="end_time" class="form-control" value="{{ request('end_time') }}"
                                step="1"> End Time
                        </div>
                        <div class="col-md-1  mt-3">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>
            </div>

                <!-- Date Range Selection for Download -->


                <!-- Show all data in scrollable table -->
                <div id="all-data-scrollable"
                    style="overflow-y: auto; max-height: 400px; display: block; margin-top: 20px;">
                    <table class="table table-bordered table-striped mt-2">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Jarak</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allLevels as $waterLevel)
                                <tr>
                                    <td>{{ $waterLevel->no }}</td>
                                    <td>{{ $waterLevel->tanggal }}</td>
                                    <td>{{ $waterLevel->waktu }}</td>
                                    <td>{{ $waterLevel->level }} Meter</td>
                                    <td>{{ $waterLevel->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                <div class="col-md-12 mt-3">
                    <div class="card text-dark bg-light mb-3">
                        <div class="card-body">
                            <form method="GET" action="{{ route('downloadReport') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="date" name="start_date" class="form-control"
                                            placeholder="Start Date" required> Start Date
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" name="end_date" class="form-control" placeholder="End Date"
                                            required> End Date
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-info">Download Report .csv</button>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#chartModal">Generate Chart</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        
           
        </div>

        <!-- Modal for Chart -->
        <div class="modal fade" id="chartModal" tabindex="-1" aria-labelledby="chartModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="chartModalLabel">Chart</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <canvas id="chartCanvas"></canvas>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let chartInstance = null;

            const generateChart = () => {
                const startDate = document.querySelector('input[name="start_date"]').value;
                const endDate = document.querySelector('input[name="end_date"]').value;

                if (startDate && endDate) {
                    fetch(`{{ route('getChartData') }}?start_date=${startDate}&end_date=${endDate}`)
                        .then(response => response.json())
                        .then(data => {
                            const ctx = document.getElementById('chartCanvas').getContext('2d');

                            // Destroy the previous chart instance if it exists
                            if (chartInstance) {
                                chartInstance.destroy();
                            }

                            chartInstance = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.map(item => item.date), // Display dates on x-axis
                                    datasets: [{
                                        label: 'Average Water Level',
                                        data: data.map(item => item.average_level),
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        x: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Date'
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Average Water Level'
                                            }
                                        }
                                    }
                                }
                            });
                        })
                        .catch(error => console.error('Error fetching chart data:', error));
                }
            };

            document.querySelector('.btn-primary[data-bs-target="#chartModal"]').addEventListener('click',
                generateChart);
        });
    </script>
@endsection
