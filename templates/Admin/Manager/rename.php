<?php
/**
 * @var \App\View\AppView $this
 * @var string $name
 * @var mixed $path
 */
?>
<?php $this->assign('page', __('File Manager')); ?>
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fa-solid fa-solid fa-edit me-2"></i><?= $name ?></div>
    </div>

    <div class="card-header">
        <?= $this->element('FileManager.breadcrumbs') ?>
    </div>

    <?= $this->Form->create(null, ['url' => ['action' => 'rename', $path]]); ?>
    <div class="card-body">
        <?= $this->Form->control('name', ['default' => $name, 'label' => __('New name')]); ?>
    </div>

    <div class="card-footer">
        <?= $this->Form->button('<i class="fa-solid fa-save"></i> ' . __('Save'), ['class' => 'btn-outline-success float-end', 'escapeTitle' => false]) ?>
        <?= $this->Html->link('<i class="fa-solid fa-times-circle"></i> ' . __('Cancel'), $this->request->referer(), ['class' => 'btn btn-outline-danger', 'escape' => false]) ?>
    </div>
    <?= $this->Form->end(); ?>
</div>