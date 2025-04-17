<?= $this->Html->script('FileManager.main', ['block' => true]) ?>
<?php $this->assign('page', __('File Manager')); ?>
<div class="card">
    <div class="card-header">
        <?= $this->element('FileManager.breadcrumbs') ?>
    </div>

    <?= $this->Form->create(null, ['url' => ['action' => 'setPermissions', $path]]) ?>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th></th>
                    <th><?= __('Read') ?></th>
                    <th><?= __('Write') ?></th>
                    <th><?= __('Execute') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row"><?= __('Owner') ?></th>
                    <td>
                        <?= $this->Form->checkbox('permissions[owner][read]', ['checked' => $currentPerms['owner']['read']]) ?>
                    </td>
                    <td>
                        <?= $this->Form->checkbox('permissions[owner][write]', ['checked' => $currentPerms['owner']['write']]) ?>
                    </td>
                    <td>
                        <?= $this->Form->checkbox('permissions[owner][execute]', ['checked' => $currentPerms['owner']['execute']]) ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Group') ?></th>
                    <td>
                        <?= $this->Form->checkbox('permissions[group][read]', ['checked' => $currentPerms['group']['read']]) ?>
                    </td>
                    <td>
                        <?= $this->Form->checkbox('permissions[group][write]', ['checked' => $currentPerms['group']['write']]) ?>
                    </td>
                    <td>
                        <?= $this->Form->checkbox('permissions[group][execute]', ['checked' => $currentPerms['group']['execute']]) ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Others') ?></th>
                    <td>
                        <?= $this->Form->checkbox('permissions[others][read]', ['checked' => $currentPerms['others']['read']]) ?>
                    </td>
                    <td>
                        <?= $this->Form->checkbox('permissions[others][write]', ['checked' => $currentPerms['others']['write']]) ?>
                    </td>
                    <td>
                        <?= $this->Form->checkbox('permissions[others][execute]', ['checked' => $currentPerms['others']['execute']]) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        <?= $this->Form->button('<i class="fa-solid fa-save"></i> ' . __('Save'), ['class' => 'btn-outline-success float-end', 'escapeTitle' => false]) ?>
        <?= $this->Html->link('<i class="fa-solid fa-times-circle"></i> ' . __('Cancel'), $this->request->referer(), ['class' => 'btn btn-outline-danger', 'escape' => false]) ?>
    </div>
    <?= $this->Form->end() ?>
</div>
