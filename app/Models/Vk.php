<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    public function apiResult($method, array $responses = [])
    {
        $url = "https://api.vk.com/method/{$this->methodType}.$method";
//        $request = "https://api.vk.com/method/{$this->methodType}.$method/?access_token={$this->access_token}&v={$this->version}&" . http_build_query($responses);
        $data = ['access_token' => $this->access_token, 'v' => $this->version];
        $data += $responses;
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $requestResult = json_decode($result, true);
        if (array_key_exists('response', $requestResult)) {
            return $requestResult['response'];
        } else {
            Log::error('Vk error', $requestResult['error']);
            throw new \Exception('Ошибка ' . $requestResult['error']['error_code'] . ': ' . $requestResult['error']['error_msg'] );
        }
    }

    public function getURL($type, $method, array $responses = [])
    {
        return "https://api.vk.com/method/$type.$method/?access_token={$this->access_token}&v={$this->version}&" . http_build_query($responses);
    }

    public function customRequest($type, $method, array $responses = [])
    {
        $request = "https://api.vk.com/method/$type.$method/?access_token={$this->access_token}&v={$this->version}&" . http_build_query($responses);
        $requestResult = json_decode(file_get_contents($request), true);
        if (array_key_exists('response', $requestResult)) {
            return $requestResult['response'];
        } else {
            throw new \Exception('Ошибка ' . $requestResult['error']['error_code'] . ': ' . $requestResult['error']['error_msg']);
        }
    }

    public static function customStaticRequest($type, $method, array $responses = [])
    {
        return (new self())->customRequest($type, $method, $responses);
    }

    public function testRequest($type, $method, array $responses = [])
    {
        $request = "https://api.vk.com/method/$type.$method/?access_token={$this->access_token}&v={$this->version}&" . http_build_query($responses);
        return $request;
        $requestResult = json_decode(file_get_contents($request), true);
        if (array_key_exists('response', $requestResult)) {
            return $requestResult['response'];
        } else {
            throw new \Exception('Ошибка ' . $requestResult['error']['error_code'] . ': ' . $requestResult['error']['error_msg']);
        }
    }

    public static function groupsToken(array $groups)
    {
        if (empty($groups)) {
            return false;
        }
        $groups = implode(',', $groups);
        $scopes = implode(',', ['photos', 'manage', 'stories', 'wall', 'docs']);
        $response = [
            'scope' => $scopes,
            'group_ids' => $groups,
            'client_id' => '7713634',
            'redirect_uri' => back()->getTargetUrl(),
            'display' => 'page',
            'response_type' => 'code',
            'v' => (new self())->version
        ];
        $url = 'https://oauth.vk.com/authorize';
        header('Location: ' . $url . '/?' . http_build_query($response));
        die();
    }

    public static function registerToken($code, $redirectUrl)
    {
        $response = [
            'client_id' => '7713634',
            'client_secret' => 'ZjtkV5ghbPnvd8m15f6K',
            'redirect_uri' => $redirectUrl,
            'code' => $code
        ];
        $url = 'https://oauth.vk.com/access_token';
        $result = json_decode(file_get_contents($url . '/?' . http_build_query($response)), true);
        if (key_exists('groups', $result)) {
            return $result['groups'];
        } else {
            throw new \Exception($result['error']['error_msg']);
        }
    }
}
