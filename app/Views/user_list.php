<?= view('components/header') ?>
<?= view('components/sidebar') ?>

<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h4 class="fw-bold mb-0">System Users</h4>
                    <button class="btn btn-primary rounded-pill px-4" onclick="openUserModal()">
                        <i class="fas fa-user-plus me-2"></i>Add User
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="userTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form id="userForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">User Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="userId">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small fw-bold">First Name</label>
                            <input type="text" name="firstname" id="firstname" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Middle Name</label>
                            <input type="text" name="middlename" id="middlename" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Last Name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Email Address</label>
                            <input type="email" name="emailaddress" id="emailaddress" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Contact Number</label>
                            <input type="text" name="contactnumber" id="contactnumber" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="small fw-bold">Role Access</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="">Select Role...</option>
                                <?php foreach($roles as $r): ?>
                                    <option value="<?= $r->Id ?>"><?= $r->Role ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('components/footer') ?>

<script>
let userTable;

$(document).ready(function() {
    userTable = $('#userTable').DataTable({
        "ajax": { "url": "<?= base_url('users/getUsers') ?>", "type": "POST", "dataSrc": "" },
        "columns": [
            { 
                "data": null, 
                "render": function(data) { return `${data.firstname} ${data.lastname}`; }
            },
            { "data": "username" },
            { "data": "Role" },
            { 
                "data": "isactive",
                "render": function(data) {
                    return data == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                }
            },
            {
                "data": null,
                "className": "text-center",
                "render": function(data) {
                    return `<button class="btn btn-sm btn-outline-primary" onclick='editUser(${JSON.stringify(data)})'><i class="fas fa-edit"></i></button>`;
                }
            }
        ]
    });

    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= base_url('users/store') ?>",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire('Success', res.message, 'success');
                    $('#userModal').modal('hide');
                    userTable.ajax.reload();
                }
            }
        });
    });
});

function openUserModal() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#password').prop('required', true); // Required for new users
    $('#userModal').modal('show');
}

function editUser(data) {
    $('#userId').val(data.id);
    $('#firstname').val(data.firstname);
    $('#middlename').val(data.middlename);
    $('#lastname').val(data.lastname);
    $('#username').val(data.username);
    $('#emailaddress').val(data.emailaddress);
    $('#contactnumber').val(data.contactnumber);
    $('#role').val(data.role);
    $('#password').prop('required', false); // Optional for edits
    $('#userModal').modal('show');
}
</script>