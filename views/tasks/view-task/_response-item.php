<?php
/** @var $task */

/** @var Response $model */

use app\models\Response;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="response-card">
    <img class="customer-photo" src="<?= Url::to('@web/img/' . $model->executor->avatar) ?>" width="146" height="156"
         alt="Фото заказчиков">
    <div class="feedback-wrapper">
        <a href="<?= Url::to(['users/view', 'id' => $model->executor->id]) ?>" class="link link--block link--big"><?= Html::encode(
                $model->executor->name
            ) ?></a>
        <div class="response-wrapper">
            <div class="stars-rating small">
                <?= str_repeat('<span class="fill-star">&nbsp;</span>', round($model->executor->executorRating)) ?>
                <?= str_repeat('<span>&nbsp;</span>', 5 - round($model->executor->executorRating)) ?>
            </div>
            <p class="reviews"><?= $model->executor->reviews_count ?? 0 ?></p>
        </div>
        <p class="response-message">
            <?= Html::encode($model->comment) ?>
        </p>

    </div>
    <div class="feedback-wrapper">
        <p class="info-text"><span class="current-time"><?= Yii::$app->formatter->asRelativeTime($model->created_at) ?></p>
        <p class="price price--small">3700 ₽</p>
    </div>
    <div class="button-popup">
        <a href="#" class="button button--blue button--small">Принять</a>
        <a href="#" class="button button--orange button--small">Отказать</a>
    </div>
</div>
