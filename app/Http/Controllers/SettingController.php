<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SettingController extends Controller
{
    public function tagIndex()
    {
        return view('settings.tags', ['objects' => $this->objectsOfProject, 'page' => 'НАСТРОЙКА ТЕГОВ']);
    }

    public function objectTags($object)
    {
        if (!key_exists($object, $this->objectsOfProject)) {
            throw new \Exception('Объект не найден!');
        }
        $tags = null;
        foreach (\DB::table('object_tag')->where('object', $object)->get() as $objectTags) {
            $tags[] = $objectTags;
        }
        if (is_null($tags)) {
            throw new \Exception('Не найдены поля для тегов. Попробуйте обновить данные.');
        }
        return view('settings.tag_info', ['tags' => $tags])->render();
    }

    public function updateTags($object, Request $request)
    {
        foreach ($this->objectsOfProject as $key => $value) {
            $columns = Schema::getColumnListing($key);
            $locked = $this->getLockedFields($key);
            foreach ($columns as $column) {
                if (in_array($column, $locked)) {
                    continue;
                }
                $data = [];
                if ($request->{"tag:$key:$column"}) {
                    $request->validate([
                        "tag:$key:$column" => 'regex:/^\S*$/'
                    ]);
                    if (
                        \DB::table('object_tag')
                            ->where('object', $key)->where('tag', $request->{"tag:$key:$column"})
                            ->get()->count() > 1
                        ||
                        \DB::table('tag_list')->where('tag', "::{$request->{"tag:$key:$column"}}::")->get()->first()
                    ) {
                        throw new \Exception("Теги должны быть уникальными! Тег \"{$request->{"tag:$key:$column"}}\" уже есть в панеле");
                    }
                    $data['tag'] = $request->{"tag:$key:$column"};
                }
                if ($request->{"description:$key:$column"}) {
                    $data['description'] = $request->{"description:$key:$column"};
                }
                if ($object == $key) {
                    $data['visible'] = $request->{"visible:$key:$column"} ? 1 : 0;
                }

                if (!empty($data)) {
                    \DB::table('object_tag')->where('object', $key)->where('field', $column)->update($data);
                }

            }
        }

        return $this->objectTags($object);
    }
}
