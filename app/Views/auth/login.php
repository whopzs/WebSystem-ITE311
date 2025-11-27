<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <h1 class="text-center mb-4">Login</h1>

        <?php if (session()->getFlashdata('register_success')): ?>
            <div class="alert alert-success" role="alert">
                <?= esc(session()->getFlashdata('register_success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('login_error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc(session()->getFlashdata('login_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= base_url('login') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= esc(old('email')) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <!-- Forgot password link -->
                        <div class="mt-2 text-end">
                            <a href="<?= base_url('forgot-password') ?>" class="small" style="color: #800000;">Forgot Password?</a>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="background-color:#800000; border-color:#800000;">Login</button>
                </form>
            </div>
        </div>

         <p class="text-center mt-3 text-muted small">Don't have an account? <a href="<?= base_url('register') ?>" style="color: #800000; font-weight: 500;">Register</a></p>
    </div>
<?php ?>
<?= $this->endSection() ?>
