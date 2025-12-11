<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">Material for <?= esc($course['title']) ?></h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('upload_success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('upload_success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('upload_error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('upload_error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('delete_success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('delete_success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('delete_error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('delete_error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Display Existing Materials -->
                    <?php if (!empty($existingMaterials)): ?>
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-maroon mb-3">Uploaded Materials</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-maroon">
                                        <tr>
                                            <th>File Name</th>
                                            <th>Upload Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($existingMaterials as $material): ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-file-alt me-2"></i>
                                                    <?= esc($material['file_name']) ?>
                                                </td>
                                                <td>
                                                    <?= date('M d, Y', strtotime($material['created_at'])) ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= base_url('materials/delete/' . $material['id']) ?>"
                                                           class="btn btn-sm btn-outline-danger"
                                                           onclick="return confirm('Are you sure you want to delete this material?')"
                                                           title="Delete Material">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No materials have been uploaded for this course yet.
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Upload New Material Form -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-maroon mb-3">Upload New Material</h6>
                    </div>

                    <form action="<?= base_url('admin/course/' . $course['id'] . '/upload') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="material_file" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="material_file" name="material_file" required>
                            <div class="form-text">
                                Allowed file types: PDF, PPT.
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-maroon me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-outline-maroon">Upload Material</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-maroon {
    color: maroon !important;
}

.card {
    border: 1px solid #800000;
    box-shadow: 0 2px 4px rgba(128, 0, 0, 0.1);
}
.card-header {
    background-color: #800000;
    color: white;
    border-bottom: 1px solid #800000;
}

.btn-outline-maroon {
    color: #800000 !important;
    border-color: #800000;
}
.btn-outline-maroon:hover {
    background-color: #800000;
    color: #fff !important;
}

.table-maroon {
    background-color: #800000;
    color: white;
}

.table-maroon th {
    border-color: #660000;
    background-color: #800000;
    color: white;
}

.table-hover tbody tr:hover {
    background-color: rgba(128, 0, 0, 0.05);
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.fas {
    font-size: 0.875rem;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}
</style>

<?= $this->endSection() ?>
