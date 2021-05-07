<?php

namespace App\Http\Controllers;

use App\Models\DishType;
use App\Models\Stories;
use http\Env\Response;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Promo;
use App\Models\Vk;
use App\Models\Dish;
use App\Models\Post;
use App\Models\Photo;
use \App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Object_;
use \Statickidz\GoogleTranslate;
use Symfony\Component\Console\Input\Input;
use Yandex\Disk\DiskClient;
use App\Traits\PostErrors;
use App\Traits\TokenFunctions;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\ImageManagerStatic as Image;

class ProjectController extends Controller
{
    use PostErrors, TokenFunctions;

    public function index($project = null)
    {
        if (Auth::user()->role != 'admin') {
            $projectList = collect();
            foreach (\DB::table('employee_project')->where('employee', Auth::user()->getAuthIdentifier())->get() as $project) {
                $projectCheck = Project::find($project->project);
                if ($projectCheck) {
                    $projectList->push($projectCheck);
                }
            }
        } else {
            $projectList = Project::all();
        }
        $projectList = $projectList->sortBy('name')->toArray();
        if (empty($project)) {
            $project = empty($projectList['items']) ? null : $projectList[0]['id'];
        }
        return view('project.projects', ['projects' => $projectList, 'page' => 'ПРОЕКТЫ', 'objectsPage' => $this->objectsOfProject, 'currentProject' => $project]);
    }

    public function story(Request $request, $project)
    {
        $sort = key_exists('story_sort', json_decode($request->local, true)) ? json_decode($request->local, true)['story_sort'] : 'asc';
        if (is_null(\DB::table('projects')->find($project)->content_plan)) {
            return view('stories.index', [
                'cpError' => 'Не выбран контент план. Перейдите во вкладку "Проект"',
                'stories' => [],
                'publicStories' => "https://vk.com/public$project?act=stories",
                'storySort' => $sort
            ])->render();
        }
        Storage::delete(Storage::allFiles('public/temp'));
        if ($request->ajax()) {
//            $errorStory = $this->checkPostExist($project);
            $stories = null;
            foreach (\DB::table('stories')->where('project_id', $project)
                         ->where('content_plan', \DB::table('projects')->find($project)->content_plan)
                         ->where('publish_date', '>', date('Y-m-d H:i', strtotime('-2 days', time())))
                         ->orWhere((function ($query) use ($project) {
                             $query->where('content_plan', null)
                                 ->where('publish_date', '>', date('Y-m-d H:i', strtotime('-2 days', time())))
                                 ->where('project_id', $project);
                         }))
                         ->orderBy('publish_date', $sort)->get() as $story) {
                $errors = [];
                $photo = Photo::find($story->content);
                if ($photo) {
                    $photo = $photo->path;
                } else {
                    $photo = null;
                }
                $errors = $this->checkStory($story->id, $photo);
                if (file_exists($photo) && preg_match('/^video/', mime_content_type($photo))) {
                    $preview = Photo::find($story->content)->preview;
                    if ($preview) {
                        $photo = $preview;
                    }
//                    $photo = Photo::makeGif($photo, $story->id);
                }

                $stories[] = [
                    'error' => $errors,
                    'story' => $story,
                    'publish' => \DB::table('stories')->find($story->id)->to_publish,
                    'object' => is_null($story->stories_type)
                        ?
                        'Контентный'
                        :
                        $this->objectsOfProject[$story->stories_type]['name'],
                    'image' => $photo,
                    'users' => (new Stories)->getAuthorEditor($story)
                    ];
            }

            $objects = $this->getObjects($project);

            return view('stories.index', [
                'stories' => $stories,
                'objects' => $objects,
                'publicStories' => "https://vk.com/public$project?act=stories",
                'storySort' => $sort
            ])->render();
        }
    }

    public function post(Request $request, $project = null)
    {
        $sort = (json_decode($request->local, true));
        if (key_exists('post_sort', $sort) && !empty(json_decode($request->local, true)['post_sort'])) {
            $sort = json_decode($request->local, true)['post_sort'];
        } else {
            $sort = 'asc';
        }
        if ($request->ajax()) {
            return $this->printPosts($project, 'project.posts', $sort);
        }
    }

