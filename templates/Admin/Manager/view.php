<?php
/**
 * @var \App\View\AppView $this
 * @var array $file
 * @var mixed $path
 */
?>
<?= $this->Html->script('FileManager.main', ['block' => true]) ?>
<?php $this->assign('page', __('File Manager')); ?>
<div class="card">
    <div class="card-header">
        <?= $this->Html->link(
            '<i class="fa-solid fa-arrow-left"></i> ' . __('Back'),
            [
                'action' => 'index',
                dirname($path),
                '?' => $this->request->getQueryParams()
            ],
            [
                'class' => 'btn btn-outline-danger',
                'escape' => false
            ]
        ) ?>
        <div class="card-tools">
            <?=
            $this->Html->link('<i class="fa-solid fa-download"></i>', ['action' => 'download', $path], [
                'class' => 'btn btn-outline-success',
                'title' => __('Download file'),
                'escape' => false
            ])
            ?>
        </div>
    </div>

    <div class="card-header">
        <?= $this->element('FileManager.breadcrumbs') ?>
    </div>

    <?php if ($this->request->getQuery('window')): ?>
        <div class="card-header">
            <button class="btn btn-success" onclick="returnFileUrl('<?= $file['url'] ?>', '<?= $file['path'] ?>', false)">
                <?= __('Select file') ?>
            </button>
        </div>
    <?php elseif ($this->request->getQuery('editor')): ?>
        <div class="card-header">
            <button class="btn btn-success" onclick="returnFileUrl('<?= $this->Url->build($file['url'], ['fullBase' => true]) ?>', '<?= $file['path'] ?>', true)">
                <?= __('Select file') ?>
            </button>
        </div>
    <?php endif; ?>

    <div class="card-body">
        <?php if (str_starts_with($file['mime'], 'image/')): ?>
            <img src="<?= $this->Url->build($file['url'], ['fullBase' => true]) ?>" class="img-fluid">
        <?php elseif (isset($file['contents'])): ?>
            <?= $file['contents'] ?>
        <?php endif; ?>
    </div>
</div>
