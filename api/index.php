<?php
require '../vendor/autoload.php';

use Api\Validator\RequestValidator;
use Api\Controllers\{UserController, LoginController, CoffeeController};
use Api\Util\UtilClass as Util;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json; charset=UTF-8");
date_default_timezone_set('America/Sao_Paulo');

$validator = new RequestValidator();
$uri = $validator->uriValidator($_SERVER['REQUEST_URI']);

switch ($uri['method']) {
    case 'POST':
        if ($uri['endpoint'] == 'login') {
            $loginController = new LoginController();
            $loginController->login($_POST);
            break;
        } else if ($uri['endpoint'] == 'users' && $uri['action'] == 'drink') {
            $coffeeController = new CoffeeController();
            $coffeeController->drinkCoffee($uri['id']);
            break;
        } else if ($uri['endpoint'] == 'users') {
            $userController = new UserController();
            $userController->createUser($_POST);
            break;
        }
        break;
    case 'GET':
        if ($uri['endpoint'] == 'ranking-day') {
            $coffeeController = new CoffeeController();
            $coffeeController->rankingDay();
            break;
        } else if ($uri['endpoint'] == 'ranking-range') {
            $coffeeController = new CoffeeController();
            $coffeeController->rankingRange();
            break;
        } else if ($uri['endpoint'] == 'ranking-lastdays') {
            $coffeeController = new CoffeeController();
            $coffeeController->rankingLastdays();
            break;
        } else if ($uri['endpoint'] == 'record-history') {
            $coffeeController = new CoffeeController();
            $coffeeController->userRecordHistory($uri['id']);
            break;
        } else if ($uri['endpoint'] == 'users') {
            $userController = new UserController();
            $userController->getUsers($uri['id']);
            break;
        }
        break;
    case 'PUT':
        $userController = new UserController();
        $userController->updateUser($uri['id']);
        break;
    case 'DELETE':
        $userController = new UserController();
        $userController->deleteUser($uri['id']);
        break;
    default:
        Util::message(404, "Endpoint not found");
        break;
}  
