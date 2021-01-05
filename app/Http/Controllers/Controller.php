<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function getRoutes()
    {
        $routes = Route::getRoutes()->get();
        $webRoutes = [];
        foreach ($routes as $route) {
            $middleware = $route->action['middleware'][0];
            if ($middleware == 'web') {
                $webRoutes[] = $route->uri;
            }
        }
        return $webRoutes;
    }

    protected function makeInput($description, $field, $data = null, $type = 'text')
    {
        $value = ($data != null) ? "value='$data'" : '';
        return <<<EOT
<div class="input-group mb-3">
  <span class="input-group-text" id="$field">$description:</span>
  <input type="$type" class="form-control bg-white" readonly placeholder="Нет данных..." aria-label="$description" $value aria-describedby="$field">
</div>
EOT;
    }
}
