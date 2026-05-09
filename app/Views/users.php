<?= view('components/header') ?>
<?= view('components/sidebar') ?>
<?php
$p = session()->get('permissions');
?>
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Users List</h4>
                    </div>
  <?php if (isset($p['USERS']['add']) && $p['USERS']['add'] == 1): ?>
                     <button class="btn btn-primary btn-sm" id="btnAddArea">
                     <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.5 12.5537C12.2546 12.5537 14.4626 10.3171 14.4626 7.52684C14.4626 4.73663 12.2546 2.5 9.5 2.5C6.74543 2.5 4.53737 4.73663 4.53737 7.52684C4.53737 10.3171 6.74543 12.5537 9.5 12.5537ZM9.5 15.0152C5.45422 15.0152 2 15.6621 2 18.2464C2 20.8298 5.4332 21.5 9.5 21.5C13.5448 21.5 17 20.8531 17 18.2687C17 15.6844 13.5668 15.0152 9.5 15.0152ZM19.8979 9.58786H21.101C21.5962 9.58786 22 9.99731 22 10.4995C22 11.0016 21.5962 11.4111 21.101 11.4111H19.8979V12.5884C19.8979 13.0906 19.4952 13.5 18.999 13.5C18.5038 13.5 18.1 13.0906 18.1 12.5884V11.4111H16.899C16.4027 11.4111 16 11.0016 16 10.4995C16 9.99731 16.4027 9.58786 16.899 9.58786H18.1V8.41162C18.1 7.90945 18.5038 7.5 18.999 7.5C19.4952 7.5 19.8979 7.90945 19.8979 8.41162V9.58786Z" fill="currentColor"></path></svg>Add Users
                    </button>
 <?php else: ?>
                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="Permission Denied">
                        <button class="btn btn-sm btn-icon disabled" style="cursor: not-allowed; opacity: 0.5;">
                          <i class="bi bi-slash-circle text-secondary fs-5"></i>
                        </button>                  
                    <?php endif; ?>
                </div>

                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table id="user-list-table" class="table table-bordered dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Factory Name</th>
                                    <th>Component Name</th>
                                    <th>Area Name</th>
                                    <th>Full Name</th>
                                    <th>Role Name</th>
                                    <th>EmailAddress</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th style="min-width: 100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($users as $r): ?>
                              <tr>
                                 <td class="text-left"><?= $r['Id'] ?></td>
                                 <td><?= $r['FactoryName'] ?></td>
                                 <td><?= $r['ComponentName'] ?></td>
                                 <td><?= $r['AreaName'] ?></td>
                                 <td><?= $r['FullName'] ?></td>
                                 <td><?= $r['RoleName'] ?></td>
                                 <td><?= $r['EmailAddress'] ?></td>
                                 <td>
                                    <span class="badge bg-soft-<?= $r['IsActive'] == 1 ? 'success' : 'danger' ?>">
                                          <?= $r['IsActive'] == 1 ? 'Active' : 'Inactive' ?>
                                    </span>
                                 </td>
                                 <td><?= $r['LastLoginDate'] ?></td>
                                <td>
                                    <?php if (isset($p['USERS']['edit']) && $p['USERS']['edit'] == 1): ?>     
                              <!-- Edit Button -->
                                <a class="btn btn-sm btn-icon  editProcess" data-id="<?= $r['Id'] ?>" title="Edit">
                                    <i class="bi bi-pencil-fill text-info fs-5"></i>
                                </a>


                                        <!-- Status Toggle -->
                                        <button class="btn btn-sm toggleStatus"
                                                data-id="<?= $r['Id'] ?>"
                                                data-status="<?= $r['IsActive'] ?>"
                                                title="<?= $r['IsActive'] ? 'Deactivate' : 'Activate' ?>">
                                            
                                            <?php if ($r['IsActive'] == 1): ?>
                                                <i class="bi bi-toggle-on text-success fs-5"></i>
                                            <?php else: ?>
                                                <i class="bi bi-toggle-off text-danger fs-5"></i>
                                            <?php endif; ?>
                                        </button>
                                        <button class="btn btn-sm toggleLock" 
                                            data-id="<?= $r['Id'] ?>" 
                                            title="<?= $r['IsLock'] ? 'Unlock User' : 'Lock User' ?>">
                                        <?php if ($r['IsLock'] == 1): ?>
                                            <i class="bi bi-lock-fill text-danger fs-5"></i>
                                        <?php else: ?>
                                            <i class="bi bi-unlock text-success fs-5"></i>
                                        <?php endif; ?>
                                    </button>
                                     <?php else: ?>
                                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="Permission Denied">
                                        <button class="btn btn-sm btn-icon disabled" style="cursor: not-allowed; opacity: 0.5;">
                                            <i class="bi bi-slash-circle text-secondary fs-5"></i>
                                        </button>
                                        <button class="btn btn-sm disabled" style="cursor: not-allowed; opacity: 0.5;">
                                            <i class="bi bi-lock-fill text-secondary fs-5"></i>
                                        </button>
                                    </span>
                                <?php endif; ?>
                              </td>


                              </tr>
                              <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= view('modals/users_modal') ?>
<?= view('components/footer') ?>
<?= view('script/users_script') ?>

