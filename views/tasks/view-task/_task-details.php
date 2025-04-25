<?php

/** @var Task $task */

/** @var ActiveDataProvider $responsesDataProvider */

/** @var $availableActions */

use app\customComponents\ActionButtonsWidget\ActionButtonsWidget;
use app\models\Response;
use app\models\Task;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?= Html::encode($task->title) ?></h3>
        <p class="price price--big"><?= Html::encode($task->budget) ?> ₽</p>
    </div>
    <p class="task-description"><?= Html::encode($task->description) ?></p>
    <?= ActionButtonsWidget::widget([
        'availableActions' => $availableActions,
        'currentUserId' => Yii::$app->user->id,
        'task' => $task,
    ]); ?>
    <div class="task-map">
        <img class="map" src="<?= Url::to('@web/img/map.png') ?>" width="725" height="346" alt="Новый арбат, 23, к. 1">
        <p class="map-address town">Москва</p>
        <p class="map-address">Новый арбат, 23, к. 1</p>
    </div>
    <h4 class="head-regular">Отклики на задание</h4>
    <?= $this->render('_response-list', ['responsesDataProvider' => $responsesDataProvider]) ?>
</div>
<div class="overlay"></div>
