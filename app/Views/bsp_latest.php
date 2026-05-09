<?= view('components/header') ?>
<?= view('components/sidebar') ?>

<style>
    /* Premium Variables */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.9);
        --php-color: #1aa053;
        --usd-color: #3a57e8;
        --eur-color: #f57323;
    }

    /* Black weight for impact */
    .fw-black { font-weight: 900; }
    
    /* Card Hover Effects */
    .market-card {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
    .market-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    /* Soft Avatar styling */
    .avatar-50 { height: 50px; width: 50px; min-width: 50px; }
    
    /* Rate Badge styling */
    .rate-pill {
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-family: 'Monaco', 'Consolas', monospace;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .pill-php { background: rgba(26, 160, 83, 0.1); color: var(--php-color); }
    .pill-usd { background: rgba(58, 87, 232, 0.1); color: var(--usd-color); }
    .pill-eur { background: rgba(245, 115, 35, 0.1); color: var(--eur-color); }

    /* Modern Table Header */
    .table-modern thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #edf2f9;
    }
</style>

<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="d-flex justify-content-between align-items-end">
                <div>
                    <h1 class="fw-bold mb-1">Market Snapshot</h1>
                    <p class="fw-bold opacity-75 mb-0">
                        <i class="far fa-calendar-check me-2"></i> 
                        Ref: <span class="fw-bold "><?= date("M d, Y", strtotime($last_date)) ?></span>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-soft-light fw-bold border border-white px-3 py-2" style="backdrop-filter: blur(5px);">
                        <i class="fas fa-satellite-dish fa-fade me-2 "></i> Real-time BSP Source
                    </span>
                </div>
            </div>
        </div>

        <?php 
            $highlights = ['USD', 'EUR', 'JPY'];
            foreach($rates as $row): 
                if(in_array($row['symbol'], $highlights)):
        ?>
            <div class="col-md-4">
                <div class="card market-card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-bold letter-spacing-1"><?= $row['country'] ?></span>
                                <h2 class="mt-1 mb-0 fw-bold">₱ <?= number_format($row['php_rate'], 4) ?></h2>
                                <div class="mt-1">
                                    <span class="badge rounded-pill bg-soft-primary text-primary"><?= $row['symbol'] ?> / PHP</span>
                                </div>
                            </div>
                            <div class="avatar-50 rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center shadow-sm">
                                <h3 class="mb-0 fw-bold"><?= substr($row['symbol'], 0, 1) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; endforeach; ?>

        <div class="col-sm-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h4 class="card-title fw-bold mb-0"><i class="fas fa-list-ul me-2 text-primary"></i>Currency Exchange Index</h4>
                    
                    <?php if(!empty($rates)): ?>
                        <div class="d-flex gap-2">
                            <?php if(!empty($rates[0]['file_name'])): ?>
                                <a href="http://10.216.15.10/ForexConversion/bsp_upload/<?= $rates[0]['file_name'] ?>" 
                                   class="btn btn-outline-success btn-sm border-2 fw-bold">
                                    <i class="fas fa-file-excel me-1"></i> EXCEL
                                </a>
                            <?php endif; ?>

                            <?php if(!empty($rates[0]['pdf_file_name'])): ?>
                                <a href="http://10.216.15.10/ForexConversion/bsp_upload/<?= $rates[0]['pdf_file_name'] ?>" 
                                   class="btn btn-outline-danger btn-sm border-2 fw-bold" target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Currency</th>
                                    <th class="text-center">Symbol</th>
                                    <th class="text-end">PHP Equiv.</th>
                                    <th class="text-end">USD Equiv.</th>
                                    <th class="text-end pe-4">EURO Equiv.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($rates)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-25 mb-3"><br>
                                            <span class="text-muted fw-bold">No market data available for this date.</span>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($rates as $row): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-40 rounded bg-soft-secondary text-secondary d-flex align-items-center justify-content-center me-3 fw-bold">
                                                        <?= substr($row['symbol'], 0, 2) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold fw-bold"><?= $row['country'] ?></div>
                                                        <div class="small text-muted"><?= $row['unit'] ?> Unit</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light fw-bold border fw-bold"><?= $row['symbol'] ?></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="rate-pill pill-php">₱ <?= number_format($row['php_rate'], 4) ?></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="rate-pill pill-usd">$ <?= number_format($row['usd_rate'], 4) ?></span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="rate-pill pill-eur">€ <?= number_format($row['euro_rate'], 4) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php if(!empty($rates)): ?>
                <div class="card-footer bg-light border-0 py-3">
                    <div class="d-flex align-items-center text-primary small fw-bold">
                        <i class="fas fa-quote-left me-2 opacity-50"></i>
                        <span>Source: <?= $rates[0]['reference_text'] ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= view('components/footer') ?>