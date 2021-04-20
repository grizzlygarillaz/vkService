<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterEmployeeController extends Controller
{
    protected $roles = ['manager', 'admin'];

    public function create()
    {
        return view('auth.register', ['roles' => $this->roles, 'page' => 'ДОБАВИТЬ ПОЛЬЗОВАТЕЛЯ']);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|string|max:55'
        ]);
        if (!in_array($request->role, $this->roles)) {
            throw new \Exception('Роль не существует');
        }
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);
        return back();
    }
}
