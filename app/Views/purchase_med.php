<?= view('components/header') ?>
<?= view('components/sidebar') ?>
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <h5 class="mb-0 text-white">New Stock Purchase Entry</h5>
                    <small>Step 1: Header Info &nbsp;|&nbsp; Step 2: Add Meds</small>
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
                                    <label class="small fw-bold">Receipt (PDF Only)</label>
                                    <input type="file" name="attachment" class="form-control form-control-sm" accept="application/pdf" required>
                                </div>
                                <div class="alert alert-info py-2 mb-0">
                                    <small class="d-block text-muted">Grand Total:</small>
                                    <h4 class="fw-bold mb-0" id="grandTotalDisplay">₱ 0.00</h4>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold text-uppercase text-muted">Meds List</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-primary text-white" id="addRow">+ Add Row</button>
                                </div>
                                <div class="table-responsive" style="max-height: 250px;">
                                    <table class="table table-sm align-middle" id="itemsTable">
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
                                    <button type="submit" id="submitBtn" class="btn btn-primary px-5">Finalize Purchase & Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Recent Purchase Logs</h5>
                <div class="d-flex align-items-center gap-2">
                    <input type="date" id="filter_from" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>">
                    <span class="small">to</span>
                    <input type="date" id="filter_to" class="form-control form-control-sm" value="<?= date('Y-m-t') ?>">
                    <button type="button" id="btnFilter" class="btn btn-sm btn-dark px-3 ">Filter</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchaseHistoryTable" class="table table-hover w-100 small">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference No</th>
                                <th>Encoded By</th>
                                <th class="text-center">Attachment</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- // Breakdown modal -->
<div class="modal fade" id="breakdownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white text-white">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-receipt me-2"></i>Purchase Breakdown</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Medicine</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="breakdownBody">
                        </tbody>
                    <tfoot>
                        <tr class="table-active fw-bold">
                            <td colspan="3" class="text-end">Grand Total:</td>
                            <td class="text-end text-primary" id="breakdownTotal">₱ 0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<?= view('components/footer') ?>
<script>
// 1. GLOBAL FUNCTIONS (Move outside ready block so HTML 'onclick' can find them)
var historyTable;

function viewBreakdown(purchaseId) {
    $('#breakdownBody').html('<tr><td colspan="4" class="text-center p-4"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</td></tr>');
    $('#breakdownModal').modal('show');

    $.ajax({
        url: "<?= base_url('purchase/getPurchaseItems') ?>/" + purchaseId,
        type: "GET",
        dataType: "JSON",
        success: function(data) {
            let html = '';
            let grandTotal = 0;
            if(data.length > 0) {
                data.forEach(item => {
                    let subtotal = parseFloat(item.Subtotal);
                    grandTotal += subtotal;
                    html += `
                        <tr>
                            <td>
                                <span class="fw-bold d-block">${item.Description}</span>
                                <small class="text-muted">${item.Dosage}</small>
                            </td>
                            <td class="text-center">${item.Qty}</td>
                            <td class="text-end">₱ ${parseFloat(item.UnitPrice).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td class="text-end fw-bold">₱ ${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        </tr>`;
                });
            }
            $('#breakdownBody').html(html);
            $('#breakdownTotal').text('₱ ' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        }
    });
}

$(document).ready(function() {
    // 2. Initialize DataTable
    historyTable = $('#purchaseHistoryTable').DataTable({
        "ajax": { 
            "url": "<?= base_url('purchase/getPurchaseHistory') ?>", 
            "dataSrc": "",
            "data": function(d) {
                d.from = $('#filter_from').val();
                d.to = $('#filter_to').val();
            }
        },
        "columns": [
            { "data": "PurchaseDate" },
            { "data": "ReferenceNo" },
            { "data": "Encoder" },
            { 
                "data": "AttachmentPath",
                "className": "text-center",
                "render": function(data) {
                    if(!data) return '<span class="badge bg-soft-secondary text-secondary">No File</span>';
                    return `<a href="<?= base_url('uploads/receipts') ?>/${data}" target="_blank" 
                               class="btn btn-sm btn-rounded btn-outline-danger py-1 px-3" 
                               style="border-radius: 50px; font-size: 11px; font-weight: 600;">
                               <i class="fas fa-file-pdf me-1"></i> RECEIPT
                            </a>`;
                }
            },
            {
                "data": "Id",
                "className": "text-center",
                "render": function(data) {
                    return `<button class="btn btn-sm btn-dark px-3 rounded-pill" onclick="viewBreakdown(${data})" style="font-size: 11px;">
                                <i class="fas fa-search me-1"></i> ITEMS
                            </button>`;
                }
            }
        ],
        "order": [[0, "desc"]]
    });

    // 3. Filter Button
    $('#btnFilter').click(function() {
        historyTable.ajax.reload();
    });

    // 4. Duplicate Check
    $(document).on('change', 'select[name="med_id[]"]', function() {
        const currentSelect = $(this);
        const selectedValue = currentSelect.val();
        if (!selectedValue) return;

        let isDuplicate = false;
        $('select[name="med_id[]"]').not(currentSelect).each(function() {
            if ($(this).val() === selectedValue) {
                isDuplicate = true;
                return false;
            }
        });

        if (isDuplicate) {
            Swal.fire({
                icon: 'warning',
                title: 'Duplicate Entry',
                text: 'This medication is already added in another row.',
                confirmButtonColor: '#506cf0'
            });
            currentSelect.val('');
        }
    });

    // 5. Add Row Logic
    $('#addRow').click(function() {
        let row = `<tr>
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
            <td class="text-center"><button type="button" class="btn btn-link text-danger p-0 removeRow"><i class="fas fa-trash-alt"></i></button></td>
        </tr>`;
        $('#itemRows').append(row);
    });

    // 6. Calculations
    $(document).on('click', '.removeRow', function() { $(this).closest('tr').remove(); calculateTotal(); });
    $(document).on('input', '.calc', function() { calculateTotal(); });

    function calculateTotal() {
        let total = 0;
        $('#itemRows tr').each(function() {
            let qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            let price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
            total += qty * price;
        });
        $('#grandTotalDisplay').text('₱ ' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

    // 7. Form Submission
    $('#purchaseForm').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: "<?= base_url('purchase/store') ?>",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function(res) {
                btn.prop('disabled', false).text('Finalize Purchase & Save');
                if(res.status === 'success') {
                    Swal.fire('Success', res.message, 'success');
                    $('#purchaseForm')[0].reset();
                    
                    // Reset table and keep first row HTML structure consistent
                    $('#itemRows').html(`
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
                    `);
                    $('#grandTotalDisplay').text('₱ 0.00');
                    historyTable.ajax.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });
});
</script>