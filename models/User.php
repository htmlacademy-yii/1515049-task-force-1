<?php

namespace app\models;

use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password_hash
 * @property string $role
 * @property int|null $city_id
 * @property string|null $avatar
 * @property string|null $telegram
 * @property string|null $phone
 * @property int|null $show_contacts
 * @property string|null $birthday
 * @property string|null $info
 * @property string|null $created_at
 * @property int $accepts_orders
 *
 * @property Category[] $category
 * @property City $city
 * @property Response[] $response
 * @property Review[] $review
 * @property Review[] $review0
 * @property Task[] $task
 * @property Task[] $task0
 * @property UserSpecialization[] $userSpecialization
 * @property float|int $executor_rating
 * @property-read null|int $age
 * @property-read int $executorReviewsCount
 * @property-read ActiveQuery $categories
 * @property-read ActiveQuery $executorReviews
 * @property-read float $executorRating
 * @property int $executor_reviews_count
 */
class User extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const string ROLE_CUSTOMER = 'customer';
    const string ROLE_EXECUTOR = 'executor';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['city_id', 'avatar', 'telegram', 'phone', 'birthday', 'info'], 'default', 'value' => null],
            [['show_contacts', 'executor_reviews_count'], 'default', 'value' => 0],
            [['executor_rating'], 'default', 'value' => 0.00],
            [['name', 'email', 'password_hash', 'role'], 'required'],
            [['accepts_orders'], 'boolean'],
            [['role', 'info'], 'string'],
            [['city_id', 'show_contacts', 'executor_reviews_count'], 'integer'],
            [['birthday', 'created_at'], 'safe'],
            [['name', 'email', 'password_hash', 'avatar', 'telegram'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 20],
            [['executor_rating'], 'number', 'min' => 0, 'max' => 5],
            [['executor_rating'], 'default', 'value' => 0],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            [['email'], 'unique'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'role' => 'Role',
            'city_id' => 'City ID',
            'avatar' => 'Avatar',
            'telegram' => 'Telegram',
            'accepts_orders' => 'Is accept',
            'phone' => 'Phone',
            'show_contacts' => 'Show Contacts',
            'birthday' => 'Birthday',
            'info' => 'Info',
            'created_at' => 'Created At',
            'executor_rating' => 'Рейтинг исполнителя',
            'executor_reviews_count' => 'Количество отзывов',
        ];
    }

    /**
     * Gets average rating from executor reviews
     * @return float
     */
    public function getExecutorRating(): float
    {
        return (float)$this->getExecutorReviews()->average('rating') ?: 0;
    }

    /**
     * Gets count of executor reviews
     * @return int
     */
    public function getExecutorReviewsCount(): int
    {
        return $this->getExecutorReviews()->count();
    }

    /**
     * Gets query for executor reviews (Review0 relation alias)
     * @return ActiveQuery
     */
    public function getExecutorReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['executor_id' => 'id']);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_INSERT => ['executor_rating', 'executor_reviews_count'],
                    ActiveRecord::EVENT_AFTER_UPDATE => ['executor_rating', 'executor_reviews_count'],
                ],
                'value' => function ($event) {
                    if ($this->role === self::ROLE_EXECUTOR) {
                        return [
                            'executor_rating' => $this->calculateExecutorRating(),
                            'executor_reviews_count' => $this->getExecutorReviews()->count(),
                        ];
                    }
                    return null;
                }
            ]
        ];
    }

    public function calculateExecutorRating(): float|int
    {
        return (float)$this->getExecutorReviews()->average('rating') ?: 0;
    }

    public function updateExecutorStars(): void
    {
        if ($this->role === self::ROLE_EXECUTOR) {
            $this->executor_rating = $this->calculateExecutorRating();
            $this->executor_reviews_count = $this->getExecutorReviews()->count();

            $this->updateAttributes(['executor_rating', 'executor_reviews_count']);
        }
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])->viaTable('user_specializations', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function getAge(): ?int
    {
        if (empty($this->birthday)) {
            return null;
        }

        $birthday = new DateTime($this->birthday);
        $today = new DateTime();
        $interval = $today->diff($birthday);

        return $interval->y;
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(Response::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['customer_id' => 'id']);
    }


    /**
     * Gets query for [[Tasks]].
     *
     * @return ActiveQuery
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return ActiveQuery
     */
    public function getExecutorTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[UserSpecializations]].
     *
     * @return ActiveQuery
     */
    public function getUserSpecializations(): ActiveQuery
    {
        return $this->hasMany(UserSpecialization::class, ['user_id' => 'id']);
    }


    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole(): array
    {
        return [
            self::ROLE_CUSTOMER => 'customer',
            self::ROLE_EXECUTOR => 'executor',
        ];
    }

    /**
     * @return string
     */
    public function displayRole(): string
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function setRoleToCustomer(): void
    {
        $this->role = self::ROLE_CUSTOMER;
    }

    /**
     * @return bool
     */
    public function isRoleExecutor(): bool
    {
        return $this->role === self::ROLE_EXECUTOR;
    }

    public function setRoleToExecutor(): void
    {
        $this->role = self::ROLE_EXECUTOR;
    }
}
