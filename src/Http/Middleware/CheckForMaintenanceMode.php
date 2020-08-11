<?php

namespace System\Http\Middleware;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;

class CheckForMaintenanceMode extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->except = array_replace_recursive($this->except, [
            config('systems.routes.backend.prefix', 'admin') . '/*',
        ]);
    }
}
