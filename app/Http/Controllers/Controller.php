<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Models\Project;
use App\Models\Tag;
use App\Http\Controllers\YandexController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $objectsOfProject = [
        'promo' => [
            'name' => 'Акции'
        ],
        'dish' => [
            'name' => 'Блюда',
            'locked' => ['id_dishType']
        ],
        'dishType' => [
            'name' => 'Категории блюд',
            'locked' => ['id_dishType']
        ]
    ];

    protected $urlFilter = ['vk.c', 'yadi.sk'];

    protected $lockedFields = ['parent_id', 'archive', 'id', 'storage', 'name_for_menu', 'img_url'];

    public function getObjects($project)
    {
        $objects = [];
        foreach ($this->objectsOfProject as $key => $value) {
            $objectIds = \DB::table("project_$key")->where('project_id', $project)->get();
            if ($objectIds) {
                foreach ($objectIds as $id) {
                    $objects[$key][] = \DB::table($key)->find($id->{$key . '_id'});
                }
            }
        }

        return $objects;
    }

    public function getLockedFields($object)
    {
        return array_merge($this->lockedFields, isset($this->objectsOfProject[$object]['locked'])
            ?
            $this->objectsOfProject[$object]['locked']
            : []);
    }
    public function replaceTags($project, $message, $object = null, $objectId = null)
    {
        $project = Project::find($project);
        foreach (DB::table('tag_list')->get() as $tag) {
            if (strstr($message, $tag->tag)) {
                $value = Tag::where([
                    ['tagId', '=', $tag->id],
                    ['project_group', '=', $project->id]
                ]);
                if ($value->count() > 0) {
                    $message = str_replace($tag->tag, $value->first()->value, $message);
                } else {
                    $message = str_replace($tag->tag, "!!!НЕТ ЗНАЧЕЧЕНИЯ ДЛЯ ТЕГА {$tag->tag}!!!", $message);
                }
            }
        }
        if (!empty($object) && key_exists($object, $this->objectsOfProject) && !empty($objectId)) {
            $tags = \DB::table('object_tag')
                ->where('object', $object)->where('visible', 1)
                ->get();
            foreach ($tags as $tag) {
                if (strstr($message, "::{$tag->tag}::")) {
                    $replace = DB::table($object)->find($objectId)->{$tag->field};
                    if (empty($replace)) {
                        $message = str_replace("::{$tag->tag}::", "!!!НЕТ ЗНАЧЕЧЕНИЯ ДЛЯ ТЕГА ::{$tag->tag}::!!!", $message);
                    } else {
                        if (filter_var($replace, FILTER_VALIDATE_URL)) {
                            $checkURL = true;
                            foreach ($this->urlFilter as $url) {
                                if (preg_match("/$url/", $replace)) {
                                    $checkURL = false;
                                    break;
                                }
                            }
                            if ($checkURL) {
                                if (preg_match('/\?\H\S*/', $replace)) {
                                    $replace .= '&utm_source=vk&utm_medium=content';
                                } else {
                                    $replace .= '?utm_source=vk&utm_medium=content';
                                }
                            }
                        }
                        if (preg_match('/price/', $tag->field)) {
                            $replace .= '₽';
                        }
                        $message = str_replace("::{$tag->tag}::", $replace, $message);
                    }
                }
            }
        }
        return $message;
    }

    public function createProjectTables($tableName)
    {
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
            });
        }
        if (!Schema::hasTable("project_$tableName")) {
            Schema::create("project_$tableName", function (Blueprint $table) use ($tableName) {
                $table->integer('project_id');
                $table->foreign('project_id')
                    ->references('id')->on('projects')->onDelete('cascade');
                $table->bigInteger("{$tableName}_id")->unsigned();
                $table->foreign("{$tableName}_id")
                    ->references('id')->on($tableName)->onDelete('cascade');
            });
        }
    }

    public function loadProjectCSV($filePath, $tableName, $project, $prefix)
    {
        $errors = [];
        if (!key_exists($prefix, $this->objectsOfProject)) {
            throw new \Exception('Объект не существует!');
        }
        $this->createProjectTables($tableName);
        $privateParams = ['parent_id', 'archive'];
        // Чтение csv файла
        $header = null;
        $data = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $prefCount = 0;
                    foreach ($row as $key => $item) {
                        $reg = "{$prefix}_";
                        if (preg_match("/^$reg/", $item)) {
                            $prefCount++;
                            $row[$key] = str_replace($reg, '', $item);
                        }
                        if (preg_match('/^id$/', $item)) {
                            $row[$key] = $item . '_' . $prefix;
                        }
                    }
                    $header = $row;
                    if ($prefCount < 2) {
                        throw new \Exception('С файлом что-то не так!! Не подходит для текущего объекта!');
                    }
                    $intersect = array_intersect($row, $privateParams);
                    sort($privateParams);
                    sort($intersect);
                    if ($intersect != $privateParams) {
                        throw new \Exception('С файлом что-то не так!! Нет обязательных полей!');
                    }
                } else {
                    if (!empty($row[0])) {
                        $data[] = array_combine($header, $row);
                    }
                }
            }
            fclose($handle);
        }

        $columns = Schema::getColumnListing($tableName);
        // Удаляем все строки проекта в текущем объекте
        $objects = \DB::table("project_$tableName")->where('project_id', $project)->get();
        foreach ($objects as $object) {
            $projectObject = \DB::table($tableName)->where('id', $object->{$tableName . '_id'});
            foreach ($projectObject->get() as $value) {
                foreach ($columns as $column) {
                    if (preg_match('/^image_/', $column) && $value->{$column} != null) {
                        $photo = \App\Models\Photo::find($value->{$column});
                        if ($photo) {
                            File::delete($photo->path);
                        }
                        \App\Models\Photo::where('id', $value->{$column})->delete();
                    }
                }
            }
            $projectObject->delete();
        }

        if (Storage::exists("/public/storage/$project/$tableName")) {
            Storage::deleteDirectory("/public/storage/$project/$tableName");
        }

        // Запись данных в БД
        foreach ($data as $type) {
            foreach ($type as $key => $value) {
                // Не загружать столбец, если его НЕТ У ВСЕХ
                if ($value == null) {
                    unset($type[$key]);
                    $key = null;
                }

                // Загрузка изображения из яндекса
                if ((preg_match('/https:\/\/yadi.sk/', $value) || preg_match('/https:\/\/disk.yandex/', $value)) && $key != 'storage') {
                    $photoYandex = null;
                    try {
                        $photoYandex = (new Photo)->downloadFromYandex($value, $project, $tableName);
                    } catch (\Exception $e) {
                        $errors += ["Что-то не так с ссылкой на изображение : $value
                        . {$this->objectsOfProject[$prefix]['name']} \"{$type['name']}\" {$e->getMessage()}"];
                    }
                    $type['image_' . $key] = ($photoYandex) ? $photoYandex['id'] : null;
                    unset($type[$key]);
                    $key = 'image_' . $key;
                }

                // Добавление новых столбцов в таблицу
                if (!is_null($key) && !in_array($key, $columns)) {
                    Schema::table($tableName, function ($table) use ($key) {
                        $table->text($key)->after('id')->nullable();
                    });
                    $columns += [$key];
                }
            }
            $insertId = \DB::table($tableName)->insertGetId($type);

            if (key_exists('storage', $type)) {
                YandexController::installAllFiles($type['storage'], $insertId, $project, $tableName);
            }
            \DB::table("project_$tableName")->insert([
                'project_id' => $project,
                ($tableName . '_id') => $insertId
            ]);
        }

        // Удаление пустых колонок из ДБ
        foreach ($columns as $column) {
            $fields = \DB::table($tableName)->select($column)->groupBy($column)->get();
            if ($fields->count() === 1 && is_null($fields->first()->{$column})) {
                Schema::table($tableName, function ($table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
        return $errors;
    }

    public function tags(Request $request, $object = null, $textarea = null)
    {
        if ($object) {
            $tags = null;
            if (!key_exists($object, $this->objectsOfProject)) {
                throw new \Exception('Недопустимый тип поста!');
            }
            $tag = \DB::table('object_tag')
                ->where('object', $object)->where('visible', 1)
                ->get();
            foreach ($tag as $item) {
                $tags[] = $item;
            }
            return view('layout.tags', ['textarea' => $request->textarea, 'objTags' => $tags])->render();
        }
        return view('layout.tags', ['textarea' => $request->textarea])->render();
    }

    public function objectTagList ($textarea, $object = null) {
        if ($object) {
            $tags = null;
            if (!key_exists($object, $this->objectsOfProject)) {
                throw new \Exception('Недопустимый тип поста!');
            }
            $tag = \DB::table('object_tag')
                ->where('object', $object)->where('visible', 1)
                ->get();
            foreach ($tag as $item) {
                $tags[] = $item;
            }
            return view('layout.tags', ['textarea' => $textarea, 'objTags' => $tags])->render();
        }
        return view('layout.tags', ['textarea' => $textarea])->render();
    }
}
