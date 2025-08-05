<?php include('../includes/header.php'); ?>
<h2>Key KPIs</h2>
<canvas id="kpiChart" width="400" height="200"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('kpiChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Students', 'Parents', 'Schools'],
        datasets: [{
            label: 'Entity Count',
            data: [50, 30, 10],
            backgroundColor: ['#007bff', '#28a745', '#ffc107']
        }]
    }
});
</script>
<?php include('../includes/footer.php'); ?>