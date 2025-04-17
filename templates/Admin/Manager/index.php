<?php $this->assign('page', __('File Manager')); ?>
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <?= $this->element('FileManager.create') ?> 
            </div>
            <div class="col-md-6">
                <?= $this->element('FileManager.upload') ?>
            </div>
        </div>
    </div>

    <?php $pass = $this->request->getParam('pass') ?>

    <?php if ($pass && !empty($pass[0])): ?>
        <div class="card-header">
            <?= $this->element('FileManager.breadcrumbs') ?>
        </div>
    <?php endif; ?>

    <div class="card-body pb-0">
        <div class="table-responsive">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th class="border"><?= __('Name') ?></th>
                        <th class="border"><?= __('Type') ?></th>
                        <th class="border"><?= __('Size') ?></th>
                        <th class="border"><?= __('Date') ?></th>
                        <th class="border"><?= __('Permissions') ?></th>
                        <th class="border"><?= __('Group') ?></th>
                        <th class="border"><?= __('Owner') ?></th>
                        <th class="border"> </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $item): ?>
                        <tr>
                            <td>
                                <?=
                                $this->Html->link(($item['type'] === 'file' ? '<i class="fa-regular fa-file fa-lg fa-fw text-primary-emphasis"></i> ' : '<i class="fa-solid fa-folder fa-lg fa-fw text-warning"></i> ') . $item['name'],
                                        [
                                            'plugin' => 'FileManager',
                                            'controller' => 'Manager',
                                            'action' => ($item['type'] === 'file' ? 'view' : 'index'),
                                            $path,
                                            $item['name'],
                                            '?' => $this->request->getQueryParams()
                                        ], ['escape' => false, 'class' => 'text-decoration-none'])
                                ?>
                            </td>
                            <td><?= $item['extension'] ?></td>
                            <td><?= isset($item['size']) ? $this->Number->toReadableSize($item['size']) : '&ltDIR&gt' ?></td>
                            <td><?= $item['modified'] ?></td>
                            <td>
                                <?=
                                !is_null($item['perms']) ?
                                        $this->Html->link($item['perms'], ['action' => 'setPermissions', $path, $item['name'], '?' => $this->request->getQueryParams()], [
                                            'title' => 'Set permissions',
                                            'class' => 'text-decoration-none',
                                            'escape' => false
                                        ]) : 'n/a'
                                ?>
                            </td>
                            <td><?= $item['group'] ?? 'n/a' ?></td>
                            <td><?= $item['owner'] ?? 'n/a' ?></td>
                            <td class="text-center">
                                <?=
                                $this->Html->link('<i class="fa-solid fa-edit me-2"></i>', ['action' => 'rename', $path, $item['name'], '?' => $this->request->getQueryParams()], [
                                    'title' => 'Rename',
                                    'class' => 'text-success',
                                    'escape' => false
                                ])
                                ?>
                                <?=
                                $this->Form->deleteLink('<i class="fa-solid fa-trash"></i>', ['action' => 'remove', $path, $item['name']], [
                                    'escape' => false,
                                    'title' => 'Delete',
                                    'confirm' => __('Are you sure you want to delete {0}?', $item['name']),
                                    'class' => 'text-danger',
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#confirm-modal'
                                ])
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>