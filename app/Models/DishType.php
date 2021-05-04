<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishType extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "setting_dish_type";
    protected $fillable = ['*'];
}
