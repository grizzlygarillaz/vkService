<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vk;
use Illuminate\Support\Facades\DB;

class Photo extends Vk
{
    use HasFactory;
    protected $methodType = 'photos';

    public function getWallServer ($group) {
        $response = [
            'group_id' => $group
        ];
        return $this->apiResult('getWallUploadServer', $response);
    }

    public function get ($album, $owner = '522446651') {
        $response = [
            'owner_id' => $owner,
            'album_id' => $album
        ];
        return $this->apiResult('get', $response);
    }

    public function promoAlbum ($promo) {
        return DB::table('promo')->where('id', '=', $promo)->get()->first()->album;
    }
}
