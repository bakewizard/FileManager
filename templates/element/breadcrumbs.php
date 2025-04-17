<?php
$stack = '';
$crumbs = explode('/', $this->request->getParam('pass')[0]);
$crumbsCount = count($crumbs);
$lastIdx = $crumbsCount - 1;
?>

<ol class="breadcrumb mb-0">
    <?php if ($crumbsCount >= 1): ?>
        <li class="breadcrumb-item"><?= $this->Html->link('<i class="fa-solid fa-home"></i>', ['action' => 'index', '?' => $this->request->getQueryParams()], ['escape' => false]) ?></li>
    <?php endif; ?>

    <?php foreach ($crumbs as $i => $crumb): ?>
        <?php $stack .= '/' . $crumb; ?>
        <?php if ($i !== $lastIdx): ?>
            <li class="breadcrumb-item">
                <?= $this->Html->link($crumb, ['action' => 'index', $stack, '?' => $this->request->getQueryParams()]) ?>
            </li>
        <?php else: ?>
            <li class="breadcrumb-item"><?= $crumb ?></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ol>