<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

trait PostErrors
{
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

    public function checkPostExist($project) {
        $vkPostponed = [];
        $response = [
            'owner_id' => "-$project",
            'filter' => 'postponed',
            'count' => 50
        ];
        $postExist = new Post;
        $postponedVK = $postExist->apiResult('get', $response)['items'];
        foreach ($postponedVK as $postponed) {
            $vkPostponed[$postponed['id']] = $postponed;
        }

        $publishedPosts = Post::where('project_id', $project)
            ->where('publish_date', '>', date('Y-m-d H:i', strtotime("+3 hours", time())))
            ->whereNotNull('vk_id')->get();
        Log::info('postponed  ' . date('Y-m-d H:i:s', strtotime("+3 hours", time())), $publishedPosts->toArray());
        foreach ($publishedPosts as $published) {
            if (!key_exists($published->vk_id, $vkPostponed)) {
                Post::where([['vk_id', $published->vk_id], ['project_id', $project]])->update(['vk_id' => null]);
            }
        }

        $busyTime = null;
        $projectInfo = Project::find($project);
        foreach ($vkPostponed as $postponed) {
            $busyTime[] = ['start' => date('Y-m-d H:i:s', strtotime("+2 hours +30 minutes", $postponed['date'])), 'end' => date('Y-m-d H:i:s', strtotime("+3 hours +30 minutes", $postponed['date']))];
        }

        $deferredBusy = [];
        $deferredPosts = Post::where('project_id', $project)->whereNull('vk_id')->get();
        if ($deferredPosts) {
            foreach ($deferredPosts as $deferred) {
                if ($busyTime) {
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

    public function checkStory ($story, $content) {
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
