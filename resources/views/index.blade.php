@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container" style="text-align: center; margin-top: 10px;">
        <!-- Logo and Description -->
        <img src="{{ asset('monairsu/public/img/logo-kpspams.png') }}" style="width: 120px;">
        <p style="font-size: 50px; font-weight: bold; color: white;">PAM SIMAS SAGARA</p>
       
        <div class="row">
            <!-- Nilai Jarak -->
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-header" style="font-size: 20px; font-weight: bold; background-color: cornflowerblue; color: white;">
                        <h3>Nilai Jarak</h3>
                    </div>
                    <div class="card-body" style="font-size: 30px; font-weight: bold;">
                        <h1><span id="nilai_jarak">0</span> m</h1>
                    </div>
                </div>
            </div>

            <!-- Ketinggian Air -->
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-header" style="font-size: 20px; font-weight: bold; background-color: cornflowerblue; color: white;">
                        <h3>Ketinggian Air Sumur</h3>
                    </div>
                    <div class="card-body" style="font-size: 30px; font-weight: bold;">
                        <h1><span id="ketinggian_air">0</span> m</h1>
                    </div>
                </div>
            </div>

            <!-- Volume Air -->
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-header" style="font-size: 20px; font-weight: bold; background-color: cornflowerblue; color: white;">
                        <h3>Volume Air Sumur</h3>
                    </div>
                    <div class="card-body" style="font-size: 30px; font-weight: bold;">
                        <h1><span id="volume_air">0</span> L</h1>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header" style="font-size: 20px; font-weight: bold; background-color: cornflowerblue; color: white;">
                        <h3>Status</h3>
                    </div>
                    <div class="card-body" style="font-size: 20px; font-weight: bold;">
                        <h1 id="STATUS-JARAK">-</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-bottom:200px">
            <!-- Grafik -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="font-size: 30px; font-weight: bold; background-color: cornflowerblue; color: white;">
                        <h3>Grafik Tinggi Air Terhadap Waktu</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="waterLevelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            var lastValue = null;

            // Initialize the chart
            var ctx = document.getElementById('waterLevelChart').getContext('2d');
            var waterLevelChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [], // Time labels will be added dynamically
                    datasets: [{
                        label: 'Nilai Jarak',
                        borderColor: 'cornflowerblue',
                        backgroundColor: 'rgba(100, 149, 237, 0.2)',
                        data: [] // Data values will be added dynamically
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Waktu (WIB)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Nilai Jarak'
                            }
                        }
                    }
                }
            });

            // Fetch the initial data
            $.getJSON("api/water-level-data", function(data) {
                data.forEach(function(entry) {
                    waterLevelChart.data.labels.push(entry.time);
                    waterLevelChart.data.datasets[0].data.push(entry.level);
                });
                waterLevelChart.update();
            });

            // Update data every second
            setInterval(function() {
                $.getJSON("api/water-level", function(data) {
                    if (lastValue !== data.level) {
                        $("#nilai_jarak").text(data.level);
                        $("#ketinggian_air").text(84 - data.level);
                        $("#volume_air").text(calculateVolume(84 - data.level));
                        $("#STATUS-JARAK").text(data.status);
                        lastValue = data.level;
                        updateChart(data);
                    }
                });
            }, 1000);

            // Function to calculate volume
            function calculateVolume(height) {
                var radius = 0.0825; // 82.5 mm to meters
                var volume = Math.PI * Math.pow(radius, 2) * height; // Volume in cubic meters
                return (volume * 1000).toFixed(2); // Convert to liters and format
            }

            // Function to update the chart
            function updateChart(data) {
                var time = new Date().toLocaleTimeString();
                var level = data.level;

                if (waterLevelChart.data.labels.length >= 15) { // Limit data points to 15
                    waterLevelChart.data.labels.shift();
                    waterLevelChart.data.datasets[0].data.shift();
                }

                waterLevelChart.data.labels.push(time);
                waterLevelChart.data.datasets[0].data.push(level);
                waterLevelChart.update();
            }
        });
    </script>
@endsection
