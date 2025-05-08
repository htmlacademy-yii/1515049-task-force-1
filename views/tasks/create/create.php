<?php

// [!] АВТОРСКИЙ КОД [!]
// Student: Романова Наталья
// Course: Профессия "PHP-разработчик#1"
// Task: модуль 2, задание module7-task2
// выполнено 24.04.2025

/* @var $model app\models\Task */

/* @var $categories app\models\Category[] */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Публикация нового задания';

?>
<main class="main-content main-content--center container">
    <style>
        .help-block:empty {
            display: none;
        }
    </style>
    <div class="add-task-form regular-form">
        <?php
        $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'errorOptions' => ['tag' => 'span', 'class' => 'help-block'],
            ],
        ]) ?>
        <h3 class="head-main head-main"><?= Html::encode($this->title) ?></h3>
        <div class="form-group">
            <?= $form->field($model, 'title')
                ->textInput(['id' => 'essence-work']) ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'description', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'rows' => 5
                ]
            ])->textarea(['id' => 'username']) ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'category_id', [
                'inputOptions' => [
                    'id' => 'town-user',
                    'class' => 'form-control'
                ]
            ])->dropDownList(
                ArrayHelper::map($categories, 'id', 'name'),
                ['prompt' => 'Выберите категорию']
            ) ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'location')->textInput([
                'id' => 'location',
                'class' => 'location-icon',
                'placeholder' => 'Город, улица, дом'
            ]) ?>

        </div>
        <div class="half-wrapper">
            <div class="form-group">
                <?= $form->field($model, 'budget', [
                    'inputOptions' => [
                        'id' => 'budget',
                        'class' => 'budget-icon'
                    ]
                ])->textInput() ?>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'ended_at', [
                    'inputOptions' => [
                        'id' => 'period-execution',
                        'class' => 'form-control',
                        'min' => date('Y-m-d')
                    ]
                ])->input('date') ?>
            </div>
        </div>
        <p class="form-label">Файлы</p>
        <div class="new-file">
            <?= $form->field($model, 'files[]', ['template' => '{input}'])->fileInput([
                'multiple' => true,
                'hidden' => true,
                'id' => 'file-upload',
            ]) ?>
            <label for="file-upload">Добавить новый файл</label>
        </div>
        <?= Html::submitInput('Опубликовать', ['class' => 'button button--blue']) ?>
        <?php
        ActiveForm::end() ?>
    </div>
</main>
