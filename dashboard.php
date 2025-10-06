<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'config.php';
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>RainMeter - Dashboard</title>
<link rel="stylesheet" href="assets/style.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="container">

  <!-- Add/Edit Form -->
  <section class="card form-card">
    <h2>Add / Edit Reading</h2>
    <form id="rainForm">
      <input type="hidden" name="id" id="rid">
      <label>Date</label>
      <input type="date" name="reading_date" id="reading_date" required>
      <label>Rainfall (mm)</label>
      <input type="number" step="0.01" name="rainfall_mm" id="rainfall_mm" required>
      <label>Notes</label>
      <input type="text" name="notes" id="notes" placeholder="Optional">
      <div class="row">
        <button type="submit" class="btn">Save</button>
        <button type="button" id="clearBtn" class="btn alt">Clear</button>
      </div>
    </form>
  </section>

  <!-- Readings Table -->
  <section class="card">
    <h2>Readings</h2>
    <div class="controls">
      <label>From <input type="date" id="filter_from"></label>
      <label>To <input type="date" id="filter_to"></label>
      <button id="filterBtn" class="btn small">Filter</button>
    </div>

    <!-- Export PDF (server-side) -->
    <form method="GET" action="export_pdf.php" target="_blank" class="controls">
      <label>From <input type="date" name="from" required></label>
      <label>To <input type="date" name="to" required></label>
      <button type="submit" class="btn small">Export PDF</button>
    </form>

    <div id="tableWrap">
      <table id="dataTable">
        <thead>
          <tr>
            <th>Date</th>
            <th>Rainfall (mm)</th>
            <th>Notes</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </section>
</main>

<script>
// fetch JSON helper
async function fetchJSON(url, opts){ const r = await fetch(url, opts); return r.json(); }

async function loadData(from='', to='') {
  let url = 'get_data.php';
  if (from && to) url += `?from=${from}&to=${to}`;
  const data = await fetchJSON(url);
  const tbody = document.querySelector('#dataTable tbody');
  tbody.innerHTML = '';
  data.forEach(r => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${r.reading_date}</td>
      <td>${r.rainfall_mm}</td>
      <td>${r.notes}</td>
      <td>
        <button class="btn tiny" data-id="${r.id}" onclick="editItem(${r.id})">Edit</button>
        <button class="btn tiny alt" onclick="deleteItem(${r.id})">Delete</button>
      </td>`;
    tbody.appendChild(tr);
  });
}

document.getElementById('rainForm').addEventListener('submit', async (e)=>{
  e.preventDefault();
  const form = new FormData(e.target);
  await fetch('add_rain.php', {method:'POST', body: form});
  clearForm();
  loadData(document.getElementById('filter_from').value, document.getElementById('filter_to').value);
});

function clearForm(){
  document.getElementById('rid').value='';
  document.getElementById('reading_date').value='';
  document.getElementById('rainfall_mm').value='';
  document.getElementById('notes').value='';
}
document.getElementById('clearBtn').addEventListener('click', clearForm);

async function editItem(id){
  const res = await fetchJSON('get_item.php?id='+id);
  document.getElementById('rid').value = res.id;
  document.getElementById('reading_date').value = res.reading_date;
  document.getElementById('rainfall_mm').value = res.rainfall_mm;
  document.getElementById('notes').value = res.notes;
}

async function deleteItem(id){
  if (!confirm('Delete this reading?')) return;
  await fetch('delete_rain.php', {method:'POST', body: new URLSearchParams({id})});
  loadData(document.getElementById('filter_from').value, document.getElementById('filter_to').value);
}

document.getElementById('filterBtn').addEventListener('click', ()=>{
  const f = document.getElementById('filter_from').value;
  const t = document.getElementById('filter_to').value;
  loadData(f,t);
});

loadData();
</script>
</body>
</html>