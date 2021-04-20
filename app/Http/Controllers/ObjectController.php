<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ObjectController extends Controller
{
    public function delete($table, $id)
    {
        $object = \DB::table($table)->find($id);
        if ($object) {
            foreach (Schema::getColumnListing($table) as $column) {
                if (preg_match('/^image_/', $column) && Photo::find($object->{$column})) {
                    Photo::find($object->{$column})->delete();
                }
            }
            \DB::table($table)->where('id', $id)->delete();
            return true;
        }
        throw new \Exception('Что-то пошло не так');
    }
}
