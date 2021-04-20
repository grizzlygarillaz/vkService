<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vk;
use Illuminate\Support\Facades\Storage;

class Stories extends Vk
{
    use HasFactory;

    public static function send($public, $story)
    {
        $content = Photo::find($story->content);
        $contentInfo = pathinfo($content->path);
        if ($content) {
            $response = [
                'add_to_news' => 1,
                'group_id' => $public
            ];
            $contentType = mime_content_type($content->path);
            if (preg_match('/^video/', $contentType)) {
                $method = 'getVideoUploadServer';
                $contentField = 'video_file';
            } elseif (preg_match('/^image/', $contentType)) {
                $method = 'getPhotoUploadServer';
                $contentField = 'file';
            }
            if (!isset($method)) {
                throw new \Exception('Ошибка загрузки файла.');
            }
            $uploadURL = (new self)->customRequest('stories', $method, $response)['upload_url'];
            $loadContent = [$contentField => curl_file_create(Storage::path($content->path), $contentInfo['extension'], $contentInfo['basename'])];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $loadContent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);
            if (key_exists('error', $response)) {
                throw new \Exception($response['error']);
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, (new self)->getURL('stories', 'save', ['upload_results' => $response['response']]));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_close($ch);
            \DB::table('stories')->where('id', $story->id)->update(['vk_id' => $response['response']['story']['id']]);
            return $response;
        }
        return false;
    }

    public function getAuthorEditor($story): array
    {
        return [
            'editor' => $story->edited_by ? (User::find($story->edited_by) ?: null) : null,
            'author' => $story->author ? (User::find($story->author) ?: null) : null
        ];
    }
}
