<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\Stories;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;

class StoriesController extends Controller
{
    public function send ($project, Request $request) {
        $story = \DB::table('stories')->find($request->story);
        \DB::table('stories')->where('id', $request->story)->update(['to_publish' => ($story->to_publish ? 0 : 1)]);
//        $story = \DB::table('stories')->find($request->story);
//        return Stories::send($project, $story);
    }

    public function edit ($story, Request $request) {
        $story = Stories::find($story);
        $validation = $request->validate([
            'publishDate' => 'required|date_format:d.m.Y H:i|after:today',
            'story_link' => 'required'
        ]);
        try {
            $content = Photo::downloadYandex($request->story_link, $story->project_id, 'story')['id'];
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
        $date = date('Y-m-d H:i:s', strtotime($request->publishDate));
        \DB::table('stories')->where('id', $story->id)->update([
            'content' => $content,
            'publish_date' => $date
        ]);
        return back();
    }

    public function delete($story)
    {
        Stories::find($story)->delete();
    }

    public function getModal($story) {
        $story = Stories::find($story);
        $content = Photo::find($story->content)->link;
        $publish_date = date('d.m.Y H:i', strtotime($story->publish_date));
        $data = ['story' => $story, 'content' => $content, 'date' => $publish_date];
        return view('stories.edit_modal', ['data' => $data])->render();
    }
}
