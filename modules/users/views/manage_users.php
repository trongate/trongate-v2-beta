<h1><?= out('Manage Users') ?></h1>
<?php
if (isset($rows[0])) {
?>
    <p>Click on a table row to view or update user details.</p>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Date Joined</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($rows as $row) {
                $status = ($row->banned === 1) ? '<span class="danger">Banned</span>' : '<span class="success">Active</span>';
            ?>
                <tr>
                    <td><?= out($row->username) ?></td>
                    <td><?= out($row->email) ?></td>
                    <td><?= out(ucfirst($row->role)) ?></td>
                    <td><?= out($row->date_joined_nice) ?></td>
                    <td><?= $status ?></td>
                    <td class="text-center">
                        <a href="<?= BASE_URL ?>users/show/<?= $row->id ?>" class="button sm">View</a>
                        <a href="<?= BASE_URL ?>users/update/<?= $row->id ?>" class="button warning sm">Edit</a>
                        <button class="danger sm" onclick="openModal('delete_<?= $row->id ?>')">Delete</button>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <p class="mt-2">
        <a href="<?= BASE_URL ?>users/create" class="button success">
            <i class="fa fa-plus"></i> Add New User
        </a>
    </p>
<?php
} else {
?>
    <div class="card">
        <div class="card-heading">No Users Found</div>
        <div class="card-body">
            <p>There are currently no users in the system.</p>
            <p class="mt-2">
                <a href="<?= BASE_URL ?>users/create" class="button success">
                    <i class="fa fa-plus"></i> Add New User
                </a>
            </p>
        </div>
    </div>
<?php
}
?>

<!-- Delete Confirmation Modals -->
<?php
if (isset($rows[0])) {
    foreach ($rows as $row) {
?>
        <div class="modal" id="delete_<?= $row->id ?>" style="display: none;">
            <div class="modal-heading">
                <i class="fa fa-warning"></i> Delete Confirmation
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the following user?</p>
                <p class="mt-1"><strong><?= out($row->username) ?></strong> (<?= out($row->email) ?>)</p>
                <p class="mt-1">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="alt" onclick="closeModal()">Cancel</button>
                <button type="button" class="danger" onclick="confirmDelete(<?= $row->id ?>)">
                    <i class="fa fa-trash"></i> Delete User
                </button>
            </div>
        </div>
<?php
    }
}
?>

<script>
function openModal(modalId) {
    var modal = document.getElementById(modalId);
    var overlay = document.getElementById('overlay');
    var modalContainer = document.getElementById('modal-container');

    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'overlay';
        document.body.appendChild(overlay);
    }

    if (!modalContainer) {
        modalContainer = document.createElement('div');
        modalContainer.id = 'modal-container';
        document.body.appendChild(modalContainer);
    }

    modalContainer.appendChild(modal);
    modal.style.display = 'block';

    // Trigger reflow for animation
    setTimeout(function() {
        overlay.style.zIndex = '9998';
        modalContainer.style.zIndex = '9999';
        modal.style.zIndex = '10000';
        modal.style.opacity = '1';
        modal.style.marginTop = 'var(--modal-margin-top)';
    }, 10);
}

function closeModal() {
    var modals = document.querySelectorAll('.modal');
    var overlay = document.getElementById('overlay');
    var modalContainer = document.getElementById('modal-container');

    modals.forEach(function(modal) {
        modal.style.opacity = '0';
        modal.style.marginTop = '-160px';
        setTimeout(function() {
            modal.style.display = 'none';
        }, 300);
    });

    if (overlay) {
        overlay.style.zIndex = '-2';
    }

    if (modalContainer) {
        modalContainer.style.zIndex = '-3';
    }
}

function confirmDelete(userId) {
    window.location.href = '<?= BASE_URL ?>users/delete/' + userId;
}
</script>
