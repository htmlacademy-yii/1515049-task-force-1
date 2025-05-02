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
use yii\web\View;

$isCustomer = Yii::$app->user->id === $task->customer_id;
$userIsExecutorOfAnyResponse = Response::find()
    ->where(['executor_id' => Yii::$app->user->id, 'task_id' => $task->id])
    ->exists();

$latitude = $task->latitude;
$longitude = $task->longitude;

$this->registerJS(
    <<<JS
    ymaps.ready(init);
function init() {
    var myMap = new ymaps.Map('map', {
        center: [$latitude, $longitude],
        zoom: 16
    })

    myMap.controls.remove('trafficControl');
    myMap.controls.remove('searchControl');
    myMap.controls.remove('geolocationControl');
    myMap.controls.remove('typeSelector');
    myMap.controls.remove('fullscreenControl');
    myMap.controls.remove('rulerControl');

    var placemark = new ymaps.Placemark([$latitude, $longitude]);
        myMap.geoObjects.add(placemark);
}
JS,
    View::POS_READY
);
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
        <div id="map" style="width: 725px; height: 346px;"></div>
        <?php
        if ($task->city) : ?>
            <p class="map-address town"><?= Html::encode($task->city->name) ?></p>
            <p class="map-address"><?= Html::encode($task->latitude . ', ' . $task->longitude) ?></p>
        <?php
        endif; ?>
    </div>
    <?php
    if ($isCustomer || $userIsExecutorOfAnyResponse) : ?>
        <h4 class="head-regular">Отклики на задание</h4>
        <?= $this->render('_response-list', ['responsesDataProvider' => $responsesDataProvider]) ?>
    <?php
    endif; ?>
</div>
<div class="overlay"></div>
