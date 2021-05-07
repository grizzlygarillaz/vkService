<?php

namespace App\Http\Controllers;

use App\Models\DishType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;

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

    public function dishTypeIndex(Request $request)
    {

        $data = [
            'dish_type_names' => DishType::select('name')->groupBy('name')->get()
        ];
        $dishTypes = [];
        foreach ($data['dish_type_names'] as $name) {
            $dishTypes[$name->name] = DishType::where('name', $name->name)->get();
        }
        $data += ['data' => $dishTypes];
        if ($request->ajax()) {
            return view('settings.dishType.list', $data)->render();
        }
        return view('settings.dishType.index', $data);
    }

    public function addDishType(Request $request)
    {
        $request->validate([
            'dish_type' => 'required|unique:App\Models\DishType,name'
        ]);
        if (in_array($request->dish_type, ['all', 'other'])) {
            throw new \Exception('Зарезервированное имя категории');
        }
        $dishType = new DishType;
        $dishType->name = $request->dish_type;
        $dishType->save();

        return $this->dishTypeIndex($request);
    }

    public function setFilter(Request $request)
    {
        $request->validate([
            'category' => 'required'
        ]);

        if (DishType::where('name', $request->category)->get()->count() < 1) {
            throw new \Exception('Категория не найдена');
        }

        if (empty(trim($request->filter, ' '))) {
            return false;
        }
        DishType::where('name', $request->category)->delete();

        foreach (explode(',', $request->filter) as $filter) {
            $emptyFilter = DishType::where('name', $request->category)->where('filter', null)->get()->count();
            if ($emptyFilter > 0) {
                DishType::where('name', $request->category)->where('filter', null)->update(['filter' => $filter]);
                continue;
            }
            $dishType = new DishType;
            $dishType->name = $request->category;
            $dishType->filter = $filter;
            $dishType->save();
        }
    }

    public function deleteFilter ($filter, Request $request)
    {
        DishType::where('name', $filter)->delete();
        return $this->dishTypeIndex($request);
    }
}
