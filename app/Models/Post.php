<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Vk;
use App\Models\Project;
use App\Models\Photo;
use Illuminate\Support\Facades\Schema;

class Post extends Vk
{
    use HasFactory;

    public $methodType = 'wall';
    public $filleble = 'error';
    /**
     * @param $groupId - id группы для поста
     * @param $message - сообщение поста
     * @param $dateTime - дата публикации
     * @param array $photoIds - идентификатор фото
     */
    public function sendDeferredPost($groupId, $message, $dateTime, $mute, array $photoIds = [])
    {
        $groupId = -$groupId;
        $dateTime = strtotime($dateTime);
        $media = '';
        $request = [
            "owner_id" => $groupId,
            "from_group" => 1,
            "message" => $message,
            "publish_date" => $dateTime,
            "mute_notifications" => $mute
        ];

        if (!empty($photoIds)){
            foreach ($photoIds as $key => $id) {
                $media .= $id;
                if (array_key_exists($key + 1, $photoIds)) {
                    $media .= ',';
                }
            }
            if ($media != '') {
                $request += ['attachments' => $media];
            }
        }
        return $this->apiResult('post', $request);
    }

    public function checkSameDate ($post, $project)
    {

    }


    public function getAuthorEditor($post): array
    {
        return [
            'editor' => $post->edited_by ? (User::find($post->edited_by) ?: null) : null,
            'author' => $post->author ? (User::find($post->author) ?: null) : null
        ];
    }

    public function send($groupId, $message, array $photoIds = [])
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

    public function projects() {
        return $this->belongsToMany(Project::class);
    }

}
