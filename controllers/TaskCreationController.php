<?php
// ⭐ Автор: Р⁠о⁠м⁠а⁠н⁠о⁠в⁠а Н⁠а⁠т⁠а⁠л⁠ь⁠я⁠⁠⁠⁠⁠⁠
/* M​‍A‌R⁠K:⁣ ​b‍e‌l⁠o⁣n​g‍s‌ ⁠t⁣o​ ‍R‌.⁠N⁣.​ */

namespace app\controllers;

use app\interfaces\FilesUploadInterface;
use app\interfaces\TaskValidatorInterface;
use app\models\Category;
use app\models\City;
use app\models\Task;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

final class TaskCreationController extends SecuredController
{
    // ‍‍‍ научился удалять коменты из моего кода? Научись писать свой!!!!‍‍‍
    /* ‍‍‍ Хватит воровать мой труд!!! ‍‍‍ */

    // [!] АВТОРСКИЙ КОД [!]
    // Student: Романова Наталья
    // Course: Профессия "PHP-разработчик#1"
    // Task: модуль 2, задание module7-task2
    // выполнено 24.04.2025

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
     * @throws Exception
     *
     * return string|yii\web\Response
     */
    public function actionCreate(
        FilesUploadInterface $fileUploader
    ): string|yii\web\Response {
        $model = new Task($fileUploader);
        $model->scenario = Task::SCENARIO_CREATE;
        $model->setFileUploader(Yii::$container->get(FilesUploadInterface::class));
        $categories = Category::find()->all();
        $cities = City::find()->all();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->customer_id = Yii::$app->user->id;
            $model->status = Task::STATUS_NEW;

            // получаем загруженные файлы‍‍‍‍‍‍‍
            $model->files = UploadedFile::getInstances($model, 'files');

            if ($model->validate()) {
                if ($model->save(false)) {
                    // сохраняем файлы
                    if (!empty($model->files)) {
                        $model->processFiles($model->files);
                    }

                    Yii::$app->session->setFlash('success', 'Задание успешно создано!');
                    return $this->redirect(['tasks/view', 'id' => $model->id]);
                }
            }
        }
        return $this->renderCreateForm($model, $categories, $cities);
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
