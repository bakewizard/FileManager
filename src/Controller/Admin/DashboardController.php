<?php

declare(strict_types=1);

namespace FileManager\Controller\Admin;

use App\Controller\Admin\AppController;

class DashboardController extends AppController
{

    /**
     * Plugin dashboard
     * 
     * Displays the plugin dashboard
     */
    public function index()
    {
        
    }

    /**
     * Plugin settings
     * 
     * Displays/Sets the plugin settings
     *
     * @return \Cake\Http\Response|null
     */
    #[\Override]
    public function settings()
    {
        parent::settings();
    }
}
