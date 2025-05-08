<?php

namespace app\controllers;

use app\logic\Actions\CreateTaskAction;
use app\models\City;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

final class TaskCreationController extends SecuredController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    throw new ForbiddenHttpException('Только заказчики могут создавать задания');
                },
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isRoleCustomer();
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     *
     * return string|yii\web\Response
     */
    public function actions(): array
    {
        return [
            'create' => [
                'class' => CreateTaskAction::class,
            ],
        ];
    }

    public function actionCityList($term = null): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $cities = City::find()
            ->select(['id', 'name', 'latitude', 'longitude'])
            ->where(['like', 'name', $term])
            ->limit(10)
            ->all();

        return array_map(function ($city) {
            return [
                'id' => $city->id,
                'label' => $city->name,
                'value' => $city->name,
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
            ];
        }, $cities);
    }

    protected function renderCreateForm($model, $categories, $cities): string
    {
        return $this->render('@app/views/tasks/create/create', [
            'model' => $model,
            'categories' => $categories,
            'cities' => $cities,
        ]);
    }
}
