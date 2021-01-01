<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;
    private $projects;
    private $promo;

    public function __construct()
    {
        $this->projects = DB::table('projects')->get();
        $this->promo = DB::table('promo')->get();
    }

    public function getProjects() {
        return $this->projects;
    }

    public function getPromos() {
        return $this->promo;
    }
}
