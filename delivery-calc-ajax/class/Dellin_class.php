<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 02.12.2019
 * Time: 1:13
 */

class Dellin_class
{
    protected $token;
    protected $email;
    protected $pass;
    protected $sessionID;

    public function __construct($token,$email,$pass){
        $this->token = $token;
        $this->email = $email;
        $this->pass = $pass;
    }

    public function auth(){

        $response_obj = $this->curl_session(
            [
                "appkey"    => $this->token,
                "login"     => $this->email,
                "password"  => $this->pass
            ],
            "https://api.dellin.ru/v1/customers/login.json"
        );

        $this->sessionID = $response_obj->sessionID;

    }

    public function curl_session( $data, $url, $method = "POST" ){

        //массив данных для авторизации
        $auth_array = [ "appkey"    => $this->token ];

        $data_string = json_encode($auth_array+$data);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL               => $url,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_ENCODING          => "",
            CURLOPT_MAXREDIRS         => 10,
            CURLOPT_TIMEOUT           => 30,
            CURLOPT_POSTFIELDS        => $data_string,
            CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST     => $method,
            CURLOPT_HTTPHEADER        => array(
                "Content-Type: application/json",
                "Content-Length: " . strlen($data_string),
                "cache-control: no-cache"

            ),
        ));

        $response = json_decode(curl_exec($curl));
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

}