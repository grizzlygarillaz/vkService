<?php

namespace App\Traits;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Post;
use Illuminate\Support\Facades\Log;
use \App\Traits\ArrayFunc;

trait PostErrors
{
    use ArrayFunc;

    public function checkError($message, $project, $post)
    {
        $errors = null;
        $errorCode = null;
        if ($post->publish_date < date('Y-m-d H:i:s', strtotime('+3hours'))) {
            $errors[] = 'Пост просрочен';
        }

        $trigger = Project::find($project)->trigger_words;
        if (!empty($trigger)) {
            $messageLowerCase = mb_strtolower($message);
            $triggerWords = explode(',', $trigger);
            foreach ($triggerWords as $word) {
                if (str_contains($messageLowerCase, $word)) {
                    $errors[] = 'Найдено стоп-слово "' . $word . '"';
                }
            }
        }

        if (preg_match('/\:\:\S*\:\:/', $message)) {
            $errors[] = 'Найден незаменённый тег. Необходимо выбрать дополнительные параметры!';
        }

        $errorTumbler = Post::find($post->id);
        $errorTumbler->error = !empty($errors);
        $errorTumbler->save();

        return $errors;
    }

    public function checkPostExist($project)
    {
        $postExist = new Post;
        $busyTime = null;
        $publishedPosts = Post::where([
            ['project_id', '=', $project],
            ['publish_date', '>', date('Y-m-d', strtotime('+4 hour'))],
        ])
            ->whereNotNull('vk_id')->get();
        $postsList = null;
        foreach ($publishedPosts as $published) {
            $postsList[] = "-{$project}_{$published->vk_id}";
        }
        if (!empty($postsList)) {
//
//        foreach ($vkPostponed as $postponed) {
//            $busyTime[] = ['start' => date('Y-m-d H:i:s', strtotime("+2 hours +30 minutes", $postponed['date'])), 'end' => date('Y-m-d H:i:s', strtotime("+3 hours +30 minutes", $postponed['date']))];
//        }
            $response = ['posts' => implode(',', $postsList)];
            $list = $postExist->apiResult('getById', $response);
//            foreach ($publishedPosts as $published) {
//                $found = $this->search_key_val($list, 'id', $published->vk_id, true);
//                $found = array_search($published->vk_id, array_column($list, 'id'));
//                if (empty($found)) {
//                    Post::where([['vk_id', $published->vk_id], ['project_id', $project]])->update(['vk_id' => null]);
//                }
//            }

//            Log::info($publishedPosts);
            foreach ($list as $item) {
                $busyTime[] = [
                    'start' => date('Y-m-d H:i:s', strtotime("+2 hours +30 minutes", $item['date'])),
                    'end' => date('Y-m-d H:i:s', strtotime("+3 hours +30 minutes", $item['date']))
                ];
                $publishedPosts = $publishedPosts->filter(function ($value) use ($item) {
                    return $value->vk_id != $item['id'];
                });
            }

            foreach ($publishedPosts as $published) {
                Post::where([['vk_id', $published->vk_id], ['project_id', $project]])->update(['vk_id' => null]);
            }
            Log::info($publishedPosts);
        }
//        if (!empty($postsList)) {
//            $response = ['owner_id' => "-$project", 'count' => 50];
//            $postedVK = $postExist->apiResult('get', $response)['items'];

//            $response = ['posts' => implode(',', $postsList)];
//            $list = $postExist->apiResult('getById', $response);
//            foreach ($publishedPosts as $published) {
//                $found = $this->search_key_val($list, 'id', $published->vk_id, true);
//                $found = array_search($published->vk_id, array_column($list, 'id'));
//                if (!empty($found)) {
//                    Post::where([['vk_id', $published->vk_id], ['project_id', $project]])->update(['published' => $found['post_type']]);
//
//                    if ($found['post_type'] == 'post' && $published->published == 'postpone' && $published->post_type == 'dish') {
//                        Log::info('test');
//                        $currentDish = \DB::table('project_dish')->find($published->object_id);
//                        if (is_null($currentDish->queue)) {
//                            $dishList = \DB::table('project_dish')->where('project_id', $project)->get();
//                            $count = 0;
//                        } else {
//                            $dishList = \DB::table('project_dish')->where('project_id', $project)->where('queue', '<', $currentDish->queue)->get();
//                            $count = $currentDish->queue;
//                        }
//                        foreach ($dishList as $projectDish) {
//                            \DB::table('project_dish')->where('dish_id', $projectDish->dish_id)->update(['queue' => $count]);
//                            $count++;
//                        }
//                        \DB::table('project_dish')->where('dish_id', $currentDish->id)->update(['queue' => $count]);
//                    }
//                } else {
//                    $secondSearch = $this->search_key_val($postedVK, 'postponed_id', $published->vk_id, true);
//                    Log::info($secondSearch);
//                    if ($secondSearch && $published->published == 'postpone' && $published->post_type == 'dish') {
//                        $currentDish = \DB::table('project_dish')->where('dish_id', $published->object_id)->first();
//                        if ($currentDish) {
//                            if (is_null($currentDish->queue)) {
//                                $dishList = \DB::table('project_dish')->where('project_id', $project)->get();
//                                $count = 0;
//                            } else {
//                                $dishList = \DB::table('project_dish')->where('project_id', $project)->where('queue', '>', $currentDish->queue)->orderBy('queue')->get();
//                                $count = $currentDish->queue;
//                            }
//                            foreach ($dishList as $projectDish) {
//                                \DB::table('project_dish')->where('dish_id', $projectDish->dish_id)->update(['queue' => $count]);
//                                $count++;
//                            }
//                            \DB::table('project_dish')->where('dish_id', $currentDish->dish_id)->update(['queue' => $count]);
//                        }
//                        Post::where([['vk_id', $published->vk_id], ['project_id', $project]])->update(['vk_id' => $secondSearch['id']]);
//                        continue;
//                    }
//                    Post::where([['vk_id', $published->vk_id], ['project_id', $project]])->update(['vk_id' => null]);
//                }
//            }
//        }

//        $vkPostponed = [];
//        $response = [
//            'owner_id' => "-$project",
//            'filter' => 'postponed',
//            'count' => 50
//        ];
//        $postponedVK = $postExist->apiResult('get', $response)['items'];
//        foreach ($postponedVK as $postponed) {
//            $vkPostponed[$postponed['id']] = $postponed;
//        }
//        $busyTime = null;
//
//        foreach ($vkPostponed as $postponed) {
//            $busyTime[] = ['start' => date('Y-m-d H:i:s', strtotime("+2 hours +30 minutes", $postponed['date'])), 'end' => date('Y-m-d H:i:s', strtotime("+3 hours +30 minutes", $postponed['date']))];
//        }
//
        $deferredBusy = [];
        $deferredPosts = Post::where('project_id', $project)->whereNull('vk_id')->get();
        if ($deferredPosts) {
            if ($busyTime) {
                foreach ($deferredPosts as $deferred) {
                    foreach ($busyTime as $time) {
                        if ($deferred->publish_date >= $time['start'] && $deferred->publish_date <= $time['end']) {
                            $deferredBusy[$deferred->id] = 'В отложенных записях имеется пост в схожее время! Пожалуйста измените время публикации.';
                            continue;
                        }
                    }
                }
            }
        }
        return $deferredBusy;
    }

