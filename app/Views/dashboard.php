<?= view('components/header') ?>
<?= view('components/sidebar') ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-2">
                    <form method="GET" action="<?= base_url('dashboard') ?>" class="row align-items-center">
                        <div class="col-auto"><span class="fw-bold text-muted">Filter Analytics:</span></div>
                        <div class="col-auto">
                            <input type="date" name="start" class="form-control form-control-sm" value="<?= $startDate ?>">
                        </div>
                        <div class="col-auto text-muted">to</div>
                        <div class="col-auto">
                            <input type="date" name="end" class="form-control form-control-sm" value="<?= $endDate ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm px-4">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-primary text-white mb-4 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-uppercase small">Issuances in Period</h6>
                            <h2 class="fw-bold mb-0"><?= $issuanceCount ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-info text-white mb-4 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-uppercase small">Critical Items Count</h6>
                            <h2 class="fw-bold mb-0"><?= $criticalCount ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white"><h5 class="fw-bold mb-0">Issuance Trends (Last 7 Days)</h5></div>
                <div class="card-body">
                    <canvas id="trendChart" style="height: 300px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle"></i> Critical Stock Details</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if(empty($criticalMeds)): ?>
                            <div class="p-4 text-center text-muted">All stock healthy!</div>
                        <?php else: ?>
                            <?php foreach($criticalMeds as $med): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <span class="fw-bold d-block small"><?= $med->Description ?></span>
                                    <small class="text-danger fw-bold"><?= $med->Qty ?> Units Left</small>
                                </div>
                                <a href="<?= base_url('medlist') ?>?Id=<?= $med->Id ?>" class="btn btn-sm btn-outline-danger p-1">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white"><h5 class="fw-bold mb-0">Top 5 Medications Issued</h5></div>
                <div class="card-body">
                    <canvas id="medBarChart" style="height: 250px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof jQuery !== 'undefined') {
        initDashboard();
    }
});

function initDashboard() {
    $.ajax({
        url: "<?= base_url('dashboard/getChartData') ?>",
        type: "GET",
        dataType: "JSON",
        success: function(res) {
            // Debug: Check your console (F12) to see if 'date_label' is present
            console.log("Dashboard Data:", res);

            const medsData = res.issuedMeds || [];
            const trendsData = res.dailyTrends || [];

            // --- BAR CHART (Top Medications) ---
            const ctxBar = document.getElementById('medBarChart');
            if (ctxBar) {
                new Chart(ctxBar.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: medsData.map(i => i.Description),
                        datasets: [{
                            label: 'Units Issued',
                            data: medsData.map(i => i.total_qty),
                            backgroundColor: '#506cf0',
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // --- AREA CHART (Daily Trends) ---
            const ctxLine = document.getElementById('trendChart');
            if (ctxLine) {
                new Chart(ctxLine.getContext('2d'), {
                    type: 'line',
                    data: {
                        // FIX: Changed 'i.date' to 'i.date_label' to match your Controller
                        labels: trendsData.map(i => i.date_label), 
                        datasets: [{
                            label: 'Issuances',
                            data: trendsData.map(i => i.count),
                            fill: true,
                            backgroundColor: 'rgba(80, 108, 240, 0.1)',
                            borderColor: '#506cf0',
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#506cf0'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { 
                                    stepSize: 1,
                                    callback: function(value) { if (value % 1 === 0) { return value; } }
                                } 
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        },
        error: function(xhr) {
            console.error("Dashboard AJAX failed to load data.");
        }
    });
}
</script>

<?= view('components/footer') ?>