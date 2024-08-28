<?php
namespace Api\Validator;

use Api\Util\UtilClass as Util;
use Api\classes\Auth;

class RequestValidator {
    const METHODS = ['GET', 'POST', 'PUT', 'DELETE'];
    const ENDPOINTS = [
        'POST' => ['users', 'login'],
        'GET' => ['users', 'ranking-day', 'ranking-range', 'ranking-lastdays', 'record-history'],
        'PUT' => ['users'],
        'DELETE' => ['users']    
    ];

    public function uriValidator($request_uri): array {
        $this->validateMethod();
        $request_uri = explode('/', $request_uri);
        $uri = [];       
        $uri = [
            'method' => $_SERVER['REQUEST_METHOD'],
            'endpoint' => $request_uri[1],
            'id' => $request_uri[2] ?? null,
            'action' => $request_uri[3] ?? null
        ];

        if ($uri['id'] && !is_numeric($uri['id'])) {
            Util::message(400, "Invalid user ID");
            exit;
        }
    
        if ($uri['action'] !== null && $uri['action'] !== 'drink') {
            Util::message(400, "Invalid action");
            exit;
        }

        $this->validateRoutes($uri);
        
        return $uri;
    }

    public function validateCreatUser(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $requiredFields = ['name', 'email', 'password'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                Util::message(400, "Required data not provided");
            }
        }

        if (strlen($data['name']) < 4) {
            Util::message(400, "'Name' must have at least 4 characters");
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Util::message(400, "Invalid 'e-mail'");
        }
        if (strlen($data['password']) < 6) {
            Util::message(400, "'Password' must have at least 6 characters");
        }

        return $data;
    }

    public function validateGetUsers($id = null) {
        $auth = new Auth();
        $auth->validateToken($auth->getToken());
        if (is_numeric($id)) {
            return $id;
        }

        return null;
    }

    public function validateUpadateUser($id): array {
        $auth = new Auth();
        $auth->validateToken($auth->getToken());
        $data = json_decode(file_get_contents('php://input'), true);
        $data['id'] = $id;

        if (!is_numeric($data['id'])) {
            Util::message(400, "Required data not provided");
        }
        if (!isset($data['name']) && !isset($data['email']) && !isset($data['password'])) {
            Util::message(400, "Required data not provided");
        }
        if (isset($data['name']) && strlen($data['name']) < 4) {
            Util::message(400, "'Name' must have at least 4 characters");
        }
        if (isset($data['email']) && isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Util::message(400, "Invalid 'E-mail'");
        }
        if (isset($data['password']) && strlen($data['password']) < 6) {
            Util::message(400, "'Password' must have at least 6 characters");
        }

        return $data;
    }
    
    public function validateDelete($id): int {
        $auth = new Auth();
        $auth->validateToken($auth->getToken());
        $data = json_decode(file_get_contents('php://input'), true);
        $data['id'] = $id;

        if (!is_numeric($data['id'])) {
            Util::message(400, "Required data not provided");
        }

        return $data['id'];
    }

    public function validateDrinkCoffee(): int {
        $dataAuth = new Auth();
        $dataAuth->validateToken($dataAuth->getToken());
        if (!is_numeric($dataAuth->auth->id)) {
            Util::message(400, "User not provided");
        }

        return $dataAuth->auth->id;
    }

    public function validateMethod(): void {
        if (!in_array($_SERVER['REQUEST_METHOD'], self::METHODS)) {
            Util::message(405, 'Method Not Allowed');
        }
    }

    public function validateLogin(): array {
        $data = json_decode(file_get_contents('php://input'), true);

        $requiredFields = ['email', 'password'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                Util::message(400, "Required data not provided");
            }
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Util::message(400, "Invalid 'e-mail'");
        }
        if (strlen($data['password']) < 6) {
            Util::message(400, "'Password' must have at least 6 characters");
        }

        return $data;
    }

    public function validateRankingUsers() {
        $auth = new Auth();
        $auth->validateToken($auth->getToken());
        $data = json_decode(file_get_contents('php://input'), true);
        $data['date'] = $data['date'] ?? null;

        if (!$data['date']) {
            Util::message(400, "Required data not provided");
        }
        if ($data['date'] && !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['date'])) {
            Util::message(400, "Invalid date format");
        }

        return $data['date'];
    
    }

    public function validateRankingLastdays() {
        $auth = new Auth();
        $auth->validateToken($auth->getToken());
        $data = json_decode(file_get_contents('php://input'), true);
        $data['days'] = $data['days'] ?? null;

        if (!$data['days']) {
            Util::message(400, "Required data not provided");
        }
        if ($data['days'] && !is_numeric($data['days'])) {
            Util::message(400, "Invalid number of days");
        }

        return $data['days'];
    }

    public function validateRankingRange() {
        $auth = new Auth();
        $auth->validateToken($auth->getToken());
        $data = json_decode(file_get_contents('php://input'), true);
        $data['date_start'] = $data['date_start'] ?? null;
        $data['date_end'] = $data['date_end'] ?? null;     

        $dateStart = strtotime(str_replace('/', '-', $data['date_start']));
        $dateEnd = strtotime(str_replace('/', '-', $data['date_end']));

        if ($dateStart > $dateEnd) {
            Util::message(400, "Start date cannot be greater than end date");
        }

        if (!$data['date_start'] || !$data['date_end']) {
            Util::message(400, "Required data not provided");
        }
        if ($data['date_start'] && !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['date_start'])) {
            Util::message(400, "Invalid date format");
        }
        if ($data['date_end'] && !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['date_end'])) {
            Util::message(400, "Invalid date format");
        }

        return $data;
    }

    private function validateRoutes($uri) {
        if (!in_array($uri['endpoint'], self::ENDPOINTS[$uri['method']])) {
            Util::message(404, "Route not found");
        }
    }

    public function validateRecordHistory($id) {
        $auth = new Auth();
        $auth->validateToken($auth->getToken());
        if (!is_numeric($id)) {
            Util::message(400, "User not provided");
        }

        return $id;
    }
}