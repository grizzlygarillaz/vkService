<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Support\Facades\Schema;

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

    public function replaceTags($project, $message)
    {
        $project = Project::where('id', $project)->first();
        foreach (Tag::all() as $tag) {
            if (strstr($message, $tag->tag) && in_array($tag->property, Schema::getColumnListing('projects')) && $project->{$tag->property} != null) {
                $message = str_replace($tag->tag, $project->{$tag->property}, $message);
            }
        }
        return $message;
    }


    protected function makeInput($description, $field, $data = null, $type = 'text')
    {
        $value = ($data != null) ? "value='$data'" : '';
        return <<<EOT
<div class="input-group mb-3">
  <span class="input-group-text">$description:</span>
  <input type="$type" id="$field" name="$field"  class="form-control bg-white" readonly placeholder="Нет данных..." aria-label="$description" $value aria-describedby="$field">
</div>
EOT;
    }
}
