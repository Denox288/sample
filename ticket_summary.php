<?php
include 'db_connect.php';

$_SESSION['WorkerID'];
$_SESSION['UserName'];
$_SESSION['DEPT'];

$username='UserName';
$statuses = ['PENDING', 'ON-GOING', 'RESOLVED'];
$counts = [];

foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tickets WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $counts[$status] = $result['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket Summary</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            color: black;
            padding: 40px;
            margin: 0;
        }

        .dashboard-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .card {
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.23);
            margin-bottom: 20px;
            flex: 1 1 220px;
            min-width: 120px;
            max-width: 100%;
            box-sizing: border-box;
        }

        .card h4 {
            margin: 0 0 6px;
            font-weight: normal;
            font-size: 14px;
            color: grey;
        }

        .card h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: black;
        }

        .chart-box {
            height: 60px;
            margin-top: 10px;
        }

        @media (max-width: 900px) {
            .dashboard-cards {
                flex-direction: column;
                align-items: center;
            }
            .card {
                width: 90%;
                max-width: 400px;
            }
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .card {
                padding: 12px;
                width: 100%;
                min-width: unset;
                max-width: unset;
            }
            .chart-box {
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-cards">
        <div class="card">
            <h4>Total Pending</h4>
            <h2><?= $counts['PENDING'] ?></h2>
            <div class="chart-box">
                <canvas id="pendingChart"></canvas>
            </div>
        </div>
        <div class="card">
            <h4>Total ON-GOING</h4>
            <h2><?= $counts['ON-GOING'] ?></h2>
            <div class="chart-box">
                <canvas id="ON-GOINGChart"></canvas>
            </div>
        </div>
        <div class="card">
            <h4>Total Resolved</h4>
            <h2><?= $counts['RESOLVED'] ?></h2>
            <div class="chart-box">
                <canvas id="resolvedChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        function generateMiniChart(ctx, color) {
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array.from({length: 10}, (_, i) => i + 1),
                    datasets: [{
                        data: Array.from({length: 10}, () => Math.floor(Math.random() * 100)),
                        borderColor: color,
                        backgroundColor: color + "33",
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { display: false },
                        y: { display: false }
                    }
                }
            });
        }

        generateMiniChart(document.getElementById('pendingChart'), '#e74c3c');   // red
        generateMiniChart(document.getElementById('ON-GOINGChart'), '#f39c12');   // orange
        generateMiniChart(document.getElementById('resolvedChart'), '#2ecc71');  // green
    </script>
</body>
</html>
