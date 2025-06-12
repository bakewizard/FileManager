<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?php $this->assign('page', __('File Manager')); ?>
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fa-solid fa-tachometer-alt"></i> <?= __('Dashboard') ?></div>
    </div>
    <div class="card-body">
        <div class="list-group">
            <?= $this->Html->link('<i class="fa-solid fa-cog"></i> Settings', ['plugin' => 'FileManager', 'controller' => 'Dashboard', 'action' => 'settings'], ['escape' => false, 'class' => 'list-group-item']) ?>
        </div>
    </div>
</div>