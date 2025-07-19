<?php
class Cache {
    private static $instance = null;
    private $cacheDir;
    
    private function __construct() {
        $this->cacheDir = __DIR__ . '/../cache/';
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Cache();
        }
        return self::$instance;
    }
    
    public function get($key) {
        $cacheFile = $this->cacheDir . md5($key) . '.json';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
            return json_decode(file_get_contents($cacheFile), true);
        }
        return null;
    }
    
    public function set($key, $data, $ttl = 3600) {
        $cacheFile = $this->cacheDir . md5($key) . '.json';
        file_put_contents($cacheFile, json_encode($data));
    }
    
    public function delete($key) {
        $cacheFile = $this->cacheDir . md5($key) . '.json';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }
    
    public function clear() {
        $files = glob($this->cacheDir . '*.json');
        foreach ($files as $file) {
            unlink($file);
        }
    }
}
?> 