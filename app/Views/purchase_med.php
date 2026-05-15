<?= view('components/header') ?>
<?= view('components/sidebar') ?>

<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">New Stock Purchase Entry</h5>
                </div>
                <div class="card-body">
                    <form id="purchaseForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-3 border-end">
                                <div class="mb-3">
                                    <label class="small fw-bold">Date Purchased</label>
                                    <input type="date" name="PurchaseDate" class="form-control form-control-sm" required value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">Ref/OR Number</label>
                                    <input type="text" name="ReferenceNo" class="form-control form-control-sm" required placeholder="INV-XXXX">
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">Receipt (PDF)</label>
                                    <input type="file" name="attachment" class="form-control form-control-sm" accept="application/pdf" required>
                                </div>
                                <div class="alert alert-info py-2 mb-0 text-center">
                                    <small class="d-block text-muted">Grand Total:</small>
                                    <h4 class="fw-bold mb-0" id="grandTotalDisplay">₱ 0.00</h4>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small fw-bold text-muted text-uppercase">Meds List</span>
                                    <button type="button" class="btn btn-sm btn-primary px-3" id="addRow">+ Add Row</button>
                                </div>
                                <div class="table-responsive" style="max-height: 250px;">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light small">
                                            <tr>
                                                <th width="50%">Medicine Description</th>
                                                <th width="15%">Qty</th>
                                                <th width="25%">Price (₱)</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemRows">
                                            <tr>
                                                <td>
                                                    <select name="med_id[]" class="form-select form-select-sm" required>
                                                        <option value="">-- Choose --</option>
                                                        <?php foreach($meds as $m): ?>
                                                            <option value="<?= $m->Id ?>"><?= $m->Description ?> (<?= $m->Dosage ?>)</option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td><input type="number" name="qty[]" class="form-control form-control-sm calc" required min="1"></td>
                                                <td><input type="number" name="price[]" class="form-control form-control-sm calc" required step="0.01"></td>
                                                <td class="text-center text-muted"><i class="fas fa-lock small"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <div class="text-end">
                                    <button type="submit" id="submitBtn" class="btn btn-primary px-5">Finalize & Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <small class="text-uppercase fw-bold opacity-75">Period Spend</small>
                    <h3 class="mb-0 text-white fw-bold" id="statsTotalSpend">₱ 0.00</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-body">
                    <small class="text-uppercase fw-bold opacity-75">Units Stocked</small>
                    <h3 class="mb-0 text-white fw-bold" id="statsTotalQty">0 Units</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <small class="text-uppercase fw-bold opacity-75">Reports</small>
                    <div class="mt-1">
                        <button class="btn btn-sm btn-light py-0 px-3 fw-bold" onclick="downloadDetailedReport()">
                            <i class="fas fa-file-download me-1"></i> Detailed CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white">Recent Purchase Logs</h5>
                    <div class="d-flex align-items-center gap-2">
                        <input type="date" id="filter_from" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>">
                        <input type="date" id="filter_to" class="form-control form-control-sm" value="<?= date('Y-m-t') ?>">
                        <button type="button" id="btnFilter" class="btn btn-sm btn-dark px-3">Filter</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="purchaseHistoryTable" class="table table-hover w-100 small">
                            <thead>
                                <tr>
                                    <th width="5%"></th>
                                    <th>Date</th>
                                    <th>Ref No</th>
                                    <th>Encoder</th>
                                    <th class="text-end">Items Qty</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-center">Receipt</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('components/footer') ?>

