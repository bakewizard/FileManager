<!doctype html>
<html>
    <head>
        <title><?= $this->fetch('title') ?></title>
        <?= $this->Html->charset() ?>
        <?= $this->Html->meta('viewport', 'width=device-width, initial-scale=1') ?>
        <?= $this->Html->meta('icon') ?>
        <?= $this->fetch('meta') ?>
        <?= $this->Html->css('/backend/css/app') ?>
        <?= $this->fetch('css') ?>
    </head>
    <body>
        <div class="app-wrapper">
            <main class="app-main">
                <div class="app-content-header">
                    <div class="container-fluid">
                        <h1>
                            <?= $this->fetch('page') ?>
                        </h1>
                    </div>
                </div>
                <div class="app-content">
                    <div class="container-fluid">
                        <?= $this->Flash->render() ?>
                        <?= $this->fetch('content') ?>
                    </div>
                </div>
            </main>
            <?= $this->element('/form/confirm_modal') ?>
        </div>
        <?= $this->Html->script('/backend/js/app') ?>
        <?= $this->fetch('script') ?>
    </body>
</html>
