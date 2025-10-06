<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    // Option 1: file uploaded via form
    if (isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
        $path = $_FILES['csv_file']['tmp_name'];
    } elseif (!empty($_POST['server_file'])) {
        // Option 2: use file already placed in uploads/ (edit it via file manager)
        $safe = basename($_POST['server_file']);
        $path = __DIR__ . '/uploads/' . $safe;
        if (!file_exists($path)) { $msg = 'Server file not found.'; $path = null; }
    } else {
        $path = null;
    }

    if ($path) {
        if (($handle = fopen($path, 'r')) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $row++;
                if ($row==1) { continue; } // skip header
                $date = trim($data[0]);
                $rain = trim($data[1]);
                $notes = isset($data[2]) ? trim($data[2]) : '';
                if (!$date || $rain==='') continue;
                $stmt = $conn->prepare("INSERT INTO rain_data (user_id, reading_date, rainfall_mm, notes) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isds", $user_id, $date, $rain, $notes);
                $stmt->execute();
            }
            fclose($handle);
            $msg = 'CSV imported successfully.';
        } else {
            $msg = 'Failed to open file.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>RainMeter - Import CSV</title>
<link rel="stylesheet" href="assets/style.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="container">
  <section class="card">
    <h2>Import Old Rainfall Data</h2>
    <?php if($msg): ?><div class="info"><?=htmlspecialchars($msg)?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <label>Upload CSV file</label>
      <input type="file" name="csv_file" accept=".csv">
      <label>Or use server-side CSV (place file in /uploads and enter filename)</label>
      <input type="text" name="server_file" placeholder="previous_data.csv">
      <div class="row">
        <button type="submit" class="btn">Import</button>
      </div>
    </form>
    <p class="muted">CSV format: <b>date,rainfall_mm,notes</b> (date in YYYY-MM-DD)</p>
  </section>
</main>
</body>
</html>
