<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function get()
    {
        $users = User::where('id', '!=' , Auth::user()->getAuthIdentifier())
            ->where('id', '!=', 2)->get()->sortBy('name');
        return view('employees.list', ['users' => $users, 'page' => 'СПИСОК СОТРУДНИКОВ']);
    }

    public function search(Request $request)
    {

    }

    public function employeeProjects($employee)
    {
        $projects = [];
        foreach (\DB::table('employee_project')->where('employee', $employee)->get() as $project) {
            array_push($projects, $project->project);
        }
        return view('employees.projects', [
            'employee' => $employee,
            'projects' => Project::all()->sortBy('name'),
            'employeeProjects' => $projects
        ]);
    }

    public function employeeProjectsSet($employee, Request $request)
    {
        \DB::table('employee_project')->where('employee', $employee)->delete();
        foreach ($request->projects as $project) {
            \DB::table('employee_project')->insert([
                'employee' => $employee,
                'project' => $project
            ]);
        }
        return back();
    }
}
