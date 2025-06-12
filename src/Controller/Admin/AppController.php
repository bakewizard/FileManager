<?php
declare(strict_types=1);

namespace FileManager\Controller\Admin;

use App\Controller\Admin\AppController as BaseController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Override;

/**
 * @property \Search\Controller\Component\SearchComponent $Search
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
class AppController extends BaseController
{
    protected Filesystem $storage;
    protected ?string $basePath = null;
    protected ?string $fullPath = null;

    /**
     * @inheritDoc
     */
    #[Override]
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $path = Configure::read('FileManager.basePath', '/');
        $this->basePath = '/' . ($path !== '/' ? $path . '/' : '');
        $this->fullPath = WWW_ROOT . str_replace('/', DS, trim($this->basePath, '/')) . DS;

        try {
            $this->storage = $this->getStorage($this->fullPath);
        } catch (FilesystemException $e) {
            $this->Flash->error($e->getMessage());
            $event->setResult($this->redirect(['controller' => 'Dashboard']));
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function beforeRedirect(EventInterface $event, $url, Response $response)
    {
        $previousRequest = new ServerRequest(['url' => $this->referer('/')]);
        if ($previousRequest->getQuery('window') || $previousRequest->getQuery('editor')) {
            if (!is_array($url)) {
                $url = ['controller' => $this->request->getParam('controller'), 'action' => $this->request->getParam('action')];
            }
            $url['?'] = $previousRequest->getQueryParams();
            $event->setResult($response->withLocation(Router::url($url, true)));
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        if ($this->request->is('get') && ($this->request->getQuery('window') || $this->request->getQuery('editor'))) {
            $this->viewBuilder()->setLayout('default')->setTheme(null);
        }
    }
}
