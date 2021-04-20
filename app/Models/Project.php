<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['access_token'];

    public $hidden = ['min_balance', 'prev_day_cost', 'prev_week_cost', 'prev_month_cost',
        'day_plane', 'critical_balance', 'notified', 'created_at', 'updated_at'];

    public function promos() {
        return $this->belongsToMany('Promo');
    }

    public function posts() {
        return $this->belongsToMany(Post::class);
    }
}
