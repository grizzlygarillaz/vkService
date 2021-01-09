<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        return view('projects', ['projects' => Project::all()->sortBy('name'), 'page' => 'Проекты']);
    }

    public function info(Request $request) {
        $info = '';
        $project = Project::where('id', $request->id)->get()->first();
        $info .= $this->makeInput('Название', 'name', $project->name);
        $info .= $this->makeInput('Город', 'city', $project->city);
        $info .= $this->makeInput('Группа', 'group_link', $project->group_link);
        $info .= $this->makeInput('Телефон', 'phone', $project->phone);
        return response()->json(['about' => $info]);
    }

    public function addPromo (Request $request) {
        $promo = DB::table('promo_project')->insert(['project_id' => $request->project, 'promo_id' => $request->promo]);
    }
}
