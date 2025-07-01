<?php
include 'db_connect.php';
include 'user.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php#login");
    exit;
}

// ✅ Get the actual logged-in username
$user = $_SESSION['UserName'];

// Get counts per month for 2025
$sql = "
    SELECT MONTH(timestamp) AS month_num, COUNT(*) AS request_count
    FROM tickets
    WHERE requestor = ? AND YEAR(timestamp) = 2025
    GROUP BY month_num
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$res = $stmt->get_result();

$monthCounts = array_fill(1, 12, 0);
while ($row = $res->fetch_assoc()) {
    $monthCounts[(int)$row['month_num']] = (int)$row['request_count'];
}

// Prepare chart data
$months = [
    1 => "January", 2 => "February", 3 => "March", 4 => "April",
    5 => "May", 6 => "June", 7 => "July", 8 => "August",
    9 => "September", 10 => "October", 11 => "November", 12 => "December"
];

$chartData = [];
foreach ($months as $num => $name) {
    $value = $monthCounts[$num];
    $chartData[] = [
        "category" => $name,
        "value" => $value,
        "radius" => $value * 5
    ];
}

$jsonData = json_encode($chartData);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Timeline for <?= htmlspecialchars($user) ?></title>
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
    <style>
        #chartdiv {
            width: 100%;
            height: 500px;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fb;
            padding: 20px;
        }
    </style>
</head>
<body>
    <h2>Request Timeline</h2>
    <div id="chartdiv"></div>

    <script>
    am4core.ready(function() {
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartdiv", am4charts.PieChart);
        chart.data = <?= $jsonData ?>;

        var series = chart.series.push(new am4charts.PieSeries());
        series.dataFields.value = "value";
        series.dataFields.radiusValue = "radius";
        series.dataFields.category = "category";
        series.slices.template.tooltipText = "{category}: {value} requests";
        series.slices.template.stroke = am4core.color("#fff");
        series.slices.template.strokeWidth = 2;
        series.slices.template.strokeOpacity = 1;

        chart.innerRadius = am4core.percent(30);
        chart.legend = new am4charts.Legend();
    });
    </script>
</body>
</html>
