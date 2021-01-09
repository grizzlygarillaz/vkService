<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vk;
use App\Traits\UploadTrait;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Buzz\Browser;
use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use Intervention\Image\Facades\Image;

class Photo extends Vk
{
    use HasFactory;
    use UploadTrait;

    protected $methodType = 'photos';

    public function getWallServer($group)
    {
        $response = [
            'group_id' => $group
        ];
        return $this->apiResult('getWallUploadServer', $response);
    }

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

    public function promoAlbum($promo)
    {
        return DB::table('promo')->where('id', '=', $promo)->get()->first()->album;
    }

    public function createAlbum($promo, $groupId = null)
    {
        $response = [
            'title' => $promo,
            'privacy_view' => 'only_me'
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
            $photoUrl = Storage::path("public/$photoLink");
            $photosId += [DB::table('photo')->insertGetId(['path' => $photoLink])];
            $type = last(explode('/', $photo->getClientMimeType()));
            $photosList += ["file$count" => curl_file_create($photoUrl, $photo->getClientMimeType(), "file{$count}name.$type")];
            if ($count == 5) {
                break;
            }
            $count++;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data",
            'Connection: Keep-Alive']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $photosList);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        usleep(100000);
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
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        return ['save_response' => $response, 'photoIds' => $photosId];
    }
}
