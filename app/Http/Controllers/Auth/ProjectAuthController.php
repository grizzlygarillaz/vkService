<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vk;
use App\Models\Project;
use App\Models\Photo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class ProjectAuthController extends Controller
{
    public function registerGroup(Request $request)
    {
        $accessTokens = Vk::registerToken($request->code);
        foreach ($accessTokens as $token) {
            Project::find($token['group_id'])->update(['access_token' => $token['access_token']]);
        }
        return back();
    }

    public function index(Request $request)
    {
        $error = [];
        if ($request->code) {
            $request->validate([
                'code' => 'required|string'
            ]);
            try {
                $accessTokens = Vk::registerToken($request->code, URL::current());
                foreach ($accessTokens as $token) {
                    Project::find($token['group_id'])->update(['access_token' => $token['access_token']]);
                }
            } catch (\Exception $e) {
                $error += [$e->getMessage()];
            }
        }
        return view('import_tags', ['page' => 'ИМПОРТ ПРОЕКТОВ'])->withErrors($error);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'csv' => 'required|mimes:csv,txt'
        ]);
        $errors = [];
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('tag_list')->truncate();
        \DB::table('tags')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $path = $request->file('csv')->getRealPath();
        $formatCounter = 0;
        $header = null;
        $data = array();
        if (($handle = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    foreach ($row as $key => $item) {
                        if (preg_match('/^project_\S*/', $item)) {
                            $formatCounter++;
                        }
                        if (preg_match('/^tag_/', $item)) {
                            $tag = '::' . str_replace('tag_', '', $item) . '::';
                            $row[$key] = \DB::table('tag_list')->insertGetId(['tag' => $tag]);
                        }
                    }
                    if ($formatCounter < 2) {
                        throw new \Exception('Ошибка чтения файла. Проверьте корректность формата полей.');
                    }
                    $header = $row;
                } else {
                    if (!empty($row[0])) {
                        $data[] = array_combine($header, $row);
                    }
                }
            }
            fclose($handle);
        }

        $groupIds = [];
        foreach ($data as $id => $value) {
            $projectData = [];
            foreach ($value as $key => $item)
                if (preg_match('/^project_\S*/', $key)) {
                    if (preg_match('/^project_group_id/', $key)) {
                        $projectData += ['id' => $item];
//                        $currentProject = $item;
                        array_push($groupIds, $item);
                    } elseif (preg_match('/^project_border/', $key)) {
                        if (!empty($item)) {
                            try {
                                $photoYandex = (new Photo)->downloadFromYandex($item, $value['project_group_id'], 'borders');
                                $projectData['border'] = $photoYandex['id'];
                            } catch (\Exception $e) {
                                $errors += ["Что-то не так с ссылкой на изображение : {$item} - проект {$value['project_group_id']}"];
                            }
                        }
                    } else {
                        $field = str_replace('project_', '', $key);
                        $projectData += [$field => $item];
                    }
                } else {
                    if (!empty($item)) {
                        \DB::table('tags')->insert([
                            'tagId' => $key,
                            'value' => $item,
                            'project_group' => $value['project_group_id']
                        ]);
                    }
                }
//            $currentCP = Project::find($projectData['id']);
//            if ($currentCP) {
//                $projectData += ['content_plan' => $currentCP->content_plan];
//            }
            $keys = array_keys($projectData);
            if (($key = array_search('id', $keys)) !== false) {
                unset($keys[$key]);
            }
            Project::upsert($projectData, ['id'], $keys);
        }
        foreach (Project::all() as $projectCheck) {
            if (!in_array($projectCheck->id, $groupIds)) {
                Project::find($projectCheck->id)->delete();
            }
        }
        return back()->withErrors($errors);
    }
}
