<?php

namespace App\Http\Controllers;

use App\Models\Ads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\This;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Promo;
use App\Models\Photo;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\Console\Helper\Table;
use \Statickidz\GoogleTranslate;

class PostController extends Controller
{
    private $token;
    private $photo;

    public function __construct()
    {
        $this->photo = new Photo();
        $this->token = new Post();
        $tags = new Tag;
        $trans = new GoogleTranslate();
        foreach (Schema::getColumnListing('projects') as $property) {
            if (Tag::where('property', '=', $property)->count() == 0 && !in_array($property, Project::all()->first()->hidden)) {
                $tag = '::' . strtoupper($property) . '::';
                $tags->tag = $tag;
                $tags->property = $property;
                $tags->save();
            }
        }
        foreach (Tag::all() as $tag) {
            if (is_null($tag->description)) {
                $result = '';
                $tagText = implode(' ', explode('_', $tag->property));
                $result .= $trans->translate('en', 'ru', $tagText) . ' ';
                $tag->description = $result;
                $tag->save();
            }
        }
    }

    public function send($request)
    {
        $groups = ['201495762', '201495826', '201313982'];
        $baseMessage = $request['message'];
        foreach (Project::all() as $project) {
            $message = $this->token->replaceTags($project->id,$baseMessage);
            $group = $groups[rand(0, count($groups) - 1)];
            $this->token->sendPost($group, $message);
            usleep(500000);
        }
        return true;
    }

    public function sendPromo(PostRequest $request) {
        $count = 0;
        $baseMessage = $request->message;
//        $photos = $this->photo->get($this->photo->promoAlbum($request->promo))['items'];
        foreach (Project::all() as $project) {
            $group = $project->group_id;
            if (! is_null($group)) {
//                $photo = $photos[array_rand($photos)];
//                $photo = "photo{$photo['owner_id']}_{$photo['id']}";
//                $message = $this->replaceTags($project->id,$baseMessage);
                $message = '<head>  <meta property="vk:image" content="https://sun9-17.userapi.com/impf/c540106/v540106693/cfd3/-6hDUYsgLmo.jpg?size=604x444&quality=96&sign=4fb7a3260365ba0d563a5c3166ac6275&type=album"/> </head>';
                $this->token->sendDeferredPost($group, $message, $request->publishDate);
                $count++;
            }
            usleep(100000);
        }
        return $count > 0;
    }

    public function addPost() {
        return view('posts.send', ['tags' => Tag::all(), 'promos' => Promo::all(), 'page' => 'ОТПРАВИТЬ ПОСТ']);
    }

    public function replaceTags($project, $message)
    {
        $project = Project::where('id', $project)->get()->first();
        foreach (Tag::all() as $tag) {
            if (strstr($message, $tag->tag) && in_array($tag->property, Schema::getColumnListing('projects')) && $project->{$tag->property} != null) {
                $message = str_replace($tag->tag, $project->{$tag->property}, $message);
            }
        }
        return $message;
    }

    public function sendPost(PostRequest $request) {
        $this->sendPromo($request) ?
            Alert::toast('Пост успешно отправлен', 'success')
            :
            Alert::toast('Что-то пошло не так', 'error');
        return back();
    }
}
