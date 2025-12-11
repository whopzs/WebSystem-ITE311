<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-maroon">
                    <i class="bi bi-people me-2"></i>User Management
                </h1>
            </div>
        </div>
    </div>

    <!-- Users List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">Users</h6>
                    <button class="btn btn-sm maroon-btn text-white" onclick="addUser()">Add User <i class="bi bi-plus-circle ms-1"></i></button>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" action="" class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= esc($searchName ?? '') ?>" placeholder="Search by name">
                                </div>
                                <div class="col-md-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= esc($searchEmail ?? '') ?>" placeholder="Search by email">
                                </div>
                                <div class="col-md-2">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select" id="role" name="role">
                                        <option value="all" <?= (!isset($searchRole) || $searchRole == 'all') ? 'selected' : '' ?>>All Roles</option>
                                        <option value="student" <?= (isset($searchRole) && $searchRole == 'student') ? 'selected' : '' ?>>Student</option>
                                        <option value="teacher" <?= (isset($searchRole) && $searchRole == 'teacher') ? 'selected' : '' ?>>Teacher</option>
                                        <option value="admin" <?= (isset($searchRole) && $searchRole == 'admin') ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn maroon-btn">Search</button>
                                </div>
                                <div class="col-md-2">
                                    <a href="<?= current_url() ?>" class="btn maroon-btn">Clear</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if (empty($users)): ?>
                        <div class="text-center text-muted">
                            <i class="bi bi-people fa-2x mb-3 text-muted"></i>
                            <p>No users found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= esc($user['name']) ?></td>
                                            <td><?= esc($user['email']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'teacher' ? 'warning' : 'info') ?>">
                                                    <?= esc($user['role']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editUser(<?= $user['id'] ?>, '<?= esc($user['name']) ?>', '<?= esc($user['email']) ?>', '<?= esc($user['role']) ?>')">Edit</button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>)">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-maroon {
    color: maroon !important;
}
.card-header, .maroon-header {
    background-color: maroon;
    color: white;
}
.modal-header {
    background-color: #800000;
    color: white;
    border-bottom: 1px solid #dee2e6;
}
.modal-header .btn-close {
    filter: invert(1);
}
.modal-body {
    background-color: #fff;
}
.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}
#saveUserBtn {
    background-color: #800000;
    border-color: #800000;
    color: white;
}
#saveUserBtn:hover {
    background-color: #a52a2a;
    border-color: #a52a2a;
}
#createUserBtn {
    background-color: #800000;
    border-color: #800000;
    color: white;
}
#createUserBtn:hover {
    background-color: #a52a2a;
    border-color: #a52a2a;
    color: white;
}
.btn-outline-primary,
.btn-outline-danger {
    border-color: maroon;
    color: maroon;
}
.btn-outline-primary:hover,
.btn-outline-danger:hover {
    background-color: maroon;
    border-color: maroon;
    color: white;
}
.badge.bg-danger,
.badge.bg-warning,
.badge.bg-info {
    background-color: maroon !important;
    color: white;
}
.maroon-btn {
    background-color: #800000;
    border-color: #800000;
    color: white;
}
.maroon-btn:hover {
    background-color: #a52a2a;
    border-color: #a52a2a;
    color: white;
}
</style>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="userId" name="user_id">
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="userName" name="name" required>
                        <div class="text-danger" id="editNameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                        <div class="text-danger" id="editEmailError"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="successMsg" class="alert alert-success" style="display: none;"><i class="bi bi-check-circle me-2"></i>User created successfully</div>
                <form id="addUserForm">
                    <div class="mb-3">
                        <label for="addUserRole" class="form-label">Role</label>
                        <select class="form-control" id="addUserRole" name="role" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="text-danger" id="roleError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="addUserName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="addUserName" name="name" required>
                        <div class="text-danger" id="nameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="addUserEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="addUserEmail" name="email" required>
                        <div class="text-danger" id="emailError"></div>
                    </div>
                    <div class="alert mt-3" style="background-color: #f8f0f0; border-color: maroon; color: maroon;">
                        <i class="bi bi-info-circle me-2"></i><strong>Note:</strong> Default password is LMS2025
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createUserBtn">Create User</button>
                <button type="button" id="deleteUserBtn" style="display: none;"></button>
            </div>
        </div>
    </div>
</div>

<script>
function editUser(id, name, email, role) {
    $('#userId').val(id);
    $('#userName').val(name);
    $('#userEmail').val(email);
    $('#editNameError').text('');
    $('#editEmailError').text('');
    $('#editUserModal').modal('show');
}

function addUser() {
    $('#addUserName').val('');
    $('#addUserEmail').val('');
    $('#addUserRole').val('student');
    $('#nameError').text('');
    $('#emailError').text('');
    $('#roleError').text('');
    $('#successMsg').hide();
    $('#addUserModal').modal('show');
}

function isValidName(name) {
    return /^[a-zA-Z\s]+$/.test(name);
}

$(document).ready(function() {
    $('#saveUserBtn').click(function() {
        $('#editNameError').text('');
        $('#editEmailError').text('');
        
        var name = $('#userName').val().trim();
        if (!isValidName(name)) {
            $('#editNameError').text('Name can only contain letters and spaces.');
            return;
        }
        
        var csrfToken = '<?= csrf_hash() ?>';
        var formData = {
            csrf_token_name: csrfToken,
            name: name,
            email: $('#userEmail').val()
        };

        var userId = $('#userId').val();

        $.ajax({
            url: '<?= base_url('users/update') ?>/' + userId,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#editUserModal').modal('hide');
                    location.reload(); // Reload to show updated data
                } else if (response.message === 'Validation errors.') {
                    $('#editNameError').text(response.errors.name || '');
                    $('#editEmailError').text(response.errors.email || '');
                } else {
                    alert('Failed to update user: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error updating user: ' + error);
            }
        });
    });

    $('#createUserBtn').click(function() {
        $('#nameError').text('');
        $('#emailError').text('');
        $('#roleError').text('');
        
        var name = $('#addUserName').val().trim();
        if (!isValidName(name)) {
            $('#nameError').text('Name can only contain letters and spaces.');
            return;
        }
        
        var csrfToken = '<?= csrf_hash() ?>';
        var formData = {
            csrf_token_name: csrfToken,
            name: name,
            email: $('#addUserEmail').val(),
            password: 'LMS2025',
            role: $('#addUserRole').val()
        };

        $.ajax({
            url: '<?= base_url('users/create') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#successMsg').show();
                    setTimeout(function() {
                        $('#addUserModal').modal('hide');
                        location.reload();
                    }, 2000);
                } else if (response.message === 'Validation errors.') {
                    $('#nameError').text(response.errors.name || '');
                    $('#emailError').text(response.errors.email || '');
                    $('#roleError').text(response.errors.role || '');
                } else {
                    alert('Failed to create user: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error creating user: ' + error);
            }
        });
    });

    $('#deleteUserBtn').click(function() {
        var csrfToken = '<?= csrf_hash() ?>';
        var formData = {
            csrf_token_name: csrfToken,
        };

        var userId = $(this).data('userId');

        $.ajax({
            url: '<?= base_url('users/delete') ?>/' + userId,
            type: 'POST',
            data: formData,
            
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to delete user: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error deleting user: ' + error);
            }
        });
    });
});

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        $('#deleteUserBtn').data('userId', id).trigger('click');
    }
}
</script>

<?= $this->endSection() ?>
