<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>RainMeter - Graphs</title>
<link rel="stylesheet" href="assets/style.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="container">
  <section class="card">
    <h2>Rainfall Charts</h2>
    <div class="chart-controls">
      <button data-view="daily" class="btn small chartView active">Daily</button>
      <button data-view="monthly" class="btn small chartView">Monthly</button>
      <button data-view="yearly" class="btn small chartView">Yearly</button>
    </div>
    <canvas id="chartCanvas" height="180"></canvas>
  </section>
</main>

<script>
async function fetchData(){
  const res = await fetch('get_data.php');
  return res.json();
}

let chart = null;
let currentView = 'daily';

document.querySelectorAll('.chartView').forEach(b=>{
  b.addEventListener('click', ()=>{
    document.querySelectorAll('.chartView').forEach(x=>x.classList.remove('active'));
    b.classList.add('active');
    currentView = b.getAttribute('data-view');
    render();
  });
});

async function render(){
  const data = await fetchData();
  const ctx = document.getElementById('chartCanvas').getContext('2d');
  let labels=[], values=[];
  if (currentView==='daily'){
    labels = data.map(d=>d.reading_date);
    values = data.map(d=>parseFloat(d.rainfall_mm));
  } else if (currentView==='monthly'){
    const agg={};
    data.forEach(d=>{
      const m = d.reading_date.slice(0,7);
      agg[m] = (agg[m]||0) + parseFloat(d.rainfall_mm);
    });
    labels = Object.keys(agg).sort();
    values = labels.map(k=>agg[k]);
  } else {
    const agg={};
    data.forEach(d=>{
      const y = d.reading_date.slice(0,4);
      agg[y] = (agg[y]||0) + parseFloat(d.rainfall_mm);
    });
    labels = Object.keys(agg).sort();
    values = labels.map(k=>agg[k]);
  }

  if (chart) chart.destroy();
  chart = new Chart(ctx, {
    type: 'bar',
    data: { labels, datasets: [{ label: 'Rainfall (mm)', data: values }] },
    options: { responsive:true, maintainAspectRatio:false }
  });
}

render();
</script>
</body>
</html>
