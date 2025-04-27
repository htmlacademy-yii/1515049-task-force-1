<?php

namespace app\interfaces;

use yii\base\Exception;

/**
 * @param array $files Массив объектов UploadedFile
 * @param int $taskId ID задачи
 * @return array Массив сохраненных моделей File
 * @throws Exception
 */
interface FilesUploadInterface
{
    public function upload(array $files, int $taskId): array;
}
