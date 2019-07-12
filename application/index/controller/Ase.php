<?php
namespace app\index\controller;
class Ase{
    protected $method;
    protected $secret_key;
    protected $iv;
    protected $options;
    public function __construct($key = 'encrypt', $method = 'AES-256-CBC', $options = 0){
        // key是必须要设置的
        $this->secret_key = substr(md5($key),0,16);

        $this->method = $method;

        $length = openssl_cipher_iv_length($method);

        $this->iv = $this->getRandomString($length);

        $this->options = $options;
    }
    private function getRandomString($length){
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_repeat($pool, 5), 0, $length);
    }
    public function encrypt($data){
        $json = json_encode($data);
        $encrypt = openssl_encrypt($json, $this->method, $this->secret_key, $this->options, $this->iv);
        return base64_encode($encrypt);
    }
    public function decrypt($data)
    {
        $base64 = base64_decode($data);
        $decrypt = openssl_decrypt($base64, $this->method, $this->secret_key, $this->options, $this->iv);
        return json_decode($decrypt,true);
    }
}
?>