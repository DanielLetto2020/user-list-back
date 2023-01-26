<?php


namespace App\Enums;


class EnumUserAccessLevel extends AbstractEnum
{
    public const CREATOR = "CREATOR";
    public const DEVELOPER = "DEVELOPER";
    public const ADMIN = "ADMIN";
    public const USER = "USER";

    protected static array $validValues = [
        self::CREATOR => true,
        self::DEVELOPER => true,
        self::ADMIN => true,
        self::USER => true,
    ];

    public static array $meta = [
        self::CREATOR => [
            'description' => 'Создатель',
        ],
        self::DEVELOPER => [
            'description' => 'Разработчик',
        ],
        self::ADMIN => [
            'description' => 'Админ',
        ],
        self::USER => [
            'description' => 'Пользователь',
        ],
    ];
}
