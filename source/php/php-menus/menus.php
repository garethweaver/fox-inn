<?php

class Menus {

    //these settings are named when $this->fetch() is called
    private $basePath = '';
    private $path = '/php/php-menus/';
    private $cache = '';
    private $cacheInt;
    private $menuPath = '/assets/pdf';

    public function __construct() {
        $this->cacheInt = $_GET['flush'] ? 0 : 10;
    }

    public function fetch(){
        $this->basePath = $_SERVER['DOCUMENT_ROOT'] . $this->path;
        $this->cache = $this->basePath . 'cache/menus.json';
        $cacheTimer = $this->cacheInt * 60;

        if (file_exists($this->cache)) {
            if (time() - filemtime($this->cache) > $cacheTimer){
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
        $files = array();
        $dir = $_SERVER['DOCUMENT_ROOT'] . $this->menuPath;

        if(is_dir($dir)){
            if($dh = opendir($dir)){
                while(($file = readdir($dh)) != false){

                    if($file == "." or $file == ".."){
                        // nothing
                    } else if (strrpos($file, '.pdf') > 0) {
                        $fileData = array(
                            'file_name' => $file,
                            'title' => str_replace('-', ' ', pathinfo($file, PATHINFO_FILENAME)),
                            'url' => $this->menuPath . '/' . $file,
                            'file_size' => filesize($dir . '/' . $file)
                        );

                        if (file_exists($dir . '/' . pathinfo($file, PATHINFO_FILENAME) . '.jpg')) {
                            $fileData['image_url'] = $this->menuPath . '/' . pathinfo($file, PATHINFO_FILENAME) . '.jpg';
                        }

                        array_push($files, $fileData);
                    }
                }
            }
        }

        //update cache
        file_put_contents($this->cache, json_encode($files));
    }

}

$getMenus = new Menus();
$menus = $getMenus->fetch();
echo json_encode($menus);

?>
