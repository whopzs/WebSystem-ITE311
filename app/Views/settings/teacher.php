<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-maroon">
                    <i class="bi bi-gear me-2"></i>Settings
                </h1>
            </div>
        </div>
    </div>

    <style>
        .text-maroon {
            color: maroon !important;
        }
        .card {
            border: none;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: maroon;
            box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.25);
        }
        .form-check-input:checked {
            background-color: maroon;
            border-color: maroon;
        }
        .btn-outline-maroon {
            color: maroon;
            border-color: maroon;
        }
        .btn-outline-maroon:hover {
            background-color: maroon;
            color: white;
        }
        .card-header {
            border-bottom: 2px solid rgba(255,255,255,0.1);
        }
    </style>

    <div class="row">
        <!-- Profile Settings -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3" style="background-color: maroon; color: white;">
                    <h6 class="m-0 font-weight-bold text-white">Profile Settings</h6>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <div class="mb-4">
                            <label for="profileName" class="form-label fw-bold">
                                <i class="bi bi-person me-2 text-maroon"></i>Full Name
                            </label>
                            <input type="text" class="form-control form-control-lg" id="profileName" name="name" 
                                   value="<?= esc($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="profileEmail" class="form-label fw-bold">
                                <i class="bi bi-envelope me-2 text-maroon"></i>Email Address
                            </label>
                            <input type="email" class="form-control form-control-lg" id="profileEmail" name="email" 
                                   value="<?= esc($user['email'] ?? '') ?>" required>
                            <small class="form-text text-muted">This email will be used for account recovery and notifications</small>
                        </div>
                        <hr class="my-4">
                        <h6 class="text-maroon mb-3">
                            <i class="bi bi-key me-2"></i>Change Password
                        </h6>
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" 
                                   placeholder="Enter current password">
                            <small class="form-text text-muted">Required only if changing password</small>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" 
                                   placeholder="Enter new password">
                            <small class="form-text text-muted">Minimum 6 characters. Use a strong, unique password.</small>
                            <small id="passwordStrength" class="form-text"></small>
                        </div>
                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" 
                                   placeholder="Re-enter new password">
                            <small id="passwordMatch" class="form-text"></small>
                        </div>
                        <button type="submit" class="btn btn-lg w-100" style="background-color: maroon; color: white; border: 1px solid maroon;">
                            <i class="bi bi-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3" style="background-color: maroon; color: white;">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="bi bi-bell me-2"></i>Notification Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                            <label class="form-check-label fw-bold" for="emailNotifications">
                                Email Notifications
                            </label>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Receive email notifications for important system events
                        </small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" id="enrollmentNotifications" checked>
                            <label class="form-check-label fw-bold" for="enrollmentNotifications">
                                Enrollment Requests
                            </label>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Get notified when students request to enroll in your courses
                        </small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" id="courseNotifications" checked>
                            <label class="form-check-label fw-bold" for="courseNotifications">
                                Course Assignments
                            </label>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Receive notifications when you are assigned to new courses
                        </small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" id="scheduleNotifications" checked>
                            <label class="form-check-label fw-bold" for="scheduleNotifications">
                                Schedule Updates
                            </label>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Get notified when course schedules are updated
                        </small>
                    </div>
                    <button type="button" class="btn btn-lg w-100" style="background-color: maroon; color: white; border: 1px solid maroon;" onclick="saveNotificationSettings()">
                        <i class="bi bi-save me-2"></i>Save Preferences
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Profile form submission
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Updating...');
        
        $.ajax({
            url: '<?= base_url('settings/updateProfile') ?>',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('danger', response.message || 'Failed to update profile');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var errorMsg = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showAlert('danger', errorMsg);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    function showAlert(type, message) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x" style="z-index: 1050; margin-top: 20px;" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';
        $('body').append(alertHtml);
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }

    function saveNotificationSettings() {
        showAlert('success', 'Notification preferences saved successfully');
    }

    // Password strength indicator
    $('#newPassword').on('input', function() {
        var password = $(this).val();
        if (password.length === 0) {
            $('#passwordStrength').html('');
            return;
        }
        
        var strength = 0;
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        
        var strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        var strengthColor = ['danger', 'warning', 'info', 'primary', 'success'];
        
        var indicator = '<span class="text-' + strengthColor[strength - 1] + '"><i class="bi bi-shield-check me-1"></i>Password strength: ' + strengthText[strength - 1] + '</span>';
        $('#passwordStrength').html(indicator);
    });

    // Confirm password match
    $('#confirmPassword').on('input', function() {
        var newPassword = $('#newPassword').val();
        var confirmPassword = $(this).val();
        if (confirmPassword.length === 0) {
            $('#passwordMatch').html('');
            $(this).removeClass('is-invalid is-valid');
            return;
        }
        
        if (newPassword === confirmPassword) {
            $(this).removeClass('is-invalid').addClass('is-valid');
            $('#passwordMatch').html('<span class="text-success"><i class="bi bi-check-circle me-1"></i>Passwords match</span>');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
            $('#passwordMatch').html('<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Passwords do not match</span>');
        }
    });
});
</script>

<?= $this->endSection() ?>

