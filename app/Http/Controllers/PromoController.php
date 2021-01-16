<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PromoRequest;
use App\Models\Promo;
use App\Models\Tag;
use App\Models\Photo;
use Illuminate\Support\Facades\DB;

class PromoController extends Controller
{
    public function saveLocked(PromoRequest $request)
    {
        $promo = new Promo;
        $promoName = $request->promo_name;
        $promo->name = $promoName;
        $photo = new Photo;
        if (!$this->checkPromo($promoName)) {
            $album = $photo->createAlbum($promoName);
            usleep(100000);
            $albumId = $album['id'];
        } else {
            $albumId = $this->checkPromo($promoName);
        }
        $params = $photo->saveToAlbum($promoName, $request->file('promo_images'), $albumId);
        $promo->album = $albumId;
        $start = strtotime($request->promoStart);
        $promo->start = date('Y-m-d H:i:s', $start);
        $end = strtotime($request->promoEnd);
        $promo->end = date('Y-m-d H:i:s', $end);
        $promo->layout = $request->promo_layout;
        $promo->locked = true;
        $promo->save();
        $promoId = $promo->id;
        $photoIds = $params['photoIds'];
        foreach ($photoIds as $param) {
            DB::table('promo_photo')->insert([
                'photo_id' => $param,
                'promo_id' => $promoId
            ]);
            usleep(5000);
        }
        return back();
    }

    public function saveIndividual($promo, $photo)
    {
        $promo = new Promo;
        $promo->name = $promo;
        $promo->photo = $photo;
        $promo->locked = true;
        $promo->save();
    }

    public function getPromoPhotos()
    {
        $promoPhotos = DB::table('promo_photo')->get()->unique('promo_id');
        $result = [];
        foreach ($promoPhotos as $ph) {
            $promo = Promo::where('id', $ph->promo_id)->get()->first()->name;
            $result += [$promo => []];
            $photos = DB::table('promo_photo')->where('promo_id', '=', $ph->promo_id)->get();
            foreach ($photos as $photo) {
                $result[$promo] += [DB::table('photo')->where('id', '=', $photo->photo_id)->get()->first()];
            }
        }
        return $result;
    }

    public function index()
    {
        $photos = $this->getPromoPhotos();
        return view('promo.basic', ['promos' => Promo::all(), 'photos' => $photos, 'page' => 'ПРОМО-АКЦИИ', 'tags' => Tag::all()]);
    }

    public function checkPromo($promo)
    {
        $photo = new Photo;
        $promo = strtolower($promo);
        $albums = $photo->getAlbums()['items'];
        foreach ($albums as $album) {
            $title = strtolower($album['title']);
            if ($title == $promo) {
                return $album['id'];
            }
        }
        return false;
    }

    public function getCurrent ($promo, $project) {
        $currentPromo = Promo::where('id', $promo)->first();
        $start = strtotime($currentPromo->start);
        $promoStart = date('d.m.Y H:i', $start);
        $end = strtotime($currentPromo->end);
        $promoEnd = date('d.m.Y H:i', $end);
        $promoText = $this->replaceTags($project, $currentPromo->layout);
        $promoImg = null;
        $photo = \DB::table('promo_photo')->where('promo_id', $promo)->first()->photo_id;
        if ($photo) {
            $promoImg = '/storage/' . \DB::table('photo')->where('id', $photo)->first()->path;
        }
        return response()->json([
            'promo' => $currentPromo,
            'start' => $promoStart,
            'end' => $promoEnd,
            'text' => $promoText,
            'img' => $promoImg]);
    }

    public function availablePromo(Request $request)
    {
        $promoList = '';
        $promos = Promo::where('locked', true)->get();
        foreach ($promos as $promo) {
            if (DB::table('promo_project')
                ->where('project_id', $request->id)
                ->where('promo_id', $promo->id)->get()->count() < 1) {
                $promoList .= <<<EOT
<div class="form-check text-start">
  <input class="form-check-input" type="checkbox" value="{$promo->id}" id="promo{$promo->id}">
  <label class="form-check-label" for="promo{$promo->id}">
    $promo->name
  </label>
</div>
EOT;
            }
        }
        return response()->json(['available_promo' => $promoList]);
    }
}
