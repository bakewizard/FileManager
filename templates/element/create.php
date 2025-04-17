<?= $this->Form->create(null, ['url' => ['action' => 'create', $path], 'type' => 'file']); ?>
<?=

$this->Form->control('name', [
    'prepend' => '<i class="fa-solid fa-folder"></i>',
    'label' => false,
    'spacing' => 'mb-md-0 mb-2',
    'placeholder' => 'Create folder',
    'append' => $this->Form->button('<i class="fa-solid fa-folder-plus fa-fw"></i>', ['class' => 'btn-success', 'escapeTitle' => false])
]);
?>
<?= $this->Form->end(); ?>