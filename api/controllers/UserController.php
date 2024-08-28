<?php
namespace Api\Controllers;

use Api\Models\User;
use Api\Util\UtilClass as Util;
use Api\Validator\RequestValidator;
use Api\classes\Auth;

class UserController { 
    
    public $user;
    public $validator;
    public $auth;

    public function __construct() {
        $this->user = new User();
        $this->validator = new RequestValidator();
    }

    public function createUser($post) {
        $data = $this->validator->validateCreatUser($post);
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $this->user->checkNameOrEmailExists($name, $email);
        $data['id'] = $this->user->saveUsers($data);
        $this->auth = new Auth();
        $this->auth->createToken($data);
        Util::message(201, "User created successfully", 'success'); 
    }

    public function updateUser($id) {
        $data = $this->validator->validateUpadateUser($id);
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        if ($name || $email) {
            $this->user->checkNameOrEmailExists($name, $email);
        }
        $this->user->updateUser($data);
    }

    public function getUsers($id = null) {
        $data['user_id'] = $this->validator->validateGetUsers($id);
        $users = isset($data['user_id']) ? $this->user->getUserById($data['user_id']) : $this->user->getAllUsers();

        if ($users) {
            Util::output($users);
        } else {
            Util::message(404, "User not found");
        }
    }

    public function deleteUser($id) {
        $data = $this->validator->validateDelete($id);
        $this->user->deleteUser($data);
    }
}