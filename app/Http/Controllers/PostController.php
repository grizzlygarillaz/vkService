<?php

namespace App\Http\Controllers;

use App\Models\Ads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;
use phpDocumentor\Reflection\Types\This;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\User;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Vk;
use App\Models\Photo;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\Console\Helper\Table;
use \Statickidz\GoogleTranslate;

class PostController extends Controller
{
    private $token;
    private $photo;


    public function send($request)
    {
        $groups = ['201495762', '201495826', '201313982'];
        $baseMessage = $request['message'];
        foreach (Project::all() as $project) {
            $message = $this->token->replaceTags($project->id, $baseMessage);
            $group = $groups[rand(0, count($groups) - 1)];
            $this->token->sendPost($group, $message);
            usleep(500000);
        }
        return true;
    }

    public function editPoll($post, $type, Request $request)
    {
        $image = new Photo;
        $removeImage = \DB::table('content_plan_post')->find($post) ? \DB::table('content_plan_post')->find($post)->image : null;
        if (!empty($removeImage)) {
            $image->deletePhoto($removeImage);
        } else {
            $image = null;
        }
        $image = new Photo;
        if (!is_null($request->image)) {
            $image = $image->downloadFromYandex($request->image)['id'];
        } else {
            $image = null;
        }

        $date = date('Y-m-d H:i:s', strtotime($request->publishDate));
        $updateData = [
            'publish_date' => $date,
            'post_type' => $request->postType,
            'text' => $request->text,
            'image' => $image
        ];

        $request->validate([
            'answer' => 'required|array|between:1,10',
            'answer.*' => 'required',
            'question' => 'required',
            'publishDate' => 'required|date_format:d.m.Y H:i|after:today',
            'background_poll' => 'required'
        ]);
        if ($request->background_poll != 'photo') {
            $background = '"background":' . $request->background_poll;
        } else {
            $request->validate(['image' => 'required']);
        }
        $answer = json_encode($request->answer);
        $anonym = $request->anonymous ? 1 : 0;
        $poll = '{"answer":' . $answer . ',"question":"' . $request->question . '","anonymous":"$anonym"'
            . (isset($background) ? ",$background" : '') . '}';
        $image = new Photo;
        if (!is_null($request->image)) {
            $image = $image->downloadFromYandex($request->image)['id'];
        } else {
            $image = null;
        }
        $updateData += ['poll' => $poll];
        $updateData += ['edited_by' => Auth::user()->id];

        if ($request->notification) {
            $updateData += ['mute' => 0];
        } else {
            $updateData += ['mute' => 1];
        }
        if ($request->border) {
            $updateData += ['border' => 1];
        } else {
            $updateData += ['border' => 0];
        }

        if ($type == 'content_plan') {
            \DB::table('content_plan_post')->where('id', $post)->update($updateData);

            foreach (Project::all() as $project) {
                Post::where([
                    'post_reference' => $post,
                    'project_id' => $project->id
                ])
                    ->update($updateData);
                usleep(500);
            }
        }

        if ($type == 'project') {
            Post::where([
                'id' => $post
            ])->update($updateData);
            usleep(500);
        }
        return back();
    }

    public static function getPoll($post)
    {
        $post = Post::find($post);
        $poll = json_decode($post->poll, true);
        $response = [
            'owner_id' => -$post->project_id,
            'disable_unvote' => 1,
            'question' => $poll['question'],
            'add_answers' => json_encode($poll['answer']),
            'is_anonymous' => $poll['anonymous'],
        ];
        if ($post->image) {
            $image = Photo::find($post->image)->path;
            if (preg_match('/^image/', mime_content_type($image))) {
                $content = (new Photo)->getPollPhoto($post->project_id, $image);
                Log::info($content);
                $content = $content['id'];
                $response += ['photo_id' => $content];
            }
        } elseif (key_exists('background', $poll)) {
            $response += ['background_id' => (int)$poll['background']];
        }
        Log::info($response);
        return Vk::customStaticRequest('polls', 'create', $response);
    }

    public function sendPromo(PostRequest $request)
    {
        $count = 0;
        $baseMessage = $request->message;
        $photos = $this->photo->get($this->photo->promoAlbum($request->promo))['items'];
        foreach (Project::all() as $project) {
            $group = $project->id;
            if (!is_null($group)) {
                $photo = $photos[array_rand($photos)];
                $photo = "photo{$photo['owner_id']}_{$photo['id']}";
                $message = $this->replaceTags($project->id, $baseMessage);
                $this->token->sendDeferredPost($group, $message, $request->publishDate);
                $count++;
            }
            usleep(100000);
        }
        return $count > 0;
    }


    public function sendGroupPost($project, $post, $photo)
    {
        $post_obj = new Post;
        $post = Post::find($post);
        $project = Project::find($project);
        $post_obj->sendDeferredPost($project->id, $post->text, $date = $post->publishDate);
    }


    public function sendPost(PostRequest $request)
    {
        $this->sendPromo($request) ?
            Alert::toast('Пост успешно отправлен', 'success')
            :
            Alert::toast('Что-то пошло не так', 'error');
        return back();
    }

