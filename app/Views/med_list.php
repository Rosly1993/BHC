<?= view('components/header') ?>
<?= view('components/sidebar') ?>

<style>
    .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .table thead th { background-color: #f8f9fa; font-size: 0.8rem; letter-spacing: 0.5px; }
    .badge-soft-success { background: #e2f6e9; color: #28a745; border: 1px solid #28a745; }
    .badge-soft-secondary { background: #f1f1f1; color: #6c757d; border: 1px solid #6c757d; }

    /* Pulsing animation for critical stock */
    .pulse-danger {
        animation: pulse-red 2s infinite;
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }

    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    /* Soft Badge Styles */
    .badge-stock {
        padding: 0.5em 0.8em;
        border-radius: 50px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
</style>
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills mb-4 justify-content-center" id="med-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4" id="registry-tab" data-bs-toggle="pill" data-bs-target="#pills-registry" type="button" role="tab">
                                <i class="fas fa-list me-2"></i>Med Registry
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4" id="inventory-tab" data-bs-toggle="pill" data-bs-target="#pills-inventory" type="button" role="tab">
                                <i class="fas fa-boxes me-2"></i>Current Inventory
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-registry" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="fw-bold">Medicine Definitions</h4>
                                <button class="btn btn-primary rounded-pill" onclick="openModal()">+ Add New Med</button>
                            </div>
                            <table id="medTable" class="table table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Dosage</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="pills-inventory" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="fw-bold">Stock Levels</h4>
                            </div>
                            <table id="inventoryTable" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Dosage</th>
                                        <th>Current Qty</th>
                                        <th>Last Stock In</th>
                                        <th>Updated By</th>
                                        <th>History</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="medModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 60%">
        <div class="modal-content border-0 shadow-lg">
            <form id="medForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalLabel">Medicine Info</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="Id" id="medId">
                     <div class="row">
                              <div class="col-md-6">
                    <div class="form-floating mb-3">
                   
                        <input type="text" name="Description" id="Description" class="form-control" placeholder="Description" required>
                        <label>Description</label>
                    </div>
                    </div>

                    <div class="col-6">
                        <div class="form-floating mb-3">
                            <input type="number" name="Quantity" id="Quantity" class="form-control" placeholder="0" min="0" style="cursor: not-allowed;" readonly>
                            <label>Add Stock Quantity (Stock In)</label>
                            
                        </div>
                    </div>
                </div>
                
                     <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select name="Dosage" id="Dosage" class="form-select" required>
                                    <option value="">Choose...</option>
                                    <?php foreach($dosages as $d): ?>
                                        <option value="<?= $d->Dosage ?>"><?= $d->Dosage ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Dosage</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select name="Type" id="Type" class="form-select" required>
                                    <option value="">Choose...</option>
                                    <?php foreach($types as $t): ?>
                                        <option value="<?= $t->Type ?>"><?= $t->Type ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Type</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- // history openModal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-width: 70%">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-history me-2"></i>Stock Movement History</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="historyTable">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Qty Change</th>
                                <th>Details</th>
                                <th>Processed By</th>
                            </tr>
                        </thead>
                        <tbody id="historyBody">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('components/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var table;
    var invTable;

    $(document).ready(function() {
        // 1. Initialize Registry Table
        table = $('#medTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": { 
                "url": "<?= base_url('medlist/getMedList') ?>", 
                "type": "POST" 
            },
            "columns": [
                { "data": "Description" },
                { "data": "Dosage" },
                { "data": "Type" },
                { 
                    "data": "Isactive",
                    "defaultContent": "0",
                    "render": function(data, type, row) {
                        return (data == 1) ? 
                            '<span class="badge badge-soft-success">Active</span>' : 
                            '<span class="badge badge-soft-secondary">Inactive</span>';
                    } 
                },
                { 
                    "data": null, 
                    "defaultContent": "", // Prevents the 'null' parameter error
                    "render": function(data, type, row) {
                        return `
                            <div class="btn-group shadow-sm bg-white rounded">
                                <button title="Edit" class="btn btn-sm btn-outline-primary border-0" onclick='editMed(${JSON.stringify(row)})'><i class="fas fa-edit"></i></button>
                              
                            </div>`;
                    } 
                }
            ]
        });
  // <button title="Delete" class="btn btn-sm btn-outline-danger border-0" onclick="deleteMed(${row.Id})"><i class="fas fa-trash"></i></button>
        // 2. Initialize Inventory Table
            invTable = $('#inventoryTable').DataTable({
                "ajax": { 
                    "url": "<?= base_url('medlist/getInventory') ?>", 
                    "type": "POST", // Add this line to match your route
                    "dataSrc": "" 
                },
                    "columns": [
                { "data": "Description" },
                { "data": "Dosage" },
                { 
                "data": "Qty",
                "render": function(data) {
                    let badgeClass = "";
                    let icon = "";
                    let pulse = "";

                    if (data <= 0) {
                        badgeClass = "bg-danger text-white"; // Out of stock
                        icon = '<i class="fas fa-times-circle"></i>';
                        pulse = "pulse-danger";
                    } else if (data < 10) {
                        badgeClass = "bg-soft-danger text-danger border border-danger"; // Critical
                        icon = '<i class="fas fa-exclamation-triangle"></i>';
                        pulse = "pulse-danger";
                    } else if (data < 20) {
                        badgeClass = "bg-soft-warning text-warning border border-warning"; // Warning
                        icon = '<i class="fas fa-info-circle"></i>';
                    } else {
                        badgeClass = "bg-soft-success text-success border border-success"; // Good
                        icon = '<i class="fas fa-check-circle"></i>';
                    }

                    return `<span class="badge-stock ${badgeClass} ${pulse}">
                                ${icon} ${data} Units
                            </span>`;
                }
                },
                { "data": "DateAdded" },
                { "data": "firstname" },
                { 
                    "data": null, 
                    "defaultContent": "",
                    "render": function(data, type, row) {
                        // row.Med_id refers to the ID in the inventory table join
                        return `<button class="btn btn-sm btn-outline-info" onclick="viewHistory(${row.Med_id})"><i class="fas fa-history"></i> History</button>`;
                    }
                }
            ]
        });

        // 3. Fix for DataTable width when switching tabs
        $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        });

        // 4. Handle Form Submission
        $('#medForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= base_url('medlist/store') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                success: function(res) {
                    if (res.status === 'success') {
                        $('#medModal').modal('hide');
                        
                        // Reload BOTH tables
                        table.ajax.reload(null, false);
                        invTable.ajax.reload(null, false);
                        
                        Swal.fire({ 
                            icon: 'success', title: 'Success', text: res.message, 
                            timer: 1500, showConfirmButton: false 
                        });
                    } else {
                        Swal.fire({ icon: 'warning', title: 'Oops...', text: res.message });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'System Error', text: 'Server communication failed.' });
                }
            });
        });
    });

    // --- Helper Functions ---

    function openModal() {
        $('#medForm')[0].reset();
        $('#medId').val('');
        $('#Quantity').val(0); 
        $('#modalLabel').text('Add New Medication');
        $('#medModal').modal('show');
    }

    function editMed(row) {
        $('#medId').val(row.Id);
        $('#Description').val(row.Description);
        $('#Dosage').val(row.Dosage);
        $('#Type').val(row.Type);
        $('#Quantity').val(0); 
        $('#modalLabel').text('Edit & Stock In');
        $('#medModal').modal('show');
    }

    function deleteMed(id) {
        Swal.fire({
            title: 'Delete this record?',
            text: "This will remove the medication registry.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('medlist/delete/') ?>/" + id,
                    type: 'DELETE',
                    success: function(res) {
                        table.ajax.reload(null, false);
                        invTable.ajax.reload(null, false);
                        Swal.fire('Deleted!', res.message, 'success');
                    }
                });
            }
        });
    }

   function viewHistory(medId) {
    // Show loading state or clear previous data
    $('#historyBody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
    $('#historyModal').modal('show');

    $.ajax({
        url: "<?= base_url('medlist/getHistory') ?>/" + medId,
        type: "GET",
        dataType: "JSON",
        success: function(data) {
            let html = '';
            if (data.length > 0) {
                data.forEach(function(row) {
                    let badgeColor = (row.Action === 'ISSUED') ? 'danger' : 'success';
                    let qtyPrefix = (row.Qty_Change > 0) ? '+' : '';
                    
                    html += `
                        <tr>
                            <td class="small">${new Date(row.Created_At).toLocaleString()}</td>
                            <td><span class="badge bg-soft-${badgeColor} text-${badgeColor}">${row.Action}</span></td>
                            <td class="fw-bold text-${badgeColor}">${qtyPrefix}${row.Qty_Change}</td>
                            <td class="small text-muted">${row.Details}</td>
                            <td><i class="fas fa-user-circle me-1"></i> ${row.firstname}</td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center p-4">No movement history found for this item.</td></tr>';
            }
            $('#historyBody').html(html);
        },
        error: function() {
            $('#historyBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load history.</td></tr>');
        }
    });
}
</script>