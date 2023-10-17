<?php if (isset($_REQUEST['error'])) : ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <p>
            <?= $_REQUEST['error'] ?>
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>