    protected function printPosts($project, $view, $sort)
    {
        if (is_null(\DB::table('projects')->find($project)->content_plan)) {
            return view('project.posts', [
                'cpError' => 'Не выбран контент план. Перейдите во вкладку "Проект"',
                'posts' => [],
                'deferredPosts' => "https://vk.com/wall-$project?postponed=1",
                'postSort' => $sort
            ])->render();
        }
        $loadErrors = null;
        $errorPost = $this->checkPostExist($project);
        $contentPlan = \DB::table('content_plan_post')->where('content_plan', \DB::table('projects')->find($project)->content_plan)->get();
        $posts = null;
        foreach (Post::where('project_id', $project)
                     ->where('content_plan', \DB::table('projects')->find($project)->content_plan)
                     ->where('publish_date', '>', date('Y-m-d H:i', strtotime('-2 days', time())))
                     ->orWhere((function ($query) use ($project) {
                         $query
                             ->where('content_plan', null)
                             ->where('publish_date', '>', date('Y-m-d H:i', strtotime('-2 days', time())))
                             ->where('project_id', $project);
                     }))
                     ->orderBy('vk_id')->orderBy('publish_date', $sort)->get() as $post) {
            $message = $this->replaceTags($project, $post->text);
            if ($post->object_id) {
                try {
                    $message = $this->replaceTags($project, $post->text, $post->post_type, $post->object_id);
                } catch (\Exception $e) {
                    $loadErrors = ['Вы недавно обновляли данные товаров, могут быть ошибки в замене тегов'];
                }
            }

            $error = $this->checkError($message, $project, $post);

            $photo = Photo::find($post->image);
            if ($photo && file_exists($photo->path)) {
                $photo = $photo->path;
                if (preg_match('/^video/', mime_content_type($photo))) {
                    $preview = Photo::find($post->image)->preview;
                    if ($preview) {
                        $photo = $preview;
                    }
                } else {
                    if ($post->border) {
                        $border = Photo::find(Project::find($project)->border);
                        if ($border) {
                            try {
                                $bordered = Photo::makeBorder($photo, $border->path, $post->id);
                                $photo = $bordered;
                            } catch (\Exception $e) {
                                $error[] = $e->getMessage();
                            }
                        }
                    }
                }
            } else {
                $photo = null;
            }
            if (key_exists($post->id, $errorPost)) {
                $error[] = $errorPost[$post->id];
            }
            $posts[] = [
                'error' => $error,
                'post' => $post,
                'object' => is_null($post->post_type)
                    ?
                    'Контентный'
                    :
                    $this->objectsOfProject[$post->post_type]['name'],
                'categories' => DishType::select('name')->groupBy('name')->get(),
                'image' => $photo,
                'text' => $message,
                'users' => (new Post)->getAuthorEditor($post)];
        }

        $objects = $this->getObjects($project);

        return view($view, [
            'posts' => $posts,
            'objects' => $objects,
            'deferredPosts' => "https://vk.com/wall-$project?postponed=1",
            'loadErrors' => $loadErrors,
            'postSort' => $sort
        ])->render();
    }

    public function guestAccess($project, $access_token)
    {
        if (Project::where('id', $project)->where('token', $access_token)->get()->count() > 0) {
            return $this->printPosts($project, 'guest.project', 'asc');
        }
        return abort(404);
    }

    public function updateToken($project)
    {
        $project = Project::find($project);
        if ($project) {
            $token = $this->generateRandomString(16);
            $project->token = $token;
            $project->save();
        } else {
            throw new \Exception('Проект не найден');
        }
        return URL::to('/guest/project/') . "/{$project->id}/$token";
    }

    public function selectPostType(Request $request, $project)
    {
        $request->validate([
            'post' => 'required|integer',
            'object' => 'integer'
        ]);
        if (!key_exists($request->type, $this->objectsOfProject)) {
            return false;
        }
        $columns = Schema::getColumnListing($request->type);
        $images = preg_grep('/^image_img/', Schema::getColumnListing($request->type));
        $object = \DB::table($request->type)->find($request->object);
        if ($object) {
            $post = Post::find($request->post);
            $post->object_id = $request->object;
            if (empty($images)) {
                $post->image = \DB::table('content_plan_post')->find($post->post_reference)->image;
            } else {
                $post->image = $object->{array_shift($images)};
            }
            $post->save();
        } else {
            return false;
        }
        return true;
    }

