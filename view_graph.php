<?php
session_start();
require_once "dbconnection.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$query = $pdo->query("SELECT medicine_name, stock FROM medicines ORDER BY stock DESC");
$medicineNames = [];
$stockCounts = [];

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $medicineNames[] = $row['medicine_name'];
    $stockCounts[] = $row['stock'];
}

$lowStockQuery = $pdo->query("SELECT medicine_name, stock FROM medicines WHERE stock <= 20 ORDER BY stock ASC");
$lowNames = [];
$lowStocks = [];

while ($row = $lowStockQuery->fetch(PDO::FETCH_ASSOC)) {
    $lowNames[] = $row['medicine_name'];
    $lowStocks[] = $row['stock'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📊 Inventory Graph</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">📊 Inventory Graph</a>
        <a href="admin-dashboard.php" class="btn btn-light btn-sm">⬅️ Back</a>
    </div>
</nav>

<div class="container">

    <!-- Buttons -->
    <div class="text-center mb-3">
        <button class="btn btn-primary me-2" onclick="showChart('chart1')">Stock Chart</button>
        <button class="btn btn-success" onclick="showChart('chart2')">Low Stock Chart</button>
    </div>

    <!-- Card Container -->
    <div class="card shadow p-4">

        <h4 class="text-center mb-4 fw-bold" id="chartTitle">Medicine Stock Chart</h4>

        <canvas id="chart1" height="120"></canvas>
        <canvas id="chart2" height="120" style="display:none;"></canvas>

    </div>
</div>

<script>

const labels = <?= json_encode($medicineNames) ?>;
const data = <?= json_encode($stockCounts) ?>;
const lowLabels = <?= json_encode($lowNames) ?>;
const lowData = <?= json_encode($lowStocks) ?>;


const ctx1 = document.getElementById('chart1').getContext('2d');
const stockChart = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Stock Quantity',
            data: data,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

const ctx2 = document.getElementById('chart2').getContext('2d');
const lowStockChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: lowLabels,
        datasets: [{
            label: 'Low Stock Quantity',
            data: lowData,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

function showChart(chart) {
    document.getElementById("chart1").style.display = "none";
    document.getElementById("chart2").style.display = "none";

    document.getElementById(chart).style.display = "block";

    if (chart === "chart1") {
        document.getElementById("chartTitle").innerText = "Medicine Stock Chart";
    } else {
        document.getElementById("chartTitle").innerText = "Low Stock Alert Chart (≤20)";
    }
}
</script>

</body>
</html>
