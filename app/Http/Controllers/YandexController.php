<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Arhitector\Yandex\Client as YaClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class YandexController extends Controller
{
    protected static $checkFolders = ['/post', '/story'];

    public function getAccessToken($client = 'fa1adbfb573b4206b2c79307a997e4aa', $redirect = 'https://oauth.yandex.ru/verification_code')
    {
        $response = [
            'client_id' => $client,
            'redirect_uri' => $redirect
        ];
        $request = "https://oauth.yandex.ru/authorize?response_type=token&" . http_build_query($response);
        return redirect($request);
    }

    protected static function getContent($public_key, $path = '/', $type = null, $media = null)
    {
        $items = null;
        $disk = 'https://cloud-api.yandex.net/v1/disk/public/resources?public_key=' . urlencode($public_key);
        if ($path) {
            $disk .= '&path=' . urlencode($path);
        }
        $result = json_decode(file_get_contents($disk), true);
        foreach ($result['_embedded']['items'] as $item) {
            switch ($type) {
                case 'dir' :
                {
                    if ($item['type'] == $type) {
                        $items[] = $item;
                    }
                    break;
                }
                case 'file' :
                {
                    if (key_exists('media_type', $item) && $item['media_type'] == $media) {
                        $items[] = $item;
                    }
                    break;
                }
                case null :
                {
                    $items[] = $item;
                    break;
                }
            }
        }
        return $items;
    }

    public static function getAll ($public_key = 'https://yadi.sk/d/E4n7YJIdFOWI3g')
    {
        $files = [];
        $folders = self::getContent($public_key);
        foreach ($folders as $folder) {
            if (in_array($folder['path'], self::$checkFolders)) {
                $files[str_replace('/', '', $folder['path'])] = self::getContent($public_key, $folder['path']);
            }
        }
        return $files;
    }

    public static function installAllFiles ($public_key, $object, $project, $objectType) {
        $directories = self::getAll($public_key);
        foreach ($directories as $key => $directory) {
            $path = "/public/storage/$project/$objectType/$object/$key";
            foreach ($directory as $file) {
                if ($file['type'] == 'file' && in_array($file['media_type'], ['video', 'image'])) {
                    if(!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    Storage::put("$path/{$file['name']}", file_get_contents($file['file']));
                }
            }
        }
    }
}
