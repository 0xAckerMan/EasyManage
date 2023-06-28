<?php

namespace Inc\API;

class Login{

    public function register(){
        //$this->login();
    }

    function login(){
        if(isset($_POST['login-submit'])){

            $args = [
                'method'=>'POST',
                'body'=>[
                    'username'=>$_POST['email_username'],
                    'password'=>$_POST['password']
                ]
                ];

            $result = wp_remote_post('http://localhost/EasyManage/wp-json/jwt-auth/v1/token', $args);

            $token =(json_decode(wp_remote_retrieve_body($result)));
            echo '<pre>';
            // var_dump(($token->token));
                // var_dump($token->token);
                setcookie('token', $token->token, time() + (86400 * 30), '/', 'localhost');
            echo '</pre>';
        }
    }
}

// if(class_exists('Login')){
//     $login = new Login();
// }

?>