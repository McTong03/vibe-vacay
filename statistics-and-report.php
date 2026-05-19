<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics & Report</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/statistics-and-report.css">
</head>
<body>
    <?php include('./includes/admin-nav-bar.php'); ?>

    <div class="main-content">

        <div class="page-title-block">
            <h1 class="page-title">Statistics &amp; Report</h1>
            <p class="breadcrumb">Admin / Statistics &amp; Report</p>
        </div>

        <div class="filter-card">
            <div class="filter-row">
                <div class="date-input-wrap">
                    <input type="date" class="date-input" />
                </div>
                <div class="date-input-wrap">
                    <input type="date" class="date-input" />
                </div>
                <div class="select-wrap">
                    <select class="filter-select">
                        <option value="">All</option>
                        <option value="30">30 Days</option>
                        <option value="60">60 Days</option>
                        <option value="90">90 Days</option>
                    </select>
                    <i class="ph-bold ph-caret-down select-icon"></i>
                </div>
            </div>
            <div class="filter-actions">
                <button class="btn-reset" onclick="resetFilters()">Reset</button>
                <button class="btn-generate" onclick="generateReport()">Generate</button>
            </div>
        </div>

        <div class="stat-cards-row">
            <div class="stat-card">
                <p class="stat-label">Total Users</p>
                <p class="stat-value">5,670</p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Destinations</p>
                <p class="stat-value">9,270</p>
            </div>
        </div>

        <p class="section-title">Popular Destination Tags</p>
        <div class="chart-card">
            <div class="donut-section">
                <div class="donut-wrap">
                    <canvas id="tagsChart"></canvas>
                </div>
                <div class="legend-table">
                    <div class="legend-row">
                        <span class="legend-dot" style="background:#1A2B49"></span>
                        <span class="legend-name">Lifestyle</span>
                        <span class="legend-val">120</span>
                    </div>
                    <div class="legend-row">
                        <span class="legend-dot" style="background:#7b92d2"></span>
                        <span class="legend-name">Urban</span>
                        <span class="legend-val">60</span>
                    </div>
                    <div class="legend-row">
                        <span class="legend-dot" style="background:#a8bceb"></span>
                        <span class="legend-name">Vibrant</span>
                        <span class="legend-val">60</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="section-title">Destination Chart</p>
        <div class="chart-card">
            <div class="bar-section">
                <canvas id="destChart"></canvas>
            </div>
        </div>

        <p class="section-title">Popular States</p>
        <div class="chart-card">
            <div class="donut-section">
                <div class="donut-wrap">
                    <canvas id="statesChart"></canvas>
                </div>
                <div class="legend-table">
                    <div class="legend-row">
                        <span class="legend-dot" style="background:#1A2B49"></span>
                        <span class="legend-name">Kuala Lumpur</span>
                        <span class="legend-val">120</span>
                    </div>
                    <div class="legend-row">
                        <span class="legend-dot" style="background:#7b92d2"></span>
                        <span class="legend-name">Selangor</span>
                        <span class="legend-val">60</span>
                    </div>
                    <div class="legend-row">
                        <span class="legend-dot" style="background:#a8bceb"></span>
                        <span class="legend-name">Penang</span>
                        <span class="legend-val">60</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.font.family = "'Poppins', sans-serif";

        const NAVY  = '#1A2B49';
        const BLUE  = '#7b92d2';
        const LIGHT = '#a8bceb';

        // Donut: Tags
        new Chart(document.getElementById('tagsChart'), {
            type: 'doughnut',
            data: {
                labels: ['Lifestyle', 'Urban', 'Vibrant'],
                datasets: [{ data: [50, 25, 25], backgroundColor: [NAVY, BLUE, LIGHT], borderWidth: 0, hoverOffset: 6 }]
            },
            options: { cutout: '60%', plugins: { legend: { display: false } }, animation: { duration: 900 } }
        });

        // Bar: Destinations
        new Chart(document.getElementById('destChart'), {
            type: 'bar',
            data: {
                labels: ['Lifestyle', 'Urban', 'Vibrant', 'Nature', 'Adventure', 'Culture'],
                datasets: [{ label: 'Destinations', data: [120, 60, 60, 90, 45, 75], backgroundColor: NAVY, borderRadius: 6, hoverBackgroundColor: BLUE }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#374151' } },
                    y: { grid: { color: '#e5e7eb' }, ticks: { color: '#374151' } }
                }
            }
        });

        // Donut: States
        new Chart(document.getElementById('statesChart'), {
            type: 'doughnut',
            data: {
                labels: ['Kuala Lumpur', 'Selangor', 'Penang'],
                datasets: [{ data: [50, 25, 25], backgroundColor: [NAVY, BLUE, LIGHT], borderWidth: 0, hoverOffset: 6 }]
            },
            options: { cutout: '60%', plugins: { legend: { display: false } }, animation: { duration: 900 } }
        });

        function resetFilters() {
            document.querySelectorAll('.date-input').forEach(i => i.value = '');
            document.querySelector('.filter-select').value = '';
        }

        function generateReport() {
            const btn = document.querySelector('.btn-generate');
            btn.textContent = 'Generating...';
            btn.disabled = true;
            setTimeout(() => { btn.textContent = 'Generate'; btn.disabled = false; }, 1500);
        }

        // Open native date picker on click anywhere in the input
        document.querySelectorAll('.date-input').forEach(input => {
            input.addEventListener('click', function() {
                this.showPicker();
            });
        });
    </script>
</body>
</html>