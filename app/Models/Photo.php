<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vk;
use App\Traits\UploadTrait;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Buzz\Browser;
use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use Intervention\Image\Facades\Image;

class Photo extends Vk
{
    use HasFactory;
    use UploadTrait;

    protected $table = 'photo';
    public $methodType = 'photos';

    public function get($album, $owner = '522446651')
    {
        $response = [
            'owner_id' => $owner,
            'album_id' => $album
        ];
        return $this->apiResult('get', $response);
    }

    public function getAlbums($owner = '522446651')
    {
        $response = [
            'owner_id' => $owner
        ];
        return $this->apiResult('getAlbums', $response);
    }

    public function deletePhoto(int $id) {
        if (DB::table('photo')->find($id)) {
            $photo = DB::table('photo')->find($id)->path;
            if (file_exists($photo)) {
                File::delete($photo);
            }
            if (DB::table('photo')->find($id)->preview) {
                File::delete(DB::table('photo')->find($id)->preview);
            }
        }
        DB::table('photo')->where('id', $id)->delete();
    }

    public function promoAlbum($promo)
    {
        return DB::table('promo')->where('id', '=', $promo)->get()->first()->album;
    }

    public function createAlbum($promo, $groupId = null, $privacy = 'only_me')
    {
        $response = [
            'title' => $promo,
            'privacy_view' => $privacy
        ];
        if (!is_null($groupId)) {
            $response += ['group_id' => $groupId];
        }
        return $this->apiResult('createAlbum', $response);
    }

    public function getUploadServer($albumId, $groupId = null)
    {
        $response = [
            'album_id' => $albumId,
        ];
        if (!is_null($groupId)) {
            $response += ['group_id' => $groupId];
        }
        return $this->apiResult('getUploadServer', $response);
    }

    public static function downloadYandex($link, $project = null, $folder = null) {
        return (new self())->downloadFromYandex($link, $project, $folder);
    }

    public function downloadFromYandex($link, $project = null, $folder = null)
    {
        $url = 'https://cloud-api.yandex.net/v1/disk/public/resources/download?public_key=' . urlencode($link);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        if (!property_exists($response, 'href')) {
            throw new \Exception('С ссылкой что-то не так. Проверьте ссылку на файл.');
        }
        parse_str(parse_url($response->href)['query'], $params);
        if (!is_null($folder)) {
            $path = "public/$folder";
        } else {
            $path = "public/default";
        }
        if (!is_null($project)) {
            $path .= "/$project";
        }
        if(!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }
        $dir = $path;
        $filename = uniqid('yandex_');
        $path = $path . '/' . $filename . '.' . pathinfo($params['filename'])['extension'];
        Storage::put($path, file_get_contents($response->href));
        $result = [
            'path' => $path
        ];
        if (preg_match('/^image\//', mime_content_type($path)) || preg_match('/^video\//', mime_content_type($path))) {
            $photoInfo = ['path' => $path, 'link' => $link];
            if (preg_match('/^video\//', mime_content_type($path))) {
                $photoInfo += ['preview' => self::makeGif($path, $dir, $filename)];
            }
            $result += ['id' => DB::table('photo')->insertGetId($photoInfo)];
        } else {
            throw new \Exception('Файл не является медиа-контентом. Проверьте ссылку на файл.');
        }
        return $result;
    }

    /**
     * @param $photo path to base photo image
     * @param $border path to border
     * @param string $prefix prefix of output filename
     */
    public static function makeBorder ($photo, $border, $prefix = 'bordered') {
        $img = Image::make($photo);
        if ($img->width() != $img->height()) {
            throw new \Exception('Изображение не подходящего разрешения для вставки виньетки. Необходимое соотношение сторон 1:1.');
        }
        $path = "public/temp/{$prefix}_" . date("U", time()) . ".jpeg";
        $img->insert(Image::make($border)->resize($img->width(), $img->height()), 'bottom-right')->save($path);
        return $path;
    }

    public static function makeGif ($video, $path, $filename) {
        $videoPath = $video;

// The gif will have the same dimension. You can change that of course if needed.

        $gifPath = "$path/$filename" . ".gif";
// Transform
        $ffmpeg = \FFMpeg\FFMpeg::create();
        $ffmpegVideo = $ffmpeg->open($videoPath);
        $ffmpegVideo->gif(\FFMpeg\Coordinate\TimeCode::fromSeconds(0), new \FFMpeg\Coordinate\Dimension(240, 420), 5)->save($gifPath);
        return $gifPath;
    }

