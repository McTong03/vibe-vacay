<?php
include 'conn.php';

//Popular tag types
$tags_sql = "SELECT tt.tag_type_name, COUNT(dtm.destination_id) AS dest_count
             FROM destination_tag_mapping dtm
             JOIN destination_tags dt ON dtm.tag_id = dt.tag_id
             JOIN tag_type tt ON dt.tag_type_id = tt.tag_type_id
             GROUP BY tt.tag_type_id, tt.tag_type_name
             ORDER BY dest_count DESC";

$tags_result = mysqli_query($conn, $tags_sql);
$tag_labels = [];
$tag_data   = [];

if ($tags_result) {
    while ($row = mysqli_fetch_assoc($tags_result)) {
        $tag_labels[] = $row['tag_type_name'];
        $tag_data[]   = (int)$row['dest_count'];
    }
}

// Top 6 destinations by review count (bar chart)
$dest_sql = "SELECT destination_name, reviews_count
             FROM destinations
             ORDER BY reviews_count DESC
             LIMIT 6";

$dest_result = mysqli_query($conn, $dest_sql);
$dest_labels = [];
$dest_data   = [];

if ($dest_result) {
    while ($row = mysqli_fetch_assoc($dest_result)) {
        $dest_labels[] = $row['destination_name'];
        $dest_data[]   = (int)$row['reviews_count'];
    }
}

// Popular states
$states_sql = "SELECT s.state_name, COUNT(d.destination_id) AS dest_count
               FROM destinations d
               JOIN states s ON d.state_id = s.state_id
               GROUP BY s.state_id, s.state_name
               ORDER BY dest_count DESC
               LIMIT 6";

$states_result = mysqli_query($conn, $states_sql);
$state_labels = [];
$state_data   = [];

if ($states_result) {
    while ($row = mysqli_fetch_assoc($states_result)) {
        $state_labels[] = $row['state_name'];
        $state_data[]   = (int)$row['dest_count'];
    }
}

mysqli_close($conn);
?>

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
                <div class="legend-table"></div>
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
                <div class="legend-table"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.font.family = "'Poppins', sans-serif";

        const COLORS = ['#1A2B49', '#7b92d2', '#a8bceb', '#4a6fa5', '#c5d3f0', '#2e4a7a'];

        // 1. Donut: Popular Destination Tag Types
        const tagLabels = <?= json_encode($tag_labels) ?>;
        const tagData = <?= json_encode($tag_data) ?>;

        new Chart(document.getElementById('tagsChart'), {
            type: 'doughnut',
            data: {
                labels: tagLabels,
                datasets: [{
                    data: tagData,
                    backgroundColor: COLORS,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                cutout: '60%',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                animation: {
                    duration: 900
                }
            }
        });

        // Update tags legend dynamically
        const tagsLegendEl = document.querySelector('#tagsChart').closest('.donut-section').querySelector('.legend-table');
        tagsLegendEl.innerHTML = tagLabels.map((label, i) => `
        <div class="legend-row">
            <span class="legend-dot" style="background:${COLORS[i]}"></span>
            <span class="legend-name">${label}</span>
            <span class="legend-val">${tagData[i]}</span>
        </div>
    `).join('');

        // 2. Bar: Destinations by review count
        new Chart(document.getElementById('destChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($dest_labels) ?>,
                datasets: [{
                    label: 'Review Count',
                    data: <?= json_encode($dest_data) ?>,
                    backgroundColor: '#1A2B49',
                    borderRadius: 6,
                    hoverBackgroundColor: '#7b92d2'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#374151'
                        },
                        title: {
                            display: true,
                            text: 'Destination', // ← add this
                            font: {
                                size: 13,
                                weight: '600'
                            },
                            color: '#1A2B49'
                        }
                    },
                    y: {
                        grid: {
                            color: '#e5e7eb'
                        },
                        ticks: {
                            color: '#374151'
                        },
                        title: {
                            display: true,
                            text: 'Review Count',
                            font: {
                                size: 13,
                                weight: '600'
                            },
                            color: '#1A2B49'
                        }
                    }
                },
                datasets: {
                    bar: {
                        barPercentage: 0.8, // ← makes bars wider
                        categoryPercentage: 0.7 // ← controls spacing between bars
                    }
                }
            }
        });

        // 3. Donut: Popular States
        const stateLabels = <?= json_encode($state_labels) ?>;
        const stateData = <?= json_encode($state_data) ?>;

        new Chart(document.getElementById('statesChart'), {
            type: 'doughnut',
            data: {
                labels: stateLabels,
                datasets: [{
                    data: stateData,
                    backgroundColor: COLORS,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                cutout: '60%',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                animation: {
                    duration: 900
                }
            }
        });

        // Update states legend dynamically
        const statesLegendEl = document.querySelector('#statesChart').closest('.donut-section').querySelector('.legend-table');
        statesLegendEl.innerHTML = stateLabels.map((label, i) => `
        <div class="legend-row">
            <span class="legend-dot" style="background:${COLORS[i]}"></span>
            <span class="legend-name">${label}</span>
            <span class="legend-val">${stateData[i]}</span>
        </div>
    `).join('');
    </script>
</body>

</html>