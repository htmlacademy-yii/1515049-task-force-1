<?php

namespace app\helpers;

use Yandex\Geo\Api;
use Yii;

final class YandexMapHelper
{
    private Api $apiClient;

    public function __construct($key)
    {
        $this->apiClient = new Api();
        $this->apiClient->setToken($key);
    }

    public function getCoordinates($city, $location): array
    {
        try {
            $query = trim($city . (!empty($location) ? ", $location" : ""));
            if (empty($query)) {
                return [null, null];
            }

            $this->apiClient->setQuery($query);
            $this->apiClient->load();

            $response = $this->apiClient->getResponse();
            if ($response && ($results = $response->getList())) {
                $geoObject = $results[0];
                return [$geoObject->getLatitude(), $geoObject->getLongitude()];
            }
        } catch (\Exception $e) {
            Yii::error("Ошибка геокодирования: " . $e->getMessage());
        }

        return [null, null];
    }
}
