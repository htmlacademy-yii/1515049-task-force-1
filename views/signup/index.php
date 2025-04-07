<?php

/** @var $model User */

/** @var $cities */

use app\models\User;
use yii\widgets\ActiveForm;

$this->title = 'Регистрация';

?>
<main class="container container--registration">
    <div class="center-block">
        <div class="registration-form regular-form">
            <?php
            $form = ActiveForm::begin() ?>
            <h3 class="head-main head-task">Регистрация нового пользователя</h3>
            <div class="form-group">
                <?= $form->field($model, 'name')
                    ->textInput(['id' => 'username']) ?>
            </div>

            <div class="half-wrapper">
                <div class="form-group">
                    <?= $form->field($model, 'email')
                        ->input('email', ['id' => 'email-user']) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'city')->dropDownList(
                        $cities,
                        [
                            'prompt' => 'Выберите город',
                            'id' => 'town-user',
                            'class' => 'form-control'
                        ]
                    ) ?>
                </div>
            </div>

            <div class="half-wrapper">
                <div class="form-group">
                    <?= $form->field($model, 'password')
                        ->passwordInput(['id' => 'password-user']) ?>
                </div>
            </div>

            <div class="half-wrapper">
                <div class="form-group">
                    <?= $form->field($model, 'password_repeat')
                        ->passwordInput(['id' => 'password-repeat-user']) ?>
                </div>
            </div>

            <div class="form-group">
                <?= $form->field($model, 'is_executor', [
                    'options' => ['tag' => false],
                    'template' => "<label class=\"control-label checkbox-label\">{input} {label}</label>\n{error}",
                ])->checkbox(['id' => 'response-user'], false) ?>
            </div>

            <input type="submit" class="button button--blue" value="Создать аккаунт">

            <?php ActiveForm::end() ?>
        </div>
    </div>
</main>
