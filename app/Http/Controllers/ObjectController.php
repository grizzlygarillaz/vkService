<?php

namespace App\Http\Controllers;

use App\Models\DishType;
use App\Models\Photo;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ObjectController extends Controller
{
    public function delete($table, $id)
    {
        $object = \DB::table($table)->find($id);
        if ($object) {
            foreach (Schema::getColumnListing($table) as $column) {
                if (preg_match('/^image_/', $column) && Photo::find($object->{$column})) {
                    Photo::find($object->{$column})->delete();
                }
            }
            \DB::table($table)->where('id', $id)->delete();
            return true;
        }
        throw new \Exception('Что-то пошло не так');
    }

    public function getCategory($project, Request $request)
    {
        $result = null;
        $dishes = \DB::table('project_dish')->where('project_id', $project)->get();
        $post = Post::find($request->post);
        $filters = DishType::where('name', $request->category)->get();
        $allFilters = DishType::all();
        if ($request->category == 'other') {
            foreach ($allFilters as $filter) {

            }
        }

        foreach ($dishes as $dish) {
            $dish = \DB::table('dish')->find($dish->dish_id);
            if ($request->category == 'all') {
                $result[] = $dish;
                continue;
            }
            if ($request->category == 'other') {
                $checking = false;
                foreach ($allFilters as $filter) {
                    if ($filter->filter && mb_stristr($dish->name, $filter->filter)) {
                        $checking = true;
                    }
                }
                if (!$checking && !empty($dish->name)) {
                    $result[] = $dish;
                }
                continue;
            }
            foreach ($filters as $filter) {
                if ($filter->filter && mb_stristr($dish->name, $filter->filter) !== false) {
                    $result[] = $dish;
                    continue;
                }
            }

        }

        return view('posts.post_type', [
            'objects' => $result,
            'type' => $post->post_type,
            'selected' => $post->object_id,
            'typeName' => $this->objectsOfProject[$post->post_type]['name']
        ])->render();
    }
}
