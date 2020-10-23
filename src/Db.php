<?php
/**
 * User: aozhuochao
 * Date: 2020/10/22
 */

namespace aogg\think\orm;


use think\db\ConnectionInterface;

class Db extends \think\Db
{
    protected function createConnection(string $name): ConnectionInterface
    {

        $config = $this->getConnectionConfig($name);

        $type = !empty($config['type']) ? $config['type'] : 'mysql';

        if($type === 'mysql') {
            $name = \aogg\think\orm\Mysql::class;
        }

        return parent::createConnection($name);
    }

}