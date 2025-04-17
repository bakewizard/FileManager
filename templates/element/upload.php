<?= $this->Form->create(null, ['url' => ['action' => 'upload', $path], 'type' => 'file']); ?>
<?=

$this->Form->control('files[]', [
    'type' => 'file',
    'multiple' => true,
    'spacing' => 'mb-md-0 mb-0',
    'label' => false,
    'append' => $this->Form->button('<i class="fa-solid fa-file-upload fa-fw"></i>', ['class' => 'btn-success', 'escapeTitle' => false])
]);
?>

<?= $this->Form->end(); ?>
