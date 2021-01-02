<?php

namespace App\Http\Controllers;

use App\Models\Ads;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\This;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Project;
use App\Models\Photo;
use RealRashid\SweetAlert\Facades\Alert;

class PostController extends Controller
{
    private $tags;
    private $token;
    private $project;
    private $photo;

    public function __construct()
    {
        $this->photo = new Photo();
        $this->token = new Post();
        $this->project = new Project();
        $this->tags = $this->token->getTags();
    }

    public function send($request)
    {
        $groups = ['201495762', '201495826', '201313982'];
        $baseMessage = $request['message'];
        $projects = $this->project->getProjects();
        foreach ($projects as $project) {
            $message = $this->token->replaceTags($project->id,$baseMessage);
            $group = $groups[rand(0, count($groups) - 1)];
            $this->token->sendPost($group, $message);
            usleep(500000);
        }
        return true;
    }

    public function sendPromo(PostRequest $request) {
        $groups = ['201495762', '201495826', '201313982'];
        $baseMessage = $request->message;
        $photos = $this->photo->get($this->photo->promoAlbum($request->promo))['items'];
        $projects = $this->project->getProjects();
        foreach ($projects as $project) {
            $photo = $photos[array_rand($photos)];
            $photo = "photo{$photo['owner_id']}_{$photo['id']}";
            $message = $this->token->replaceTags($project->id,$baseMessage);
            $group = $groups[rand(0, count($groups) - 1)];
            $this->token->sendDeferredPost($group, $message, $request->publishDate, [$photo]);
            usleep(500000);
            break;
        }
        return true;
    }

    public function addPost() {
        $tags = $this->tags;
        $promos = $this->project->getPromos();
        return view('posts.send', ['tags' => $tags, 'promos' => $promos, 'page' => 'ОТПРАВИТЬ ПОСТ']);
    }

    public function sendPost(PostRequest $request) {
        $this->sendPromo($request);
        Alert::toast('Пост успешно отправлен', 'success');
        return back();
    }
}
