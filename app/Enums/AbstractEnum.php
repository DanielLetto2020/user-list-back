<?php

namespace App\Enums;

use Illuminate\Database\Eloquent\Builder;

// use ReflectionClass;

/**
 * Class AbstractEnum
 * @package App\Enums
 */
abstract class AbstractEnum
{
    public const classTitle = 'Заголовок класса';
    public const classKey = 'classKey';

    /**
     * приоритетность группировки определяется последовательностью
     * ['apartment_type', 'group_title'];
     */
    public const groupBy = [];
    public const metaDataEnums = [];

    /**
     * @var array Массив принимаемых enum'ом значений
     */
    protected static array $validValues = [];
    public static array $meta = [];

    public static function meta()
    {
        return static::$meta;
    }

    /**
     * Проверяет наличие значения в enum'e
     * @param mixed $value Проверяемое значение
     * @return bool True если значение имеется, false если нет
     */
    public static function valueExists($value)
    {
        return array_key_exists($value, static::$validValues);
    }

    public static function getMetaByKey($key, $metaKey = '')
    {
        if (!$metaKey) {
            return static::$meta[$key];
        }

        return static::$meta[$key][$metaKey];
    }

    /**
     * Возвращает все значения в enum'e
     * @return array Массив значений в перечислении
     */
    public static function getValidValues(): array
    {
        return array_keys(static::$validValues);
    }

    /**
     * Возвращает значения в enum'е значения которых разрешены
     * @return string[] Массив разрешённых значений
     */
    public static function getEnabledValues(): array
    {
        $result = [];
        foreach (static::$validValues as $key => $enabled) {
            if ($enabled) {
                $result[] = $key;
            }
        }
        return $result;
    }

    public static function getEnabledMeta(): array
    {
        $result = [];
        foreach (static::$validValues as $key => $enabled) {
            if ($enabled) {
                $result[$key] = static::$meta[$key];
            }
        }
        return $result;
    }

    public static function getOneRandomKeyEnabledValues()
    {
        $array = self::getEnabledValues();
        $length = count($array) - 1;
        return $array[random_int(0, $length)];
    }

    public static function getEnabledKeyWhereProps($key, $value)
    {
        return collect(self::getEnabledMeta())->where($key, $value)->keys()->random();
    }

    /**
     * @param array $addFields
     * @param string $metaKey
     * @return array
     */
    public static function getMeta(array $addFields = []): array
    {
        $result = [];
        foreach (static::$validValues as $key => $enabled) {
            if ($enabled) {

                $data = [];

                if (count($addFields) > 0) {
                    foreach ($addFields as $field) {
                        $data[$field] = static::$meta[$key][$field] ?? null;
                    }
                }

                $result[$key] = $data;
            }
        }
        return $result;
    }

    /**
     * @param array $addFields
     * @param array $groupBy
     * @param string $prefixRootKey
     * @return array|\null[][]|string[][]
     */
    public static function listForFrontend(array $addFields = [], array $groupBy = [], string $prefixRootKey = ''): array
    {
        $root_key = static::classKey;

        if ($prefixRootKey) {
            $root_key = $prefixRootKey . '_' . static::classKey;
        }

        $data = [
            $root_key => [
                'title' => static::classTitle ?? null,
            ]
        ];

        $data[$root_key]['uniqueValues'] = (object)self::getMeta(['title', ...$addFields]);

        if (count($groupBy) > 0) {
            $getMetaList = (self::getMeta(['title', ...$addFields, ...$groupBy]));

            $data[$root_key]['group'] = collect($getMetaList)
                ->groupBy($groupBy, true)
                ->transform(function ($item) {
                    return $item->keys();
                });

            $data[$root_key]['uniqueValues'] = (object)collect($getMetaList)
                ->transform(function ($item) use ($groupBy, $addFields) {

                    return (object)collect($item)->except(collect($groupBy)->diff($addFields)->toArray())->toArray();
                });

        }

        if (static::metaDataEnums) {
            $data[$root_key]['enums'] = collect(static::metaDataEnums)->map(function ($class) {
                return (app($class))::getMeta(['title']);
            })->collapse();
        }

        return $data;
    }


    /**
     * @param array $addFields Дополнительные поля которые нужно добавить для фронта
     * @return array
     */
//    public static function frontData(array $addFields = []): array
//    {
////        $ReflectionClass = new ReflectionClass(static::class);
////        $Constants = $ReflectionClass->getConstants();
////        $Constants = $ReflectionClass->getMethod('meta');
//
//        $result = [];
//        $loop = 0;
//        foreach (static::$validValues as $key => $enabled) {
//            if ($enabled) {
//
//                $data = [
//                    'id' => ++$loop,
//                    'value' => $key,
//                    'title' => static::$meta[$key]['title'] ?? '',
//                ];
//
//                if (count($addFields) > 0) {
//                    foreach ($addFields as $field) {
//                        $data[$field] = static::$meta[$key][$field];
//                    }
//                }
//
//                $result[] = $data;
//            }
//        }
//
//        return $result;
//    }
//
//    public static function frontDataProperty(array $addFields = []): array
//    {
//        $result = [];
//
//        foreach (static::$validValues as $key => $enabled) {
//            if ($enabled) {
//
//                $data = [
//                    'title' => static::$meta[$key]['title'] ?? '',
//                ];
//
//                if (count($addFields) > 0) {
//                    foreach ($addFields as $field) {
//                        $data[$field] = static::$meta[$key][$field];
//                    }
//                }
//
//                $result[$key] = $data;
//            }
//        }
//
//        return [
//            'title' => static::classTitle,
//            'type' => EnumType::ENUM,
//            'data' => (object)$result
//        ];
//    }
//
//    public static function frontDataByKey(string $key, array $addFields = [])
//    {
//        $loop = 0;
//        if (static::$validValues[$key]) {
//            $data = [
//                'id' => ++$loop,
//                'value' => $key,
//                'title' => static::$meta[$key]['title'] ?? '',
//            ];
//
//            if (count($addFields) > 0) {
//                foreach ($addFields as $field) {
//                    $data[$field] = static::$meta[$key][$field];
//                }
//            }
//
//            return $data;
//        }
//
//        return [];
//    }
}
