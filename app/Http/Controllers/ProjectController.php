<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Promo;
use App\Models\Vk;
use App\Models\Post;
use \Statickidz\GoogleTranslate;
use Illuminate\Support\Facades\Schema;

class ProjectController extends Controller
{
    public function index()
    {
        return view('projects', ['projects' => Project::all()->sortBy('name'), 'page' => 'ПРОЕКТЫ']);
    }

    public function info(Request $request) {
        $project = $request->id;
        $about = $this->projectFields($project);
        $promo = '<p class="text-dark">Нет доступных акций</p>';
        $dish = '<p class="text-dark">Нет блюд</p>';
        $post = '<p class="text-dark">Нет постов</p>';
        $promos = \DB::table('promo_project')->where('project_id', $request->id)->get();
        if (!empty($promos->first())) {
            $promo = '';
            foreach ($promos as $prom) {
                $promo .= $this->makePromo(Promo::where('id', $prom->promo_id)->get()->first());
            }
        }
        $posts = Post::all();
        if ($posts->count() > 0) {
            $post = '';
        }
        foreach ($posts as $pos) {
            $post .= $this->makePost($pos, $project);
        }
        return response()->json(['about' => $about, 'promo' => $promo, 'dish' => $dish, 'posts' => $post]);
    }

    protected  function makePost($post, $project) {
        $date = date('d.m.Y H:i', (strtotime($post->publish_date)));
        return <<<EOT
    <div class="card m-1 post-card" id="$post->id">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-dark">$date</h5>
            <div class="buttons">
                <button class="btn btn-outline-secondary" id="post-settings">Редактировать</button>
            </div>
        </div>
        <div class="card-body d-flex p-0 justify-content-between">
            <p class="overflow-hidden cp-description m-3 text-dark" style="height: 150px">
                {$post->text}
            </p>
            <div class="">
                <div id='img-container'>
                    <img src="/storage/Promo test 2/wCZjAWwotC1r0g8jwbGjmQxXj.jpg" class="border-0 post-img my-2 post-image" value="random" alt="">
                </div>
                <div class="input-group mb-2">
                    <input type="file" class="form-control me-4" id="promo_images"  name="promo_images[]" multiple>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            {$this->createSelect($post->post_type, $project)}
            <button class="btn btn-outline-success" value="$post->id" id="publish-post">Отправить в публикацию</button>
        </div>
    </div>
EOT;
    }

    public function createSelect($type, $project) {
        $select = '';
        switch ($type) {
            case 1:
                $select = <<<EOT
            <div class="input-group w-50 me-3" id="cp-type">
                <label class="input-group-text" for="type-choose">Тип</label>
                <select class="form-select" id="type-choose">
                    <option selected>Choose...</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div>
EOT;
                break;
            case 2:
                $option = '';
                foreach (\DB::table('promo_project')->where('project_id', $project)->get() as $promo) {
                    $prom = Promo::find($promo->promo_id);
                    $option .= "<option value='{$prom->id}'>{$prom->name}</option>";
                }
                $select = <<<EOT
            <div class="input-group w-50 me-3" id="cp-type">
                <label class="input-group-text" for="type-choose">Акция</label>
                <select class="form-select" id="type-choose">
                    <option selected>Выберите...</option>
                    $option
                </select>
            </div>
EOT;
                break;
            case 3:
                break;
        }
        return $select;
    }

    protected function makePromo($promo)
    {
        $type = $promo->locked ? 'общая' : 'персональная';
        return <<<EOT
<div id="{$promo->id}" class="input-group mb-3 promo-info">
<span class="flex-grow-1 btn btn-outline-primary text-start promo-toggle">{$promo->name}</span>
<button class="btn btn-outline-secondary" disabled type="button">$type</button>
</div>
EOT;
    }

    public function addPromo (Request $request) {
        foreach ($request->promos as $promo) {
            \DB::table('promo_project')->insert(['project_id' => $request->project, 'promo_id' => $promo]);
        }
    }

    private function projectFields($project) {
        $translate = new GoogleTranslate();
        $about = '';
        $project = Project::where('id', $project)->get()->first();
        foreach (Schema::getColumnListing('projects') as $property) {
            if (!in_array($property, Project::all()->first()->hidden)) {
                $field = implode(' ', explode('_', $property));
                $about .= $this->makeInput($translate->translate('en', 'ru', $field), $property,
                    $property == 'time_zone' && is_numeric($project->{$property}) ?
                        ($project->{$property} + 3 > 0 ? '+' . ($project->{$property} + 3) : '-' . ($project->{$property} + 3))
                        :
                        $project->{$property});
            }
        }
        return $about;
    }

    public function removePromo (Request $request) {
        \DB::table('promo_project')
            ->where('promo_id', $request->promo)
            ->where('project_id', $request->project)
            ->delete();
    }

    private function getGroupId ($link) {
        $link = explode('vk.com/', $link)[1];
        if (preg_match('/public\d{1,}\b(?<=\w)/',$link)) {
            return str_replace('public', '', $link);
        } else {
            $response = [
                'group_id' => $link
            ];
            $api = new Vk;
            $api->methodType = 'groups';
            return $api->apiResult('getById', $response)[0]['id'];
        }
    }

    public function edit($project, Request $request) {
        $group = $this->getGroupId($request->link_to_group);
        $property = [];
        Project::where('id', $project)->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'link_to_group' => $request->link_to_group,
            'group_id' => $group,
            'city' => $request->city,
            'time_zone' => $request->time_zone - 3,
            'album_id' => $request->album_id
        ]);
    }
}
