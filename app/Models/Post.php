<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Vk;

class Post extends Vk
{
    use HasFactory;
    protected $methodType = 'wall';

    /**
     * @param $groupId - id группы для поста
     * @param $message - сообщение поста
     * @param $dateTime - дата публикации
     * @param array $photoIds - идентификатор фото
     */
    public function sendDeferredPost ($groupId, $message, $dateTime, array $photoIds = [])
    {
        $groupId = -$groupId;
        $dateTime = date_create($dateTime);
        $dateTime = date_timestamp_get($dateTime);
        $media = '';
        $request = [
            "owner_id" => $groupId,
            "from_group" => 1,
            "message" => $message,
            "publish_date" => $dateTime
        ];

        foreach ($photoIds as $key => $id) {
            $media .= "photo{$groupId}_$id";
            if (array_key_exists($key + 1, $photoIds)) {
                $media .= ',';
            }
        }
        if ($media != '') {
            $request += ['attachments' => $media];
        }

        return $this->apiResult('post', $request);
    }

    public function sendPost ($groupId, $message, array $photoIds = [])
    {
        $groupId = -$groupId;
        $media = '';
        $request = [
            "owner_id" => $groupId,
            "from_group" => 1,
            "message" => $message
        ];
        foreach ($photoIds as $key => $id) {
            $media .= $id;
            if (array_key_exists($key + 1, $photoIds)) {
                $media .= ',';
            }
        }
        if ($media != '') {
            $request += ['attachments' => $media];
        }

        return $this->apiResult('post', $request);
    }

    public function getTags ()
    {
        return DB::table('tags')->get();
    }

    public function replaceTags($project, $message)
    {
        $project = DB::table('projects')->where('id', '=', $project)->get()->first();
        $tags = $this->getTags();
        foreach ($tags as $tag) {
            if (strstr($message, $tag->tag)) {
                $message = str_replace($tag->tag, $project->{$tag->property}, $message);
            }
        }
        return $message;
    }
}
