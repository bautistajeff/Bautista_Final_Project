<?php
require_once __DIR__ . '/dbconnection.php';
requireRole('admin');

$stmt = $pdo->query("SELECT category, SUM(stock) AS total_stock FROM medicines GROUP BY category ORDER BY total_stock DESC");
$categories = $stmt->fetchAll();
$max = 1;
foreach ($categories as $item) {
    if ((int) $item['total_stock'] > $max) {
        $max = (int) $item['total_stock'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Graph</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bar-chart { height: 280px; }
        .bar-chart .bar {
            background: linear-gradient(180deg, #0d6efd, #0b5ed7);
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 8px;
            color: white;
            font-size: 12px;
            min-height: 12px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">Inventory Overview</h3>
        <a href="admin-dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="bar-chart d-flex align-items-end gap-3">
                <?php if ($categories): ?>
                    <?php foreach ($categories as $category): ?>
                        <?php $height = ($max > 0) ? ((int) $category['total_stock'] / $max) * 100 : 0; ?>
                        <div class="flex-fill text-center">
                            <div class="bar" style="height: <?php echo $height; ?>%; min-height: <?php echo ($height < 12 ? 12 : $height); ?>%;">
                                <?php echo (int) $category['total_stock']; ?>
                            </div>
                            <div class="mt-2 small text-muted"><?php echo htmlspecialchars($category['category']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted">No inventory data available.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
