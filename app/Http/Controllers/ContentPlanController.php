<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\ContentPlan;

class ContentPlanController extends Controller
{
    public function add (Request $request) {
        $cp = new ContentPlan;
        $cp->name = $request->name;
        $cp->save();
        return response()->json($this->show());
    }

    public function show() {
        $content = '';
        foreach (ContentPlan::all() as $key => $item) {
            $content .=
                "<input type='radio' class='btn-check cp-radio' name='btn-cp' id='{$item->id}' autocomplete='off'>
                <label class='btn btn-outline-secondary' for='{$item->id}'>$item->name</label>";
        }
        return ['content_plan' => $content];
    }

    public function cpPosts ($cp) {
        if (ContentPlan::where('id',$cp)->get()->count() < 1) {
            return null;
        }
        return ContentPlan::find($cp)->posts()->get();
    }

    public function index(Request $request, $cplan = null) {
        if (empty($cplan)) {
            $cplan = ContentPlan::first()->id;
        }
        if ($request->ajax()) {
            return view('posts._post', ['cp_post' => $this->cpPosts($cplan)]);
        }
        $postType = \DB::table('post_type')->get();
        $promo = \DB::table('promo')->where('locked', true)->get();
        $return = ['page' => 'КОНТЕНТ-ПЛАН',
            'cp_post' => $this->cpPosts($cplan),
            'postType' => $postType,
            'promo' => $promo];
        $return += $this->show();
        return view('posts.content_plan', $return);
    }

    public function addPost(Request $request) {
        $post = new Post;
        $post->publish_date = date('Y-m-d H:i:s',strtotime($request->publishDate));
        $post->post_type = $request->postType;
        $post->dish_type = $request->dishType;
        $post->promo_id = $request->promo;
        $post->text = $request->text;
        $post->save();

        \DB::table('content_plan_post')->insert([
            'content_plan_id' => $request->cp,
            'post_id' => $post->id
        ]);
     }
}
