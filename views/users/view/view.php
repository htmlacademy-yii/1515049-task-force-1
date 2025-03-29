<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

/** @var $user app\models\User */
/** @var $completedTasks int */
/** @var $failedTasks int */
/** @var $reviewsDataProvider yii\data\ActiveDataProvider */
?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main"><?= Html::encode($user->name) ?></h3>
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

        <div class="specialization-bio">
            <div class="specialization">
                <p class="head-info">Специализации</p>
                <ul class="special-list">
                    <?php foreach ($user->categories as $category): ?>
                        <li class="special-item">
                            <a href="<?= Url::to(['tasks/index', 'category_id' => $category->id]) ?>"
                               class="link link--regular">
                                <?= Html::encode($category->name) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="bio">
                <p class="head-info">Био</p>
                <p class="bio-info">
                    <span class="country-info">Не указано</span>,
                    <span class="town-info"><?= Html::encode($user->city_id ?? 'Не указано') ?></span>,
                    <span class="age-info"><?= $user->age ?? 'Не указано' ?> лет</span>
                </p>
            </div>
        </div>

        <h4 class="head-regular">Отзывы заказчиков</h4>
        <?= ListView::widget([
            'dataProvider' => $reviewsDataProvider,
            'itemView' => '_review',
            'layout' => "{items}\n{pager}",
            'options' => ['class' => 'responses-list'],
            'itemOptions' => ['class' => 'response-card'],
            'emptyText' => 'Пока нет отзывов',
            'pager' => [
                'options' => ['class' => 'pagination-list'],
                'linkOptions' => ['class' => 'pagination-link'],
            ]
        ]) ?>
    </div>

    <div class="right-column">
        <div class="right-card black">
            <h4 class="head-card">Статистика исполнителя</h4>
            <dl class="black-list">
                <dt>Всего заказов</dt>
                <dd><?= $completedTasks ?> выполнено, <?= $failedTasks ?> провалено</dd>
                <dt>Место в рейтинге</dt>
                <dd><?= $user->calculateExecutorRating() ?> место</dd>
                <dt>Дата регистрации</dt>
                <dd><?= Yii::$app->formatter->asDate($user->created_at, 'long') ?></dd>
                <dt>Статус</dt>
                <dd><?= $user->show_contacts ? 'Открыт для новых заказов' : 'Не принимает заказы' ?></dd>
            </dl>
        </div>

        <div class="right-card white">
            <h4 class="head-card">Контакты</h4>
            <ul class="enumeration-list">
                <li class="enumeration-item">
                    <a href="tel:<?= Html::encode($user->phone) ?>" class="link link--block link--phone"><?= Html::encode($user->phone) ?></a>
                </li>
                <li class="enumeration-item">
                    <a href="mailto:<?= Html::encode($user->email) ?>" class="link link--block link--email"><?= Html::encode($user->email) ?></a>
                </li>
                <?php if ($user->telegram): ?>
                    <li class="enumeration-item">
                        <a href="https://t.me/<?= Html::encode(ltrim($user->telegram, '@')) ?>" class="link link--block link--tg"><?= Html::encode($user->telegram) ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</main>
