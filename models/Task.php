<?php

namespace app\models;

use AllowDynamicProperties;
use app\interfaces\FilesUploadInterface;
use app\logic\Actions\CreateTaskAction;
use app\logic\AvailableActions;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $category_id
 * @property float|null $budget
 * @property string $status Статус задачи. Возможные значения:
 *          AvailableActions::STATUS_NEW,
 *          AvailableActions::STATUS_IN_PROGRESS,
 *          AvailableActions::STATUS_COMPLETED,
 *          AvailableActions::STATUS_FAILED,
 *          AvailableActions::STATUS_CANCELLED
 * @property int|null $city_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $ended_at
 * @property int $customer_id
 * @property int|null $executor_id
 * @property string|null $created_at
 *
 * @property Category $category
 * @property City $city
 * @property User $customer
 * @property User $executor
 * @property File[] $files
 * @property Response[] $responses
 * @property-read string $statusLabel
 * @property-read ActiveQuery $searchQuery
 * @property Review[] $reviews
 * @property FilesUploadInterface $fileUploader
 */
class Task extends ActiveRecord
{
    public $categoryIds;
    public $noResponses;
    public $noLocation;
    public $filterPeriod;
    public array $files = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tasks';
    }

    public function rules(): array
    {
        return [
            [['budget', 'city_id', 'latitude', 'longitude', 'ended_at', 'executor_id'], 'default', 'value' => null],
            [['status'], 'in', 'range' => array_keys(AvailableActions::getStatusMap())],
            [
                ['category_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' =>
                    Category::class,
                'targetAttribute' => ['category_id' => 'id']
            ],
            [['description', 'status'], 'string'],
            [['category_id', 'city_id', 'customer_id', 'executor_id'], 'integer'],
            [['budget', 'latitude', 'longitude'], 'number'],
            [['ended_at', 'created_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['categoryIds', 'noResponses', 'noLocation', 'filterPeriod'], 'safe'],
            [
                ['customer_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['customer_id' => 'id']
            ],
            [
                ['executor_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['executor_id' => 'id']
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Опишите суть работы',
            'description' => 'Подробности задания',
            'category_id' => 'Категория',
            'budget' => 'Бюджет',
            'status' => 'Status',
            'city_id' => 'Локация',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'ended_at' => 'Срок исполнения',
            'customer_id' => 'Customer ID',
            'executor_id' => 'Executor ID',
            'created_at' => 'Created At',
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[CreateTaskAction::SCENARIO_CREATE] = [
            'title',
            'description',
            'category_id',
            'budget',
            'city_id',
            'latitude',
            'longitude',
            'ended_at',
            'files'
        ];
        return $scenarios;
    }

    public function defineScenario($name, $attributes): void
    {
        $scenarios = $this->scenarios();
        $scenarios[$name] = $attributes;
        $this->setScenario($name);
    }

    private FilesUploadInterface $fileUploader;

    public function setFileUploader(FilesUploadInterface $fileUploader): void
    {
        $this->fileUploader = $fileUploader;
    }

    public function processFiles(array $files): array
    {
        if ($this->isNewRecord) {
            throw new \RuntimeException('Невозможно обработать файлы для несохраненной задачи');
        }

        return $this->fileUploader->upload($files, $this->id);
    }

    /**
     * @return array
     */
    public static function getStatusLabels(): array
    {
        return AvailableActions::getStatusMap();
    }

    /**
     * Валидация дедлайна при создании нового задания
     *
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function validateDeadline($attribute, $params): bool
    {
        if ($this->$attribute && strtotime($this->$attribute) <= strtotime('now')) {
            $this->addError($attribute, 'Срок исполнения не может быть раньше текущей даты');
        }
        return true;
    }

    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return AvailableActions::getStatusMap()[$this->status] ?? $this->status;
    }

    /**
     * Поиск задач с фильтрами
     */
    public function getSearchQuery(): ActiveQuery
    {
        $query = self::find()->where(['status' => AvailableActions::STATUS_NEW]);

        if (!empty($this->categoryIds)) {
            $categoryIds = is_array($this->categoryIds)
                ? $this->categoryIds
                : array_filter(explode(',', $this->categoryIds));

            if (!empty($categoryIds)) {
                $query->andWhere(['category_id' => $categoryIds]);
            }
        }

        if ($this->noResponses) {
            $query->leftJoin('responses', 'responses.task_id = tasks.id')
                ->andWhere(['responses.id' => null]);
        }

        if ($this->noLocation) {
            $query->andWhere(['city_id' => null]);
        }

        if (!empty($this->filterPeriod)) {
            $period = (int)$this->filterPeriod;
            if ($period > 0) {
                $query->andWhere(['>=', 'tasks.created_at', date('Y-m-d H:i:s', time() - $period)]);
            }
        }

        return $query->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Создает DataProvider для использования в GridView/ListView
     */
    public function getDataProvider($pageSize = 5): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $this->getSearchQuery(),
            'pagination' => ['pageSize' => $pageSize],
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]]
        ]);
    }

    /**
     * Gets a query for [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Фильтрация по статусу "Новые"
     */
    public static function findNewTasks(): ActiveQuery
    {
        return self::find()->where(['status' => AvailableActions::STATUS_NEW]);
    }

    /**
     * Фильтрация задач без исполнителя
     */
    public static function findWithoutExecutor(): ActiveQuery
    {
        return self::find()->where(['executor_id' => null]);
    }

    /**
     * Фильтрация задач по периоду
     */
    public static function filterByPeriod(ActiveQuery $query, int $hours): ActiveQuery
    {
        return $query->andWhere(['>=', 'created_at', time() - $hours * 3600]);
    }

    /**
     * Фильтрация по категориям
     */
    public static function filterByCategories(ActiveQuery $query, array $categoryIds): ActiveQuery
    {
        return $query->andWhere(['category_id' => $categoryIds]);
    }

    /**
     * Gets a query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets a query for [[Customer]].
     *
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }

    /**
     * Gets a query for [[Executor]].
     *
     * @return ActiveQuery
     */
    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * Gets a query for [[Files]].
     *
     * @return ActiveQuery
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(File::class, ['task_id' => 'id']);
    }

    /**
     * Gets a query for [[Responses]].
     *
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(Response::class, ['task_id' => 'id']);
    }

    /**
     * Gets a query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['task_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function isStatusNew(): bool
    {
        return $this->status === AvailableActions::STATUS_NEW;
    }

    public function setStatusToNew(): void
    {
        $this->status = AvailableActions::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isStatusInProgress(): bool
    {
        return $this->status === AvailableActions::STATUS_IN_PROGRESS;
    }

    public function setStatusToInProgress(): void
    {
        $this->status = AvailableActions::STATUS_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isStatusCompleted(): bool
    {
        return $this->status === AvailableActions::STATUS_COMPLETED;
    }

    public function setStatusToCompleted(): void
    {
        $this->status = AvailableActions::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isStatusFailed(): bool
    {
        return $this->status === AvailableActions::STATUS_FAILED;
    }

    public function setStatusToFailed(): void
    {
        $this->status = AvailableActions::STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function isStatusCanceled(): bool
    {
        return $this->status === AvailableActions::STATUS_CANCELLED;
    }

    public function setStatusToCanceled(): void
    {
        $this->status = AvailableActions::STATUS_CANCELLED;
    }
}
