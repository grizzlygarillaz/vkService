<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Photo;
use App\Models\Stories;
use App\Models\ContentPlan;
use App\Models\Project;
use \App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ContentPlanController extends Controller
{
    public function add(Request $request)
    {
        $cp = new ContentPlan;
        $cp->name = $request->name;
        $cp->save();
        return response()->json($this->show());
    }

    public function show()
    {
        $content = '';
        foreach (ContentPlan::all()->sortBy([['name', 'desc']]) as $key => $item) {
            $checked = ContentPlan::first()->id == $item->id ? 'checked' : '';
            $content .=
                "<input type='radio' class='btn-check cp-radio' $checked name='btn-cp' id='{$item->id}' autocomplete='off'>
                <label class='btn btn-outline-secondary' for='{$item->id}'>$item->name</label>";
        }
        return ['content_plan' => $content];
    }

    public function cpPosts($cp)
    {
        if (ContentPlan::where('id', $cp)->get()->count() < 1) {
            return null;
        }
        $posts = null;
        foreach (\DB::table('content_plan_post')->where('content_plan', $cp)->orderBy('publish_date', 'desc')->get() as $post) {
            $photo = null;
            $image = empty($post->image) ? null : (Photo::find($post->image) ? Photo::find($post->image)->path : null);
            if (!empty($image) && preg_match('/^video/', mime_content_type($image))) {
                $preview = Photo::find($post->image)->preview;
                if ($preview) {
                    $photo = $preview;
                }
            }
            $posts[] = ['post' => $post,
                'type' => key_exists($post->post_type, $this->objectsOfProject)
                    ? $this->objectsOfProject[$post->post_type]['name'] : 'Контентый',
                'image' => $photo ? $photo : $image,
                'users' => (new Post)->getAuthorEditor($post)];
        }

        return $posts;
    }

    public function cpStories($cp)
    {
        Storage::delete(Storage::allFiles('public/temp'));
        if (ContentPlan::where('id', $cp)->get()->count() < 1) {
            return null;
        }
        $stories = null;
        foreach (\DB::table('content_plan_stories')->where('content_plan', $cp)->orderBy('publish_date', 'desc')->get() as $story) {
            $photo = null;
            $image = empty($story->content) ? null : (Photo::find($story->content) ? Photo::find($story->content)->path : null);
            if (!empty($image) && preg_match('/^video/', mime_content_type($image))) {
                $preview = Photo::find($story->content)->preview;
                if ($preview) {
                    $photo = $preview;
                }
            }
            $stories[] = [
                'story' => $story,
                'object' => key_exists($story->stories_type, $this->objectsOfProject)
                    ? $this->objectsOfProject[$story->stories_type]['name'] : 'Контентый',
                'image' => $photo ? $photo : $image,
                'users' => (new Stories)->getAuthorEditor($story)];
        }

        return $stories;
    }

    public function index(Request $request, $cplan = null)
    {
        if (empty($cplan)) {
            $cplan = ContentPlan::first()->id;
        }
        if ($request->ajax()) {
            if ($request->page == 'posts') {
                return view('posts._post', ['cp_post' => $this->cpPosts($cplan)])->render();
            } elseif ($request->page == 'stories') {
                return view('stories.index_cp', ['stories' => $this->cpStories($cplan)])->render();
            }
        }

        $return = ['page' => 'КОНТЕНТ-ПЛАН',
            'cp_post' => $this->cpPosts($cplan),
            'postType' => $this->objectsOfProject];
        $return += $this->show();
        return view('posts.content_plan', $return);
    }

    //refactor
    public function addPost(PostRequest $request)
    {
        $image = new Photo;
        if (!is_null($request->image)) {
            $image = $image->downloadFromYandex($request->image)['id'];
        } else {
            $image = null;
        }
        $date = date('Y-m-d H:i:s', strtotime($request->publishDate));
        $insertData = [
            'content_plan' => $request->cp,
            'publish_date' => $date,
            'text' => $request->text,
            'post_type' => $request->postType,
            'image' => $image,
            'author' => Auth::user()->id
        ];
        if ($request->notification) {
            $insertData += ['mute' => 0];
        }
        if (empty($request->postType)) {
            $insertData += ['border' => $request->border ? 1 : 0];
        }
        if ($request->cp) {
            $insertData += ['content_plan' => $request->cp];
            $postId = \DB::table('content_plan_post')->insertGetId($insertData);
        }
        if ($request->project) {
            $projects = Project::where('id', $request->project)->get();
        } else {
            $projects = Project::all();
        }
        foreach ($projects as $project) {
            $post = new Post;
            if ($request->notification) {
                $post->mute = 0;
            }
            if (empty($request->postType)) {
                $post->border = $insertData['border'];
            }
            $post->project_id = $project->id;
            $post->publish_date = $date;
            $post->author = Auth::user()->id;
            $post->post_type = $request->postType;
            $post->text = $request->text;
            $post->image = $image;
            if ($request->cp) {
                $post->post_reference = $postId;
                $post->content_plan = $request->cp;
            }
            $post->save();
            usleep(500);
        }
        return back();
    }

//newAdd
    public function deleteCp($cp)
    {
        foreach (\DB::table("content_plan_post")->where('content_plan', $cp)->get() as $cp_post) {
            foreach (\DB::table("posts")->where('post_reference', $cp_post)->get() as $post) {
                $photo = Photo::find(\DB::table("posts")->find($post->id));
                if ($photo) {

                }
            }
        }
        \DB::table("content_plan_post")->where('content_plan', $cp)->delete();

    }

    public function addPoll(Request $request)
    {
        $request->validate([
            'answer' => 'required|array|between:1,10',
            'answer.*' => 'required',
            'question' => 'required',
            'publishDate' => 'required|date_format:d.m.Y H:i|after:today',
            'cp' => 'required',
            'background_poll' => 'required'
        ]);
        if ($request->background_poll != 'photo') {
            $background = '"background":' . $request->background_poll;
        } else {
            $request->validate(['image' => 'required']);
        }
        $answer = json_encode($request->answer);
        $anonym = $request->anonymous ? 1 : 0;
        $poll = '{"answer":' . $answer . ',"question":"' . $request->question . '","anonymous":' . $anonym
            . (isset($background) ? ",$background" : '') . '}';
        $image = new Photo;
        if (!is_null($request->image)) {
            $image = $image->downloadFromYandex($request->image)['id'];
        } else {
            $image = null;
        }
        $date = date('Y-m-d H:i:s', strtotime($request->publishDate));
        $insertData = [
            'publish_date' => $date,
            'text' => $request->text,
            'post_type' => $request->postType,
            'image' => $image,
            'poll' => $poll
        ];
        if ($request->notification) {
            $insertData += ['mute' => 0];
        }
        if (empty($request->postType)) {
            $insertData += ['border' => $request->border ? 1 : 0];
        }
        if ($request->cp) {
            $insertData += ['content_plan' => $request->cp];
            $postId = \DB::table('content_plan_post')->insertGetId($insertData);
        }
        foreach (Project::all() as $project) {
            $post = new Post;
            if ($request->notification) {
                $post->mute = 0;
            }
            $post->project_id = $project->id;
            $post->publish_date = $date;
            $post->post_type = $request->postType;
            $post->text = $request->text;
            $post->image = $image;
            $post->author = Auth::user()->id;
            if ($request->cp) {
                $post->post_reference = $postId;
                $post->content_plan = $request->cp;
            }
            $post->poll = $poll;
            $post->save();
            usleep(500);
        }
        return back();
    }

    public function addStory(Request $request)
    {
        $validation = $request->validate([
            'story_link' => 'required',
            'publishDate' => 'required|date_format:d.m.Y H:i|after:today'
        ]);
        $image = new Photo;
        if (!empty($request->story_link)) {
            try {
                $image = $image->downloadFromYandex($request->story_link);
                $image = $image['id'];
            } catch (\Exception $e) {
                return back()->withErrors($e->getMessage());
            }
        } else {
            $image = null;
        }
        $date = date('Y-m-d H:i:s', strtotime($request->publishDate));
        $insertData = [
            'publish_date' => $date,
            'content' => $image,
            'stories_type' => $request->storyType,
            'author' => Auth::user()->id
        ];
        if ($request->cp) {
            $insertData += ['content_plan' => $request->cp];
            $storyId = \DB::table('content_plan_stories')->insertGetId($insertData);
            $insertData += [
                'reference' => $storyId,
                'project_id' => null
            ];
        }
        foreach (Project::all() as $project) {
            $insertData['project_id'] = $project->id;
            \DB::table('stories')->insert($insertData);
            usleep(500);
        }
        return back();
    }

    public function editModal($post)
    {
        $post = \DB::table('content_plan_post')->find($post);
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
                'editFrom' => 'content_plan'
            ])->render();
        }
        return view('posts.edit_post_modal', [
            'cpPostEdit' => $post,
            'cpPostImage' => $image,
            'postType' => $this->objectsOfProject
        ])->render();
    }

    public function saveEditPost($type, $post, PostRequest $request)
    {
        $image = new Photo;
        $removeImage = \DB::table('content_plan_post')->find($post)->image;
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
            'image' => $image,
            'edited_by' => Auth::user()->id
        ];

        if ($type == 'poll') {
            $request->validate([
                'answer' => 'required|array|between:1,10',
                'answer.*' => 'required',
                'question' => 'required',
                'publishDate' => 'required|date_format:d.m.Y H:i|after:today',
                'cp' => 'required',
                'background_poll' => 'required'
            ]);
            if ($request->background_poll != 'photo') {
                $background = '"background":' . $request->background_poll;
            } else {
                $request->validate(['image' => 'required']);
            }
            $answer = json_encode($request->answer);
            $anonym = $request->anonymous ? 1 : 0;
            $poll = '{"answer":' . $answer . ',"question":"' . $request->question . '","anonymous":' . $anonym
                . (isset($background) ? ",$background" : '') . '}';
            $image = new Photo;
            if (!is_null($request->image)) {
                $image = $image->downloadFromYandex($request->image)['id'];
            } else {
                $image = null;
            }
        }

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

        \DB::table('content_plan_post')->where('id', $post)->update($updateData);

        foreach (Project::all() as $project) {
            Post::where([
                'post_reference' => $post,
                'project_id' => $project->id
            ])
                ->update($updateData);
            usleep(500);
        }
        return back();
    }

    public function storyEditModal($story)
    {
        $story = \DB::table('content_plan_stories')->find($story);
        $content = Photo::find($story->content);
        if ($content) {
            $content = $content->link;
        } else {
            $content = '';
        }
        $publish_date = date('d.m.Y H:i', strtotime($story->publish_date));
        $data = ['story' => $story, 'content' => $content, 'date' => $publish_date];
        return view('stories.edit_modal', ['data' => $data, 'cp' => 1])->render();
    }

    public function storySaveEdit($story, Request $request)
    {
        $storyCP = \DB::table('content_plan_stories')->find($story);
        $request->validate([
            'publishDate' => 'required|date_format:d.m.Y H:i|after:today',
            'story_link' => 'required'
        ]);
        if (!empty($storyCP->content)) {
            (new Photo)->deletePhoto($storyCP->content);
        }
        try {
            $content = (new Photo)->downloadFromYandex($request->story_link)['id'];
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

        $date = date('Y-m-d H:i:s', strtotime($request->publishDate));
        $updateData = [
            'content' => $content,
            'publish_date' => $date,
            'edited_by' => Auth::user()->id
        ];
        $projectUpdate = $updateData + ['vk_id' => null];
        foreach (Project::all() as $project) {
            \DB::table('stories')->where([
                'reference' => $story,
                'project_id' => $project->id
            ])->update($projectUpdate);
            usleep(500);
        }
        \DB::table('content_plan_stories')->where('id', $storyCP->id)->update($updateData);
        return back();
    }

    public function deletePost(Request $request, $post)
    {
        $image = new Photo;
        Log::info('Deleted post by : ' . Auth::user()->id . " ----> " . \DB::table('content_plan_post')->find($post)->id );
        $removeImage = \DB::table('content_plan_post')->find($post)->image;
        if (!empty($removeImage)) {
            $image->deletePhoto($removeImage);
        } else {
            $image = null;
        }
        foreach (Project::all() as $project) {
            Post::where([
                'post_reference' => $post,
                'project_id' => $project->id
            ])->delete();
            usleep(500);
        }
        \DB::table('content_plan_post')->where('id', $post)->delete();
        return back();
    }

    public function deleteStory($story)
    {
        $storyCP = \DB::table('content_plan_stories')->find($story);
        Log::info('Deleted story by : ' . Auth::user()->id, \DB::table('content_plan_stories')->find($story));
        if (!empty($storyCP->content)) {
            (new Photo)->deletePhoto($storyCP->content);
        }
        foreach (Project::all() as $project) {
            \DB::table('stories')->where([
                'reference' => $story,
                'project_id' => $project->id
            ])->delete();
            usleep(500);
        }
        \DB::table('content_plan_stories')->where('id', $storyCP->id)->delete();
        return back();
    }
}
