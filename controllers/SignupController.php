<?php

namespace app\controllers;

use app\models\City;
use app\models\SignupForm;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\Response;

class SignupController extends Controller
{
    /**
     * @throws Exception
     */
    public function actionIndex(): Response|string
    {
        $user = new User();
        $model = new SignupForm();
        $cities = City::find()->select(['name', 'id'])->indexBy('id')->column();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->signup()) {
                Yii::$app->user->login($user);
                return $this->goHome();
            }
        }
        return $this->render('index', [
            'model' => $model,
            'cities' => $cities
        ]);
    }
}