<?php

namespace Api\Controllers;

use Api\Util\UtilClass as Util;
use Api\Validator\RequestValidator;
use Api\Models\User;

class LoginController {
    private $validator;
    public $user;

    public function __construct() {
        $this->validator = new RequestValidator();
        $this->user = new User();
    }

    public function login($post): void {
        $data = $this->validator->validateLogin($post);
        $data_user = $this->user->getUserLogin($data);
        if (isset($data_user) && password_verify($data['password'], $data_user['password'])) {
            unset($data_user['password']);
            unset($data_user['created_at']);
            Util::output($data_user, 200);
        } else {
            Util::message(400, "Invalid 'e-mail' or 'password'");
        }
    }
}