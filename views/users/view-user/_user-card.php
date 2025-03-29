<?php

/** @var $user */

use yii\helpers\Html;
use yii\helpers\Url; ?>

<div class="user-card">
    <div class="photo-rate">
        <img class="card-photo" src="<?= Url::to('@web/img/' . $user->avatar) ?>" width="191" height="190" alt="Фото пользователя">
        <div class="card-rate">
            <div class="stars-rating big">
                <?= str_repeat('<span class="fill-star">&nbsp;</span>', round($user->executor_rating)) ?>
                <?= str_repeat('<span>&nbsp;</span>', 5 - round($user->executor_rating)) ?>
            </div>
            <span class="current-rate"><?= number_format($user->executor_rating, 2) ?></span>
        </div>
    </div>
    <p class="user-description">
        <?= Html::encode($user->info ?? 'Пользователь не указал информацию о себе') ?>
    </p>
</div>