    public function pageRender(Request $request, $page, $project = null)
    {
        if ($project && Auth::user()->role != 'admin') {
//            Log::info(Auth::user()->getAuthIdentifier() . '  ' . \DB::table('employee_project')->where('project', $project)->where('employee', Auth::user()->getAuthIdentifier())->get()->count());
            if (!\DB::table('employee_project')->where('project', $project)->where('employee', Auth::user()->getAuthIdentifier())->get()->count()) {
                throw new \Exception('Проект недоступен!');
            }
        }

        switch ($page) {
            case 'info' :
                return $this->info($request, $project);
            case 'post' :
                return $this->post($request, $project);
            case 'stories' :
                return $this->story($request, $project);
        }

        $objects = null;
        if (!is_null($project)) {
            $objects = $this->getObject($project, $request->object, 'active');
        }
        return view('layout.print_object', ['objects' => $objects, 'table' => $request->object]);
    }

    public function objectPage(Request $request, $object)
    {
        $objects = $this->getObject($request->project, $object, $request->type);
        if ($request->ajax()) {
            return view('layout.object_page', ['objects' => $objects, 'table' => $object])->render();
        }
    }

    private function getObject($project, $page, $type)
    {
        $objects = null;
        $columns = Schema::getColumnListing($page);
        if (Schema::hasTable("project_$page") && in_array('archive', $columns)) {
            $projectList = \DB::table("project_$page")->where('project_id', $project)->get();
            foreach ($projectList as $item) {
                $object = \DB::table($page)->find($item->{$page . '_id'});
                if ($object && $object->archive == ($type == 'active' ? 0 : 1)) {
                    $objects[] = $object;
                }
            }
        }
        return $objects;
    }

    public function objectInfo(Request $request, $object)
    {
        if ($request->ajax()) {
            $privateParams = ['name', 'archive', 'parent_id'];
            $info = \DB::table($object)->find($request->id);
            $currentObject['name'] = $info->name;
            foreach ($info as $key => $value) {
                if (!in_array($key, $privateParams)) {
                    if (preg_match('/^image_/', $key)) {
                        if (Photo::find($value)) {
                            $currentObject['image'][] = ['path' => Photo::find($value)->path];
                        }
                    } else {
                        if (preg_match('/^id_/', $key)) {
                            $table = str_replace('id_', '', $key);
                            $value = \DB::table($table)->find($value);
                            if ($value) {
                                $value = $value->name;
                            } else {
                                $value = 'Данные не найдена';
                            }
                        }
                        $currentObject['data'][] = ['key' => $key, 'value' => $value];
                    }
                }
            }
            return view('layout.object_modal', ['currentObject' => $currentObject, 'table' => $object])->render();
        }
        return redirect('/');
    }

    public function saveObject(Request $request, $object)
    {
        $errors = [];
        $validated = $request->validate([
            $object . '_csv' => 'required|mimes:csv,txt',
            'project' => 'required|numeric'
        ]);
        try {
            $errors += $this->loadProjectCSV($request->file($object . '_csv')->getRealPath(), $object, $request->project, $object);
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
        return back()->withErrors($errors);
    }

    public function info(Request $request, $project = null)
    {
        $project = Project::find($project);
        $info = [
            'time_zone' => [
                'name' => 'Часовой пояс',
                'value' => $project->time_zone,
            ],
            'trigger_words' => [
                'name' => 'Стоп слова',
                'value' => $project->trigger_words,
                'rule' => 'Введите слова через запятую',
                'example' => 'Пример: доставка,пицца,самовывоз'
            ]
        ];
        $data = [];
        $tags = \DB::table('tag_list')->get();
        foreach ($tags as $tag) {
            $value = \DB::table('tags')->where([
                ['tagId', '=', $tag->id],
                ['project_group', '=', $project->id]
            ]);
            $data += [$tag->tag => $value->exists() ? $value->value('value') : null];
        }
        $cpProject = $project->content_plan;
        $cpList = \DB::table('content_plan')->get();
        if ($request->ajax()) {
            return view('project.info', ['data' => $data, 'info' => $info, 'cpList' => $cpList, 'cpProject' => $cpProject,
                'link' => $project->token ? (URL::to('/guest/project/') . "/{$project->id}/{$project->token}") : null]);
        }
    }

    public function infoCpChange(Request $request, $project)
    {
        if ($request->cp != 'default') {
            $request->validate(['cp' => 'numeric']);
            \DB::table('projects')->where('id', $project)->update(['content_plan' => $request->cp]);
        }
    }

    private function projectFields($project)
    {
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

}
