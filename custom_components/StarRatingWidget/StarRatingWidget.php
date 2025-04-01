<?php
/**
 * @author Романова Наталья <Natalochka_ne@mail.ru>
 * @copyright 2025 Романова Наталья | GitHub: Natalika-frontend
 * @licence html academy Use Only
 * @version 1.0
 * @warning Несанкционированное копирование запрещено!
 */

namespace app\custom_components\StarRatingWidget;

use yii\base\Widget;

class StarRatingWidget extends Widget
{
    // [!] АВТОРСКИЙ КОД [!]
    // Student: Романова Наталья
    // Course: Профессия "PHP-разработчик#1"
    // Task: модуль 2, задание module6-task2

    public float $rating; // значение рейтинга
    public int $maxRating

    public function init(): void
    {
        parent::init();
        ob_start();
    }
    public function run()
    {
        return $this->render('star-rating');
    }
}