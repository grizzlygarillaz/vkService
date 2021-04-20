<?php
require "/home/c/cq90552/public_html/Classes/Ads/Ads.php";

$database = [
    "driver" => "mysql",
    "host" => "localhost",
    "dbname" => "cq90552_vkapp",
    "user" => "cq90552_vkapp",
    "password" => "1!QazSe$4"
];

$pdo = new PDO(
    "{$database['driver']}:host={$database['host']};dbname={$database['dbname']}",
    $database['user'],
    $database['password']
);
$ads = new \Classes\Ads\Ads('16fc2eafc607b307c1c9d80fda2da20f977e5bff943f47a15d7e74b2d604d8c2a90eff355901795087f7d');

$projects = $pdo->query("SELECT * FROM projects");
foreach ($projects as $project) {
    $stories = $pdo->prepare("SELECT * FROM stories WHERE publish_date = :date AND project_id = :project");
    $timeZone = $project['time_zone'];
//    $timeZone -= 2 * $timeZone;
    $date = date('Y-m-d H:i', strtotime("$timeZone hours", time()));
    $stories->bindParam(':date', $date);
    $stories->bindParam(':project', $project['id']);
    $stories->execute();
    foreach ($stories->fetchAll() as $story) {
        if ($story['to_publish'] && empty($story['vk_id']) && !$story['error']) {
            $content = $story['content'];
            $content = $pdo->query("SELECT * FROM photo WHERE id = $content")->fetchAll();
            if ($content) {
                $content = $content[0];
                $contentInfo = pathinfo('/home/c/cq90552/public/vkService2/storage/app/' . $content['path']);
                $response = [
                    'add_to_news' => 1,
                    'group_id' => $story['project_id']
                ];
                $contentType = mime_content_type('/home/c/cq90552/public/vkService2/storage/app/' . $content['path']);
                if (preg_match('/^video/', $contentType)) {
                    $method = 'getVideoUploadServer';
                    $contentField = 'video_file';
                } elseif (preg_match('/^image/', $contentType)) {
                    $method = 'getPhotoUploadServer';
                    $contentField = 'file';
                }
                if (!isset($method)) {
                    throw new \Exception('Ошибка загрузки файла.');
                }
                $uploadURL = $ads->apiResult($method, $response, 'stories')['response']['upload_url'];
                $loadContent = [$contentField => curl_file_create('/home/c/cq90552/public/vkService2/storage/app/' . $content['path'], $contentInfo['extension'], $contentInfo['basename'])];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $uploadURL);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"]);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $loadContent);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = json_decode(curl_exec($ch), true);
                curl_close($ch);
                if (key_exists('error', $response)) {
                    echo $uploadURL;
                    var_dump($loadContent);
                    var_dump($response);
                    var_dump($contentInfo);
                    var_dump($contentType);
                }
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ads->getURL('stories', 'save', ['upload_results' => $response['response']]));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_close($ch);
                $pdo->query("UPDATE stories SET vk_id = {$response['response']['story']['id']} WHERE id = {$story['id']}");
//                \DB::table('stories')->where('id', $story->id)->update(['vk_id' => $response['response']['story']['id']]);
//                return $response;
            }
        }
    }
    sleep(10);
}
return false;

