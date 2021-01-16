<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vk extends Model
{
    use HasFactory;

    protected $access_token;
    public $version = "5.101";
    public $methodType = '';

    //c9496840600eb0c32b3b06a57c81d0569967d19d85d6b431e46d2f4adbe315d132681999a06a7a5289d68
    //16fc2eafc607b307c1c9d80fda2da20f977e5bff943f47a15d7e74b2d604d8c2a90eff355901795087f7d
    public function __construct($token = "16fc2eafc607b307c1c9d80fda2da20f977e5bff943f47a15d7e74b2d604d8c2a90eff355901795087f7d")
    {
        $this->access_token = $token;
    }

    public function apiResult ($method, array $responses = [])
    {
        $request = "https://api.vk.com/method/{$this->methodType}.$method/?access_token={$this->access_token}&v={$this->version}&" . http_build_query($responses);
        $requestResult = json_decode(file_get_contents($request), true);
        if (array_key_exists('response', $requestResult)) {
            return $requestResult['response'];
        } else {
            return $requestResult['error'];
        }
    }
}
