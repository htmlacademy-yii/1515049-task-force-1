<?php

declare(strict_types=1);

namespace app\controllers;

use app\logic\Actions\ActionReject;
use app\logic\Actions\ActionRespond;
use app\models\Response;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 *  Контроллер для обработки действий принять/отказать каждого отклика на задание
 */
final class ResponseActionsController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    throw new ForbiddenHttpException('У вас нет прав для выполнения этого действия');
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['accept', 'reject'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isRoleExecutor();
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function actionAccept(int $id): \yii\web\Response
    {
        $response = Response::findOne($id);
        if (!$response) {
            throw new NotFoundHttpException("Отклик не найден");
        }

        $task = $response->task;
        $action = new ActionRespond();

        if (!$action->isAvailable(Yii::$app->user->id, $task->customer_id, $task->executor_id)) {
            throw new ForbiddenHttpException("Действие недоступно");
        }

        if ($action->execute($task, $response)) {
            Yii::$app->session->setFlash('success', 'Отклик принят');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось принять отклик');
        }
        Yii::info("Redirecting to task view: task ID {$task->id}", 'response-actions');
        return $this->redirect(['tasks/view', 'id' => $task->id]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionReject(int $id): \yii\web\Response
    {
        $response = Response::findOne($id);
        if (!$response) {
            throw new NotFoundHttpException("Отклик не найден");
        }

        $task = $response->task;
        $action = new ActionReject();

        if (!$action->isAvailable(Yii::$app->user->id, $task->customer_id, $task->executor_id)) {
            throw new ForbiddenHttpException("Действие недоступно");
        }

        if ($action->execute($response)) {
            Yii::$app->session->setFlash('success', "Отклик отклонен");
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось отклонить отклик');
        }

        return $this->redirect(['tasks/view', 'id' => $task->id]);
    }
}
