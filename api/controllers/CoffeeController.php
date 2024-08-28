<?php
namespace Api\Controllers;

use Api\Models\{Coffee, User};
use Api\Validator\RequestValidator;
use Api\Util\UtilClass as Util;

class CoffeeController {
    
    public $user;
    public $validator;
    public $coffee;

    public function __construct() {
        $this->coffee = new Coffee();
        $this->validator = new RequestValidator();
        $this->user = new User();
    }
    
    public function drinkCoffee($id): string {
        $this->validator->validateDrinkCoffee();
        if ($this->user->checkUserExist($id)) {
            $this->coffee->addDrink($id);
            $drinks_counter = 0;
            $drinks_counter = $this->user->getUserById($id);
            if (!empty($drinks_counter)) {
                $user_drinks = [
                    'id' => $id,
                    'name' => $drinks_counter['name'],
                    'email' => $drinks_counter['email'],
                    'drink_counter' => $drinks_counter['drink_counter'],
                ];
                Util::output($user_drinks, 200);
            }
        }
        Util::message(404, "User not found");
    }

    public function rankingDay() {
        $filter_date = $this->validator->validateRankingUsers();
        $ranking = $this->coffee->getRankingUsersPerDay($filter_date);
        if ($ranking) {
            Util::output($ranking);
        } else {
            Util::message(404, "User not found");
        }
    }

    public function rankingRange() {
        $filter_date = $this->validator->validateRankingRange();
        $ranking = $this->coffee->rankingUserDateRange($filter_date['date_start'], $filter_date['date_end']);
        if ($ranking) {
            Util::output($ranking);
        } else {
            Util::message(404, "there is no data in this filter range");
        }
    }

    public function rankingLastdays() {
        $days = $this->validator->validateRankingLastdays();
        $ranking = $this->coffee->getRankingUsersLastDays($days);
        if ($ranking) {
            Util::output($ranking);
        } else {
            Util::message(404, "User not found");
        }
    }

    public function userRecordHistory($id) {
        $this->validator->validateRecordHistory($id);
        $user = $this->user->getUserById($id);
        if ($user) {
            $history = $this->coffee->getUserRecordHistory($id);
            if ($history) {
                Util::output($history);
            } else {
                Util::message(404, "User not found");
            }
        } else {
            Util::message(404, "User not found");
        }
    }
}