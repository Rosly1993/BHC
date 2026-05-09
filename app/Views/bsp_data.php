<?= view('components/header') ?>
<?= view('components/sidebar') ?>

<style>
    /* Modern Variables & Smoothness */
    :root {
        --bsp-primary: #3a57e8;
        --bsp-info: #08b1ba;
        --bsp-success: #1aa053;
        --bsp-danger: #c03221;
    }

    /* Glassmorphism & Elevation */
    .bsp-card-link {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        border-radius: 15px !important;
        overflow: hidden;
        background: #fff;
    }

    .bsp-card-link:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(58, 87, 232, 0.15) !important;
        border-color: var(--bsp-primary) !important;
    }

    .bsp-card-link .card-label {
        font-size: 0.7rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: 800;
        opacity: 0.6;
    }

    /* Month Card Specifics */
    .month-card {
        background: linear-gradient(135deg, #ffffff 0%, #f0faff 100%);
    }

    .month-card:hover {
        background: var(--bsp-info);
        color: white !important;
    }

    /* Breadcrumb styling */
    .custom-breadcrumb {
        background: rgba(58, 87, 232, 0.05);
        padding: 8px 20px;
        border-radius: 50px;
        display: inline-flex;
    }

    /* Table Improvements */
    .table-modern thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
        color: #555;
    }

    .rate-positive {
        color: var(--bsp-success);
        font-family: 'Monaco', 'Consolas', monospace;
    }

    .badge-soft-primary { background: rgba(58, 87, 232, 0.1); color: var(--bsp-primary); border: none; }
</style>

<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-none mb-4" style="background: transparent;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold mb-1">BSP Foreign Exchange</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custom-breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="<?= base_url('bsp_data') ?>" class="text-primary"><i class="fas fa-home me-1"></i> Home</a></li>
                                <?php if(isset($year) && $year): ?>
                                    <li class="breadcrumb-item <?= !$month ? 'active' : '' ?>">
                                        <?= $month ? '<a href="'.base_url("bsp_data/$year").'">'.$year.'</a>' : $year ?>
                                    </li>
                                <?php endif; ?>
                                <?php if(isset($month) && $month): ?>
                                    <li class="breadcrumb-item active fw-bold"><?= date("F", mktime(0, 0, 0, $month, 10)) ?></li>
                                <?php endif; ?>
                            </ol>
                        </nav>
                    </div>

                    <?php if($view == 'table' && !empty($rates)): ?>
                       <div class="d-flex gap-2">
                        <?php if(!empty($rates[0]['file_name'])): ?>
                            <a href="http://10.216.15.10/ForexConversion/bsp_upload/<?= $rates[0]['file_name'] ?>" 
                            class="btn btn-success shadow-sm btn-sm border">
                                <i class="fas fa-file-excel me-1 text-white"></i> Excel Source
                            </a>
                        <?php endif; ?>

                        <?php if(!empty($rates[0]['pdf_file_name'])): ?>
                            <a href="http://10.216.15.10/ForexConversion/bsp_upload/<?= $rates[0]['pdf_file_name'] ?>" 
                            class="btn btn-danger btn-sm shadow-sm" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> Official PDF
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row">
                        
                        <?php if($view == 'years'): ?>
                            <?php foreach($items as $item): ?>
                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                    <a href="<?= base_url("bsp_data/{$item['year']}") ?>" class="card bsp-card-link text-center p-4 mb-4 text-decoration-none">
                                        <span class="card-label text-primary">Archive</span>
                                        <h3 class="mb-0 mt-2 fw-black"><?= $item['year'] ?></h3>
                                    </a>
                                </div>
                            <?php endforeach; ?>

                        <?php elseif($view == 'months'): ?>
                            <?php foreach($items as $item): ?>
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <a href="<?= base_url("bsp_data/$year/{$item['month_num']}") ?>" class="card bsp-card-link month-card text-center p-4 mb-4 text-decoration-none border-info">
                                        <h4 class="mb-0 fw-bold"><?= $item['month_name'] ?></h4>
                                        <span class="small opacity-75 mt-1"><?= $year ?></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>

                        <?php elseif($view == 'dates'): ?>
                            <div class="col-12 mb-3"><p class="text-secondary fw-bold small">SELECT REFERENCE DATE</p></div>
                            <?php foreach($items as $item): ?>
                                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                    <a href="<?= base_url("bsp_data/$year/$month/{$item['full_date']}") ?>" class="btn btn-white border w-100 mb-3 py-3 shadow-none bsp-card-link transition-all">
                                        <i class="far fa-calendar-alt me-2 text-primary"></i>
                                        <span class="fw-bold"><?= date("M d", strtotime($item['full_date'])) ?></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>

                        <?php elseif($view == 'table'): ?>
                            <div class="col-12">
                                <div class="alert alert-left alert-primary border-0 shadow-none mb-4" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Data Source:</strong> <?= $rates[0]['reference_text'] ?>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-modern align-middle">
                                        <thead>
                                            <tr>
                                                <th class="ps-0">Country / Region</th>
                                                <th>Unit</th>
                                                <th class="text-center">Symbol</th>
                                                <th class="text-end pe-0">PHP Equiv. Rate</th>
                                                <th class="text-end pe-0">USD Equiv. Rate</th>
                                                <th class="text-end pe-0">EURO Equiv. Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($rates as $row): ?>
                                                <tr>
                                                    <td class="ps-0">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-40 rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center me-3">
                                                                <i class="fas fa-globe-asia"></i>
                                                            </div>
                                                            <span class="fw-bold text-dark"><?= $row['country'] ?></span>
                                                        </div>
                                                    </td>
                                                    <td><?= $row['unit'] ?></td>
                                                    <td class="text-center">
                                                        <span class="badge badge-soft-primary px-3 py-2"><?= $row['symbol'] ?></span>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <h5 class="rate-positive mb-0">₱ <?= number_format($row['php_rate'], 4) ?></h5>
                                                    </td>
                                                     <td class="text-end pe-0">
                                                        <h5 class="rate-positive mb-0">$ <?= number_format($row['usd_rate'], 4) ?></h5>
                                                    </td>
                                                     <td class="text-end pe-0">
                                                        <h5 class="rate-positive mb-0">€ <?= number_format($row['euro_rate'], 4) ?></h5>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(empty($items) && empty($rates)): ?>
                            <div class="col-12 text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="mb-3 opacity-25">
                                <h4 class="text-secondary">No records found for this selection</h4>
                                <a href="<?= base_url('bsp_data') ?>" class="btn btn-primary mt-3">Reset Filters</a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('components/footer') ?>