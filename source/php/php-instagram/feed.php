<?php

require_once('access-token.php');

class Feed {

    //settings
    private $accessToken;
    private $user = 'fox_inn';
    private $path = '/php/php-instagram/';
    private $cacheInt;

    public function __construct($accessToken) {
        $this->accessToken = $accessToken;
        $this->cacheInt = $_GET['flush'] ? 0 : 120;
    }

    /*--------------------------------------------------------------------------------*/


    //these settings are named when $this->fetch() is called
    private $basePath = '';
    private $cache = '';

    public function fetch(){

        $this->basePath = $_SERVER['DOCUMENT_ROOT'] . $this->path;
        $this->cache = $this->basePath . 'cache/' . $this->user . '.json';
        $cacheTimer = $this->cacheInt * 60;

        if ( file_exists($this->cache) ) {
            if ( time() - filemtime($this->cache) > $cacheTimer ){
                //update
                $this->updateCache();
            }
        } else {
            //create
            $this->updateCache();
        }

        //return cached file
        $json = json_decode(file_get_contents($this->cache));
        return $json;

    }

    private function updateCache(){

        $url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $this->accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $result = curl_exec($ch);
        curl_close($ch);

        //update cache
        file_put_contents($this->cache, $result);

    }

}

$feed = new Feed($accessToken);
$photos = $feed->fetch();
echo json_encode($photos);

?>
