<?= view('components/header') ?>
<?= view('components/sidebar') ?>

<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">Issue Medication</h5>
                </div>
                <div class="card-body">
                    <form id="issueForm">
                        <div class="mb-3">
                            <label>Select Medicine</label>
                            <select name="Med_id" id="Med_id" class="form-select select2" required>
                                <option value="">-- Select --</option>
                                <?php foreach($meds as $m): ?>
                                    <option value="<?= $m->Med_id ?>" data-stock="<?= $m->Qty ?>">
                                        <?= $m->Description ?> (<?= $m->Dosage ?>) - Stock: <?= $m->Qty ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Issued To (Patient Name)</label>
                            <input type="text" name="IssuedTo" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label>Age</label>
                                <input type="number" name="Age" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label>Quantity</label>
                                <input type="number" name="Qty" id="issueQty" class="form-control" required min="1">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-4">Confirm Issuance</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Issuance Log</h5>
                    <div class="row mb-3 align-items-end">
    <div class="col-md-4">
        <label class="small fw-bold">Start Date</label>
        <input type="date" id="filter_start" class="form-control form-control-sm">
    </div>
    <div class="col-md-4">
        <label class="small fw-bold">End Date</label>
        <input type="date" id="filter_end" class="form-control form-control-sm">
    </div>
    <div class="col-md-4">
        <button id="btn_filter" class="btn btn-sm btn-info w-100"><i class="fas fa-filter"></i> Apply Filter</button>
    </div>
</div>
                </div>
                
                <div class="card-body">
                    <table id="issueLogTable" class="table table-sm table-hover w-100">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Medicine</th>
                                <th>Qty</th>
                                <th>Issuer</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('components/footer') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize the Log Table
    var logTable = $('#issueLogTable').DataTable({
        "dom": '<"d-flex justify-content-between align-items-center mb-3"Bf>rtip', 
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Medication Issuance Log'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Medication Issuance Log',
                orientation: 'portrait',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-secondary btn-sm'
            }
        ],
        "ajax": { 
            "url": "<?= base_url('issuance/getIssuanceLog') ?>", 
            "dataSrc": "",
            "data": function(d) {
                d.start = $('#filter_start').val();
                d.end = $('#filter_end').val();
            }
        },
        "columns": [
            { "data": "DateIssued" },
            { 
                "data": "IssuedTo", 
                "render": function(data, t, row){ 
                    return `<b>${data}</b> <br><small class="text-muted">Age: ${row.Age}</small>`; 
                } 
            },
            { 
                "data": "Description", 
                "render": function(data, t, row){ return `${data} (${row.Dosage})`; } 
            },
            { "data": "Qty", "className": "text-center fw-bold text-primary" },
            { "data": "Issuer" }
        ]
    });

    // Apply Filter Button
    $('#btn_filter').on('click', function() {
        logTable.ajax.reload();
    });

    // Function to refresh the dropdown without reloading the page
    function refreshMedDropdown() {
        $.ajax({
            url: "<?= base_url('issuance/getAvailableMeds') ?>",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                let dropdown = $('#Med_id');
                dropdown.empty(); 
                dropdown.append('<option value="">-- Select --</option>');
                $.each(data, function(key, val) {
                    dropdown.append(`<option value="${val.Med_id}" data-stock="${val.Qty}">${val.Description} (${val.Dosage}) - Stock: ${val.Qty}</option>`);
                });
            }
        });
    }

    $('#issueForm').on('submit', function(e) {
        e.preventDefault();
        
        let selected = $('#Med_id option:selected');
        let stock = parseInt(selected.data('stock'));
        let qty = parseInt($('#issueQty').val());

        if(qty > stock) {
            Swal.fire('Insufficient Stock', `You only have ${stock} units available.`, 'error');
            return;
        }

        $.ajax({
            url: "<?= base_url('issuance/store') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "JSON",
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Issued!', text: res.message, timer: 1500, showConfirmButton: false });
                    $('#issueForm')[0].reset();
                    logTable.ajax.reload(null, false);
                    refreshMedDropdown();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });
});
</script>