    public function toAlbumFromYandex($photoPaths, $albumId, $groupId) {
        $server = $this->getUploadServer($albumId, $groupId);
        $photoCount = 1;
        $photos = [];
        $url = $server['upload_url'];
        foreach ($photoPaths as $key => $path) {
            $path_parts = pathinfo($path['path']);
            $type = $path_parts['extension'];
            $photos += ["file$photoCount" => curl_file_create(Storage::path($path['path']), $type, $path['id']. $path_parts['basename'])];
            $photoCount++;
        }
        /**
         * Do refactor
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data",
            'Connection: Keep-Alive']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $photos);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        usleep(1000);
        $responseParam = [
            'album_id' => $albumId,
            'group_id' => $groupId,
            'server' => $response->server,
            'photos_list' => $response->photos_list,
            'hash' => $response->hash,
            'access_token' => '16fc2eafc607b307c1c9d80fda2da20f977e5bff943f47a15d7e74b2d604d8c2a90eff355901795087f7d',
            'v' => '5.101'
        ];
//        $responses = new Photo;
//        $responses = $responses->apiResult('save', $responseParam);
        $ch = curl_init();
        $url = 'https://api.vk.com/method/photos.save';
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data",
            'Connection: Keep-Alive']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $responseParam);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if (property_exists(json_decode($result), 'response')) {
            $responses = json_decode($result)->response;
        } else {
            throw new \Exception(json_decode($result)->error->error_msg);
        }
        curl_close($ch);
        foreach ($responses as $key => $response) {
            DB::table('photo')
                ->where('id', $photoPaths[$key]['id'])
                ->update([
                    'vk_id' => $response->id,
                    'album' => $response->album_id,
                    'owner' => $response->owner_id
                ]);
            usleep(100);
        }
        return $photoPaths;
    }

    public function saveToAlbum($promo, $photos, $albumId, $groupId = null)
    {
        $server = $this->getUploadServer($albumId, $groupId);
        usleep(100000);
        $url = $server['upload_url'];
        $photosList = [];
        $photosId = [];
        $count = 1;
        foreach ($photos as $photo) {
            $photoLink = $this->upload($photo, $promo);
            $photosId[] = DB::table('photo')->insertGetId(['path' => $photoLink]);
            usleep(50000);
            $type = last(explode('/', $photo->getClientMimeType()));
            $photosList += ["file$count" => curl_file_create(Storage::path("public/$photoLink"), $photo->getClientMimeType(), "file{$count}name.$type")];
            if ($count == 5) {
                break;
            }
            $count++;
        }

        /**
         * Do refactor
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data",
            'Connection: Keep-Alive']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $photosList);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        usleep(1000);
        $responseParam = [
            'album_id' => $albumId,
            'server' => $response->server,
            'photos_list' => $response->photos_list,
            'hash' => $response->hash,
            'access_token' => '16fc2eafc607b307c1c9d80fda2da20f977e5bff943f47a15d7e74b2d604d8c2a90eff355901795087f7d',
            'v' => '5.101'
        ];

        $ch = curl_init();
        $url = 'https://api.vk.com/method/photos.save';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data",
            'Connection: Keep-Alive']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $responseParam);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch))->response;
        curl_close($ch);
        foreach ($photosId as $key => $item) {
            DB::table('photo')
                ->where('id', $item)
                ->update([
                    'vk_id' => $response[$key]->id,
                    'album' => $response[$key]->album_id,
                    'owner' => $response[$key]->owner_id
                ]);
            usleep(5000);
        }

        return ['photoIds' => $photosId];
    }

    public function getWallServer (int $groupId, $image)
    {
        $wallServer = $this->customRequest('photos', 'getWallUploadServer', ['group_id' => $groupId])['upload_url'];
        $path_parts = pathinfo($image);
        $type = $path_parts['extension'];
        $photo = ["photo" => curl_file_create(Storage::path($image), $type, $path_parts['basename'])];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $wallServer);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $photo);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        usleep(1000);
        return $response;
    }

    public function saveWallPhoto ($groupId, $photo)
    {
        $response = [
            'group_id' => $groupId
        ];
        $response += $this->getWallServer($groupId, $photo);
        return $this->apiResult('saveWallPhoto', $response);
    }

    public function getPollPhoto ($groupId, $image) {
        $pollServer = $this->customRequest('polls', 'getPhotoUploadServer', ['group_id' => $groupId])['upload_url'];
        $path_parts = pathinfo($image);
        $type = $path_parts['extension'];
        $photo = ["photo" => curl_file_create(Storage::path($image), $type, $path_parts['basename'])];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pollServer);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $photo);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        Log::info($response);
        usleep(1000);
        return Vk::customStaticRequest('polls', 'savePhoto', $response);
    }

    public function getVideoServer ($groupId, $video)
    {
        $response = [
            'name' => uniqid("{$groupId}_"),
            'group_id' => $groupId,
            'repeat' => 1
        ];
        $videoServer = $this->customRequest('video', 'save', $response)['upload_url'];
        $path_parts = pathinfo($video);
        $type = $path_parts['extension'];
        $file = ["video_file" => curl_file_create(Storage::path($video), $type, $path_parts['basename'])];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $videoServer);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        usleep(1000);
        return $response;
    }
}
