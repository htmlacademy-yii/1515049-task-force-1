<?php

/** @var yii\web\View $this */

use yii\helpers\Url;

$this->title = 'Безопасность';
?>

<main class="main-content main-content--left container">
    <div class="left-menu left-menu--edit">
        <h3 class="head-main head-task">Настройки</h3>
        <ul class="side-menu-list">
            <li class="side-menu-item
            <?= Yii::$app->controller->action->id === 'settings' ? 'side-menu-item--active' : '' ?>">
                <a class="link link--nav" href="<?= Url::to(['account-settings/settings']) ?>">Мой профиль</a>
            </li>
            <li class="side-menu-item
            <?= Yii::$app->controller->action->id === 'security' ? 'side-menu-item--active' : '' ?>">
                <a class="link link--nav" href="<?= Url::to(['account-settings/security']) ?>">Безопасность</a>
            </li>
        </ul>
    </div>
    <div class="my-profile-form">
        <h3 class="head-main head-regular">Настройки безопасности</h3>
        <p>Здесь в будущем будут настройки пароля, 2FA и прочее.</p>
    </div>
</main>
