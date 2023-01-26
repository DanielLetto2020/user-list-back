<?php


namespace App\Enums;


class EnumAppType extends AbstractEnum
{
    public const WEB_USER = "WEB_USER";
    public const DASHBOARD_USER = "DASHBOARD_USER";

    protected static array $validValues = [
        self::WEB_USER => true,
        self::DASHBOARD_USER => true,
    ];

    public static array $meta = [
        self::WEB_USER => [
            'description' => "Авторизация пользователя",
        ],
        self::DASHBOARD_USER => [
            'description' => "Админка",
        ],
    ];
}
