<?php
session_start();
require_once "dbconnection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$totalRequests = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE user_id = ?");
$totalRequests->execute([$user_id]);
$totalRequests = $totalRequests->fetchColumn();

$pendingRequests = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE user_id = ? AND status = 'Pending'");
$pendingRequests->execute([$user_id]);
$pendingRequests = $pendingRequests->fetchColumn();

$totalMedicines = $pdo->query("SELECT COUNT(*) FROM medicines")->fetchColumn();
$outOfStock = $pdo->query("SELECT COUNT(*) FROM medicines WHERE stock = 0")->fetchColumn();

$medicines = $pdo->query("SELECT * FROM medicines ORDER BY medicine_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard | Botika Inventory</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">User Dashboard</span>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
  </div>
</nav>

<div class="container mt-4">
    <h4>Welcome, <?= htmlspecialchars($user['username']) ?> 👋</h4>
    <p class="text-muted">You are logged in as a regular user.</p>

    <div class="row">
        <!-- Profile -->
        <div class="col-md-4">
          <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5>Profile Info</h5>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Joined:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div class="col-md-8">
          <div class="row text-center">
            <?php
            $stats = [
                ["Total Requests", $totalRequests, "primary"],
                ["Medicines", $totalMedicines, "success"],
                ["Out of Stock", $outOfStock, "danger"],
                ["Pending", $pendingRequests, "warning"]
            ];
            foreach ($stats as $s) {
            ?>
            <div class="col-3 mb-3">
              <div class="card shadow-sm border-<?= $s[2] ?>">
                <div class="card-body">
                    <h6><?= $s[0] ?></h6>
                    <h3><?= $s[1] ?></h3>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Available Medicines</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th><th>Medicine</th><th>Category</th><th>Stock</th><th>Price</th><th>Expiry</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($medicines as $i => $m): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($m['medicine_name']) ?></td>
                    <td><?= htmlspecialchars($m['category']) ?></td>
                    <td><?= $m['stock'] ?></td>
                    <td>₱<?= number_format($m['price'], 2) ?></td>
                    <td><?= $m['expiry_date'] ?></td>
                    <td>
                        <span class="badge bg-<?= $m['stock']>0?"success":"danger" ?>">
                            <?= $m['stock']>0?"Available":"Out" ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($m['stock']>0): ?>
                        <button class="btn btn-sm btn-primary requestBtn"
                            data-id="<?= $m['id'] ?>"
                            data-name="<?= htmlspecialchars($m['medicine_name']) ?>">
                            Request
                        </button>
                        <?php else: ?>
                        <button class="btn btn-sm btn-secondary" disabled>Unavailable</button>
                        <?php endif ?>
                    </td>
                </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Request Modal -->
<div class="modal fade" id="requestModal">
 <div class="modal-dialog">
    <form method="POST" action="request_medicine.php" class="modal-content">
        <div class="modal-header"><h5>Request Medicine</h5></div>
        <div class="modal-body">
            <input type="hidden" name="medicine_id" id="medicine_id">
            <div class="mb-2">
                <label>Medicine</label>
                <input type="text" id="medicine_name" class="form-control" readonly>
            </div>
            <div class="mb-2">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" min="1" required>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success">Submit</button>
        </div>
    </form>
 </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll(".requestBtn").forEach(btn=>{
    btn.addEventListener("click",()=>{
        document.getElementById("medicine_id").value = btn.dataset.id;
        document.getElementById("medicine_name").value = btn.dataset.name;
        new bootstrap.Modal(document.getElementById('requestModal')).show();
    });
});

<?php if(isset($_SESSION['msg_success'])): ?>
Swal.fire("Success", "<?= $_SESSION['msg_success'] ?>", "success");
<?php unset($_SESSION['msg_success']); endif;?>

<?php if(isset($_SESSION['msg'])): ?>
Swal.fire("Error", "<?= $_SESSION['msg'] ?>", "error");
<?php unset($_SESSION['msg']); endif;?>
</script>

</body>
</html>
