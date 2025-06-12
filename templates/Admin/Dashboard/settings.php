<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $settings
 */
?>
<?php $this->assign('page', __('FileManager Settings')); ?>
<div class="card card-success card-outline">
    <div class="card-header">
        <div class="card-title"><?= __('Main') ?></div>
    </div>
    <?= $this->Form->create($settings, ['align' => 'horizontal']) ?>
    <div class="card-body">
        <?= $this->Form->control('basePath'); ?>
    </div>
    <div class="card-footer">
        <?= $this->Form->button('<i class="fa-solid fa-save"></i> ' . __('Save'), ['class' => 'btn-outline-success float-end', 'escapeTitle' => false]) ?>
        <?= $this->Html->link('<i class="fa-solid fa-times-circle"></i> ' . __('Cancel'), ['controller' => 'Dashboard', 'action' => 'index'], ['class' => 'btn btn-outline-danger', 'escape' => false]) ?>
    </div>
    <?= $this->Form->end() ?>
</div>
