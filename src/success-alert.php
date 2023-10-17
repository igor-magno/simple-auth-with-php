<?php if (isset($_REQUEST['success'])) : ?>
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <p>
            <?= $_REQUEST['success'] ?>
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>