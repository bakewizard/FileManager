<?php

declare(strict_types=1);

namespace FileManager;

use App\Core\CmsPlugin;
use App\Core\Configure\Engine\DbConfig;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/**
 * Plugin for FileManager
 */
class FileManagerPlugin extends CmsPlugin
{

    protected ?string $name = 'FileManager';
    protected bool $consoleEnabled = false;
    protected bool $middlewareEnabled = false;
    protected bool $servicesEnabled = false;

    #[\Override]
    public function bootstrap(PluginApplicationInterface $app): void
    {
        Cache::setConfig('filemanager', [
            'className' => 'File',
            'prefix' => 'fm_',
            'path' => CACHE . 'file-manager' . DS,
            'duration' => '+6 months'
        ]);

        Configure::config('db', new DbConfig(null, 'filemanager'));
        Configure::load('FileManager', 'db');
    }

    #[\Override]
    public function routes(RouteBuilder $routes): void
    {
        $routes->prefix('Admin', function (RouteBuilder $builder) {
            $builder->plugin($this->name, function (RouteBuilder $builder) {
                $builder->applyMiddleware('auth');
                $builder->connect('/', ['controller' => 'Manager']);
                $builder->connect('/{controller}/{action}/**', []);
                $builder->fallbacks(DashedRoute::class);
            });
        });
    }
}
