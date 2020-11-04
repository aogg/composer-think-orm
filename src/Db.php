<?php
/**
 * User: aogg
 * Date: 2020/10/22
 */

namespace aogg\think\orm;


use think\db\ConnectionInterface;

class Db extends \think\Db
{
    protected function getConnectionConfig(string $name): array
    {
        $arr = parent::getConnectionConfig($name);
        if (strtolower(!empty($arr['type'])?$arr['type']:'') === 'mysql') {
            $arr['type'] = Mysql::class;
        }

        return $arr;
    }

}