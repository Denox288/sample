<?php
include 'db_connect.php';

// Aggregate all trouble types across all months
$sql = "
  SELECT f.category, COUNT(*) AS count
  FROM tickets t
  JOIN trouble_flags f ON t.tflag = f.tflag
  WHERE t.timestamp BETWEEN '2025-01-01' AND '2025-12-30'
  GROUP BY f.category
  ORDER BY count DESC
";
$res = $conn->query($sql);

// Prepare data for amCharts
$chartData = [];
while ($row = $res->fetch_assoc()) {
    $chartData[] = [
        'sector' => $row['category'],
        'size' => (int)$row['count']
    ];
}
$json = json_encode($chartData);
?>

<!-- amCharts Libraries -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

<div id="chartdiv" style="width: 100%; height: 400px;"></div>

<script>
am4core.ready(function() {
  am4core.useTheme(am4themes_animated);

  // Create chart instance
  const chart = am4core.create("chartdiv", am4charts.PieChart);
  chart.data = <?= $json ?>;

  chart.innerRadius = am4core.percent(40);

  const series = chart.series.push(new am4charts.PieSeries());
  series.dataFields.value = "size";
  series.dataFields.category = "sector";

  series.labels.template.disabled = false;
  series.ticks.template.disabled = false;
  chart.legend = new am4charts.Legend();

  chart.titles.create().text = "Trouble Breakdown";
});
</script>
