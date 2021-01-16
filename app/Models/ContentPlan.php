<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentPlan extends Model
{
    protected $table = 'content_plan';
    use HasFactory;

    public function posts() {
        return $this->belongsToMany(Post::class);
    }
}
