<?php
/**
 * User: aogg
 * Date: 2020/7/31
 */

namespace aogg\think\orm;


class Service extends \think\Service
{
    public function register()
    {
        $this->app->bind('db', \aogg\think\orm\Db::class);
    }
}