    public function get($post)
    {
        $data = [];

        $post = Post::find($post);
        $data['post'] = $post;

        $data['postImage'] = ($post->image) ?
            Photo::find($post->image)->link
            :
            '';

        $image = Photo::find($post->image);
        if ($image) {
            $image = $image->link;
        } else {
            $image = null;
        }
        if ($post->poll) {
            return view('posts.polls.edit_modal', [
                'poll' => $post,
                'pollImage' => $image,
                'pollJSON' => json_decode($post->poll, true),
                'postType' => $this->objectsOfProject,
                'editFrom' => 'project'
            ])->render();
        }
        $data['textarea'] = 'post-project-edit-text';

        $data['tags'] = $this->objectTagList($data['textarea'], $post->post_type);

        return view('project.post_edit_modal', $data)->render();
    }

    public function sendComment($post, Request $request)
    {
        $request->validate([
            'comment' => 'required|max:1000'
        ]);
        $post = Post::find($post);
        if ($post) {
            $post->comment = $request->comment;
            $post->comment_viewed = !$request->comment;
            $post->save();
            return true;
        }
        throw new \Exception('Что-то пошло не так. Обратитесь к администратору (или к тому, от кого вы получили ссылку).');
    }

    public function commentViewed ($post)
    {
        $post = Post::find($post);
        if ($post) {
            $post->comment_viewed = true;
            $post->save();
        }
        return true;
    }

    public function edit($post, PostRequest $request)
    {
        $post = Post::find($post);

        $post->publish_date = date('Y-m-d H:i:s', strtotime($request->publishDate));

        $post->text = $request->text;

        if ($request->notification) {
            $post->mute = 0;
        } else {
            $post->mute = 1;
        }
        $post->edited_by = Auth::user()->id;
        $post->border = $request->border ? 1 : 0;
        if ($request->post_image) {
            $photo = new Photo;
            $post->image = $photo->downloadFromYandex($request->post_image, null, "post/{$post->id}")['id'];
        }
        $post->save();

        return back();
    }

    public function modalAddToProject($project)
    {
        return view('project.add_post_modal', ['project' => $project]);
    }

    public function delete($post)
    {
        Post::find($post)->delete();
    }

    public function sendDeferredPost(Request $request, $post)
    {
        $posts = $post == 'all' ? $request->posts : [$post];
        if(empty($posts)) {
            throw new \Exception('Нет постов на отправку');
        }
        foreach ($posts as $post) {
            $project = Project::find($request->project);
            $post = Post::find($post);
            if (!$project || !$post || $post->error) {
                return back()->withErrors('Ошибка отправки поста. Обратитесь к администратору');
            }

            $object = $post->object_id;
            $imageCheck = $post->image;
            if ($post->post_type == 'dish' && $post->object_id == 'queue') {
                $queueObject = \DB::table('project_dish')->where('project_id', $project->id)->orderBy('queue')->first();
                $object = $queueObject->dish_id;
                $imageCheck = \DB::table('dish')->find($object)->image_img_url;
            }
            $message = $this->replaceTags($project->id, $post->text, $post->post_type, $object);
            $timeZone = $project->time_zone < 0 ? $project->time_zone : "+{$project->time_zone}";
            $timeZone -= 3 + (2 * $timeZone);
            $date = date('d.m.Y H:i', strtotime("$timeZone hours", strtotime($post->publish_date)));

            $postClass = new Post;
            $image = Photo::find($imageCheck)->path;
            try {
                if ($post->poll) {
                    $poll = PostController::getPoll($post->id);
                    $content = ["poll{$poll["owner_id"]}_{$poll['id']}"];
                    $postId = $postClass->sendDeferredPost($project->id, $message, $date, $post->mute, $content);
                } elseif (!is_null($imageCheck) && file_exists($image)) {
                    if (preg_match('/^video/', mime_content_type($image))) {
                        $content = (new Photo)->getVideoServer($project->id, $image);
                        $content = ["video{$content['owner_id']}_{$content['video_id']}"];
                    } else {
                        $photo = new Photo;
                        if ($post->border) {
                            $border = Photo::find(Project::find($project->id)->border);
                            if ($border) {
                                $bordered = Photo::makeBorder($image, $border->path, $post->id);
                                $image = $bordered;
                            }
                        }
                        $savedPhoto = $photo->saveWallPhoto($project->id, $image);
                        $content = ["photo{$savedPhoto[0]['owner_id']}_{$savedPhoto[0]['id']}"];
                    }
                    $postId = $postClass->sendDeferredPost($project->id, $message, $date, $post->mute, $content);
                } else {
                    $postId = $postClass->sendDeferredPost($project->id, $message, $date, $post->mute);
                }
            } catch (\Exception $e) {
                if (preg_match('/invalid publish_date param/', $e->getMessage()) || preg_match('/post is already scheduled/', $e->getMessage())) {
                    throw new \Exception('В отложенных записях имеется пост со схожей датой! Пожалуйста, измените дату публикации.');
                } else {
                    throw new \Exception('Неизвестная ошибка отправки публикации. Пожалуйста, обратитесь к администратору' . $e->getMessage());
                }
            }
            if (key_exists('post_id', $postId)) {
                \DB::table('posts')->where('id', $post->id)->update(['vk_id' => $postId['post_id']]);
//                if ($post->post_type == 'dish') {
//                    if (!in_array('out_queue', Schema::getColumnListing('dish'))) {
//                        Schema::table('dish', function($table) {
//                            $table->integer('out_queue')->nullable();
//                        });
//                    }
//                    for ($i = 0; $i < \DB::table('project_dish')->where('project_id', $project->id)->get()->count(); $i++) {
//
//                    }
//                }
            } else {
                return false;
            }
            sleep(1);
        }
        return true;
    }


}
