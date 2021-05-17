<?php

namespace App\Http\Controllers;

use App\Models\Vk;
use Illuminate\Http\Request;

class StreamingVkController extends Controller
{
    public function auth ()
    {
        return Vk::request("streaming", "getServerUrl");
    }
}
