<?php
declare(strict_types=1);

namespace FileManager;

use App\Core\CmsPlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Override;

/**
 * Plugin for FileManager
 */
class FileManagerPlugin extends CmsPlugin
{
    protected ?string $name = 'FileManager';
    protected bool $consoleEnabled = false;
    protected bool $middlewareEnabled = false;
    protected bool $servicesEnabled = false;

    /**
     * @inheritDoc
     */
    #[Override]
    public function bootstrap(PluginApplicationInterface $app): void
    {
        Configure::load('FileManager', 'db');
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function routes(RouteBuilder $routes): void
    {
        $routes->prefix('Admin', function (RouteBuilder $builder): void {
            $builder->plugin($this->name ?? 'FileManager', function (RouteBuilder $builder): void {
                $builder->applyMiddleware('auth');
                $builder->connect('/', ['controller' => 'Manager']);
                $builder->connect('/{controller}/{action}/**', []);
                $builder->fallbacks(DashedRoute::class);
            });
        });
    }
}
