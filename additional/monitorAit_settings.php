<?php
/* -----------------------------------------------------------
MonitorAit_settings.php
- contains the basisc settings like db connection, paths etc

Part of App: Monitor AIT / bouwstenen - Erik Jan de Wilde / Hansei

-------------------------------------------------------------- */

abstract class MonitorAit_settings
{
    public static $version = 'ejLocal';

    public static function getDBName()
    {
        if (self::$version == 'ejLocal') {
            return 'ait';
        }

        if (self::$version == 'dev') {
            return 'aitMonitor';
        }

        if (self::$version == 'prod') {
            return 'aitMonitor';
        }

    }

    public static function getDBuid()
    {
        if (self::$version == 'ejLocal') {
            return 'user';
        }

        if (self::$version == 'dev') {
            return 'aitMonitor_user';
        }

        if (self::$version == 'prod') {
            return 'aitMonitor_user';
        }

    }

    public static function getDBpw()
    {
        if (self::$version == 'ejLocal') {
            return 'user';
        }

        if (self::$version == 'dev') {
            return 'Wn5dxNAcH6Tk_!';
        }

        if (self::$version == 'prod') {
            return 'c171R94a67c8f663_nksoe946_2hwQw';
        }

    }

    public static function getPlugin_dir_url()
    {
        if (self::$version == 'ejLocal') {
            return 'https://localhost/hansei/ait/wp-content/plugins/bouwstenen/';
        }

        if (self::$version == 'dev') {
            return 'https://mijnait.konsili.dev/_additional_classes/_third_party/AitMonitor/bouwstenen/';
        }

        if (self::$version == 'prod') {
            return 'https://mijn.aitnl.org/_additional_classes/_third_party/AitMonitor/bouwstenen/';
        }

    }

    public static function getSite_url()
    {
        if (self::$version == 'ejLocal') {
            return 'https://localhost/hansei/ait/wp-content/plugins/';
        }

        if (self::$version == 'dev') {
            return 'https://mijnait.konsili.dev';
        }

        if (self::$version == 'prod') {
            return 'https://mijn.aitnl.org';
        }

    }

    public static function getCurrentUid($userDataAitId)
    {
        if (self::$version == 'ejLocal') {
            return '3';
        }

        if (self::$version == 'dev') {
            return '3';
        }

        if (self::$version == 'prod') {
            return $userDataAitId;
        }

    }

}
