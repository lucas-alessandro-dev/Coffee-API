<?php

namespace Api\classes;

use Api\Util\UtilClass as Util;
use Api\Models\User;

class Auth {
    private $token;
    public $auth;
    public $user;

    public function __construct() {
        $this->token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        $this->auth = $this->decodeToken($this->token);
        $this->user = new User();
    }

    public function createToken($data) {
        $token = $this->generateToken($data['id'], $data['name'], $data['email']);
        $this->user->saveTokenUser($data['id'], $token);
    } 

    public function validateToken() {
        if ($this->auth === null || !isset($this->auth->exp)) {
            Util::message(401, "Invalid token");
        }

        $expirationTime = strtotime($this->auth->exp);
        $currentTime = time();

        if ($expirationTime > $currentTime) {
            return true;
        }
        Util::message(401, "token expired");
    }

    public function generateToken($id, $name, $email) {
        $auth = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'exp' => date('Y-m-d H:i:s', strtotime('+1 day')),
        ];
        $token = base64_encode(json_encode($auth));
        return $token;
    }

    public function getToken() {
        return $this->token;
    }

    public function getAuth() {
        return $this->auth;
    }

    public function decodeToken($token) {
        $auth = ($token !== null) ? json_decode(base64_decode($token)) : '';
        return $auth;
    }
}