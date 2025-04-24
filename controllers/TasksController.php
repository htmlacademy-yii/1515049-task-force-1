<?php

namespace app\controllers;

use app\interfaces\FilesUploadInterface;
use app\models\Response;
use app\models\Task;
use Yii;
use yii\data\ActiveDataProvider;
use app\models\Category;
use yii\web\NotFoundHttpException;

class TasksController extends SecuredController
// [!] АВТОРСКИЙ КОД [!]
    // Student: Романова Наталья
    // Course: Профессия "PHP-разработчик#1"
    // Task: модуль 2, задание module7-task2
    // изменено 24.04.2025
{
    private FilesUploadInterface $fileUploader;

    public function __construct(
        $id,
        $module,
        FilesUploadInterface $fileUploader,
        $config = []
    ) {
        $this->fileUploader = $fileUploader;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $task = new Task();
        $task->setFileUploader($this->fileUploader);
        $task->load(Yii::$app->request->post());

        $categories = Category::find()->all();

        $dataProvider = $task->getDataProvider();

        return $this->render('index/index', [
            'categories' => $categories,
            'dataProvider' => $dataProvider,
            'task' => $task,
        ]);
    }

    /**
     * Просмотр конкретного задания
     * @param int $id ID задания
     * @return string
     * @throws NotFoundHttpException
     * @used-by \app\config\web::urlManager Правила маршрутизации
     * @used-by \app\views\tasks\_task-list.php Ссылки в списке задач
     */
    public function actionView(int $id): string
    {
        $task = Task::findOne($id);
        $responsesDataProvider = new ActiveDataProvider([
            'query' => Response::find()
                ->where(['task_id' => $id])
                ->with('executor.executorReviews'),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
        ]);
        if (!$task) {
            throw new NotFoundHttpException('Задание не найдено.');
        }

        return $this->render('view-task/view', [
            'task' => $task,
            'responsesDataProvider' => $responsesDataProvider,
        ]);
    }
} // TODO поудалять перед проверкой!!!!
