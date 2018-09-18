<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$router->get('/links', function () use ($router) {

    $results = DB::select("SELECT * FROM files");

    echo "<ul>";
    foreach ($results as $file) {
        echo "<li>";
        echo "<a href='" . $file->link . "' target='_blank'>" . $file->link . "</a>";
        echo "</li>";
    }
    echo "</ul>";

    echo "<br><br>";

    return "end";
});

$router->get('/process', function () use ($router) {

    $uri = "https://api.openload.co/1/file/listfolder";

    echo "Processing...";

    $client = new \GuzzleHttp\Client();
    $res = $client->request('GET', $uri, [
        'query' => [
            'login' => '42ebbe1d1f156789',
            'key' => 'RaUXY6FI',
            'folder' => '6161814',
        ],
    ]);

    $content = json_decode($res->getBody(), true);

    $fileCount = 0;
    foreach ($content['result']['files'] as $file) {

        // Find link
        $results = DB::select('select * from files where link = :link', ['link' => $file['link']]);

        // If not present, save it.
        if (count($results) <= 0) {
            DB::insert('insert into files (link) values (?)', [$file['link']]);
            echo "Inserted: " . $file['link'];
            echo "<br>";
        }

        $fileCount++;

    }

    echo "Files processed: " . $fileCount;

    echo "<br><br>";

    return "Done.";
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});