<script>
$(document).ready(function() {
    // 1. Child Row Logic
    function formatChildRow(d) {
        let childTable = `
            <div class="p-3 bg-light rounded border shadow-sm mx-3 my-2">
                <div class="d-flex justify-content-between mb-2">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-list me-2"></i>Invoice Breakdown: ${d.ReferenceNo}</h6>
                    <button class="btn btn-xs btn-outline-dark py-0 px-2" onclick="window.print()"><i class="fas fa-print"></i></button>
                </div>
                <table class="table table-sm table-bordered bg-white mb-0 shadow-none">
                    <thead class="table-dark">
                        <tr>
                            <th>Medicine Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="details-row-${d.Id}">
                        <tr><td colspan="4" class="text-center p-3">Fetching details...</td></tr>
                    </tbody>
                </table>
            </div>`;

        $.get("<?= base_url('purchase/getPurchaseItems') ?>/" + d.Id, function(items) {
            let rows = '';
            items.forEach(item => {
                rows += `<tr>
                    <td>${item.Description} <small class="text-muted">(${item.Dosage})</small></td>
                    <td class="text-center">${item.Qty}</td>
                    <td class="text-end">₱${parseFloat(item.UnitPrice).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td class="text-end fw-bold">₱${parseFloat(item.Subtotal).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                </tr>`;
            });
            $(`#details-row-${d.Id}`).html(rows);
        });
        return childTable;
    }

    // 2. DataTables with Analytics and Exports
    var historyTable = $('#purchaseHistoryTable').DataTable({
        "dom": '<"d-flex justify-content-between align-items-center mb-3"Bf>rtip',
        "buttons": [
            { extend: 'excelHtml5', title: 'Summary_Report', className: 'btn btn-sm btn-success rounded-pill px-3 me-2', text: '<i class="fas fa-file-excel me-1"></i> Excel' },
            { extend: 'pdfHtml5', title: 'Summary_Report', className: 'btn btn-sm btn-danger rounded-pill px-3', text: '<i class="fas fa-file-pdf me-1"></i> PDF' }
        ],
        "ajax": { 
            "url": "<?= base_url('purchase/getPurchaseHistory') ?>", 
            "data": function(d) { d.from = $('#filter_from').val(); d.to = $('#filter_to').val(); },
            "dataSrc": ""
        },
        "columns": [
            { "className": 'dt-control text-center', "orderable": false, "data": null, "defaultContent": '<i class="fas fa-plus-circle text-primary"></i>' },
            { "data": "PurchaseDate" },
            { "data": "ReferenceNo" },
            { "data": "Encoder" },
            { "data": "TotalQty", "className": "text-end fw-bold" },
            { "data": "GrandTotal", "className": "text-end text-primary fw-bold", "render": d => '₱' + parseFloat(d).toLocaleString(undefined, {minimumFractionDigits: 2}) },
            { "data": "AttachmentPath", "className": "text-center", "render": d => d ? `<a href="<?= base_url('uploads/receipts') ?>/${d}" target="_blank" class="badge border border-danger text-danger">PDF</a>` : '-' }
        ],
        "drawCallback": function() {
            let api = this.api();
            let total = api.column(5, {page:'current'}).data().reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
            let qty = api.column(4, {page:'current'}).data().reduce((a, b) => parseInt(a) + parseInt(b), 0);
            $('#statsTotalSpend').text('₱ ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
            $('#statsTotalQty').text(qty.toLocaleString() + ' Units');
        }
    });

    // 3. Control Logic
    $('#purchaseHistoryTable tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = historyTable.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            $(this).find('i').removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
        } else {
            row.child(formatChildRow(row.data())).show();
            $(this).find('i').removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
        }
    });

    $('#btnFilter').click(() => historyTable.ajax.reload());

    $('#addRow').click(() => {
        $('#itemRows').append(`<tr><td><select name="med_id[]" class="form-select form-select-sm" required><option value="">-- Choose --</option><?php foreach($meds as $m): ?><option value="<?= $m->Id ?>"><?= $m->Description ?> (<?= $m->Dosage ?>)</option><?php endforeach; ?></select></td><td><input type="number" name="qty[]" class="form-control form-control-sm calc" required min="1"></td><td><input type="number" name="price[]" class="form-control form-control-sm calc" required step="0.01"></td><td class="text-center"><button type="button" class="btn btn-link text-danger p-0 removeRow"><i class="fas fa-trash-alt"></i></button></td></tr>`);
    });

    // --- NEW DUPLICATE MEDICATION CHECK ---
    $(document).on('change', 'select[name="med_id[]"]', function() {
        let currentSelect = $(this);
        let selectedValue = currentSelect.val();
        
        if (selectedValue !== "") {
            let matchCount = 0;
            
            // Loop through all medicine dropdowns to search for duplicates
            $('select[name="med_id[]"]').each(function() {
                if ($(this).val() === selectedValue) {
                    matchCount++;
                }
            });
            
            // If the same value is found more than once, alert and reset selection
            if (matchCount > 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Duplicate Item',
                    text: 'This medicine has already been added to the purchase entry list.'
                });
                currentSelect.val(""); // Clear selection
            }
        }
    });

    $(document).on('click', '.removeRow', function() { $(this).closest('tr').remove(); calculateTotal(); });
    $(document).on('input', '.calc', () => calculateTotal());

    function calculateTotal() {
        let total = 0;
        $('#itemRows tr').each(function() {
            let q = $(this).find('input[name="qty[]"]').val() || 0;
            let p = $(this).find('input[name="price[]"]').val() || 0;
            total += (q * p);
        });
        $('#grandTotalDisplay').text('₱ ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

   $('#purchaseForm').on('submit', function(e) {
    e.preventDefault(); // Prevents native browser refresh
    
    $.ajax({
        url: "<?= base_url('purchase/store') ?>",
        type: "POST",
        data: new FormData(this),
        contentType: false, 
        processData: false,
        success: (res) => {
            // 1. Show the success alert box
            Swal.fire('Success', res.message, 'success');
            
            // 2. Reset the main form fields (Date, Ref No, File upload)
            $('#purchaseForm')[0].reset();
            
            // 3. Remove all extra appended dynamic rows except the first one
            $('#itemRows tr').not(':first').remove();
            
            // 4. Reset the values of the remaining first row
            let firstRow = $('#itemRows tr:first');
            firstRow.find('select').val('');
            firstRow.find('input').val('');
            
            // 5. Recalculate totals to update the UI display back to ₱ 0.00
            calculateTotal();
            
            // 6. Reload the history table data dynamically without a full page refresh
            if (typeof historyTable !== 'undefined') {
                historyTable.ajax.reload(null, false); 
            }
        },
        error: (err) => {
            Swal.fire('Error', 'Something went wrong while saving.', 'error');
        }
    });
});
});

function downloadDetailedReport() {
    let f = $('#filter_from').val();
    let t = $('#filter_to').val();
    window.location.href = `<?= base_url('purchase/exportDetailed') ?>?from=${f}&to=${t}`;
}
</script>