    public function checkStory($story, $content)
    {
        $errors = [];

        $storyDB = \DB::table('stories')->find($story);
        if (empty($storyDB->content)) {
            $errors += ['Нет контента'];
        }

        $timeZone = \DB::table('projects')->find($storyDB->project_id)->time_zone + 3;
//        $timeZone -= 2 * $timeZone;
        $projectDate = date('U', strtotime("$timeZone hours", time()));
        $storyDate = date('U', strtotime($storyDB->publish_date));
//        $errors += [$projectDate . ' | ' . $storyDate];
        if ($projectDate > $storyDate) {
            $errors += ['Сторис просрочен (по часовому поясу проекта)'];
        }

        if (empty($errors)) {
            $ffprobe = \FFMpeg\FFProbe::create();
            try {
                $duration = $ffprobe
                    ->format($content) // extracts file informations
                    ->get('duration');
                if ($duration > 16) {
                    $errors += ['Видео слишком длинное'];
                }
            } catch (\Exception $e) {
                $errors += ['Файл для отображения не найден'];
            }
        }

        \DB::table('stories')->where('id', $story)->update(['error' => empty($errors) ? 0 : 1]);
        if (!empty($errors)) {
            \DB::table('stories')->where('id', $story)->update(['to_publish' => 0]);
        }
        return $errors;
    }
}
