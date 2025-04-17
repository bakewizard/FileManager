<?php

declare(strict_types=1);

namespace FileManager\Controller\Admin;

use App\Controller\Admin\AppController as BaseController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class AppController extends BaseController
{

    protected ?Filesystem $storage = null;
    protected ?string $fullPath = null;

    #[\Override]
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->fullPath = Configure::read('FileManager.basePath', WWW_ROOT);
        try {
            $this->storage = $this->getStorage($this->fullPath);
        } catch (FilesystemException $e) {
            $this->Flash->error($e->getMessage());
            $event->setResult($this->redirect(['controller' => 'Dashboard']));
        }
    }

    #[\Override]
    public function beforeRedirect(EventInterface $event, $url, Response $response)
    {
        $previousRequest = new ServerRequest(['url' => $this->referer('/')]);
        if ($previousRequest->getQuery('window') || $previousRequest->getQuery('editor')) {
            $url['?'] = $previousRequest->getQueryParams();
            $event->setResult($response->withLocation(Router::url($url, true)));
        }
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\EventInterface $event The beforeRender event.
     * @return void
     */
    #[\Override]
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        if ($this->request->is('get') && ($this->request->getQuery('window') || $this->request->getQuery('editor'))) {
            $this->viewBuilder()->setLayout('default')->setTheme(null);
        }
    }
}
