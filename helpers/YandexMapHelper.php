<?php

namespace app\helpers;

use Yandex\Geo\Api;
use Yandex\Geo\Exception;
use yii\base\Component;
use yii\caching\CacheInterface;
use Yii;

final class YandexMapHelper extends Component
{
    private string $apiKey;
    private ?CacheInterface $cache = null;
    private int $cacheDuration = 86400; // 1 день

    public function __construct(string $apiKey, $config = [])
    {
        $this->apiKey = $apiKey;
        parent::__construct($config);
    }

    public function getAddress(float $latitude, float $longitude): string
    {
        $cacheKey = "address_{$latitude}_{$longitude}";

        if ($this->cache && ($address = $this->cache->get($cacheKey))) {
            return $address;
        }

        try {
            $apiUrl = sprintf(
                'https://geocode-maps.yandex.ru/1.x/?format=json&geocode=%s,%s&apikey=%s',
                $longitude,
                $latitude,
                $this->apiKey
            );

            $response = file_get_contents($apiUrl);
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response');
            }

            if (!empty($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['text'])) {
                $address = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['text'];

                if ($this->cache) {
                    $this->cache->set($cacheKey, $address, $this->cacheDuration);
                }

                return $address;
            }
        } catch (\Exception $e) {
            Yii::error("Ошибка обратного геокодирования: " . $e->getMessage());
        }

        return 'Адрес не определен';
    }

    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    public function setCacheDuration(int $seconds): void
    {
        $this->cacheDuration = $seconds;
    }
}
