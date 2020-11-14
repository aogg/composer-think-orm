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

    /**
     * insert根据select的数据
     *
     * @param string $fullTableName 完整表名
     * @param string $whereString where条件
     * @param array $fieldsValue 替换字段 ($filed=>$value)结构
     * @return int
     */
    public function cloneByInsertSelect($fullTableName, $whereString, $fieldsValue = [])
    {

        /** @var Mysql $mysql */
        $mysql = $this->instance();

        $arr = $mysql->getTableFields($fullTableName);
        $pk = $mysql->getPk($fullTableName);
        $insertField = $selectField = '';

        foreach ($arr as $field) {
            // 去除主键
            if (is_string($pk) && $pk === $field) {
                continue;
            }else if(is_array($pk) && in_array($field, $pk)){
                continue;
            }

            // 构建
            $insertField .= $field . ',';

            if (isset($fieldsValue[$field])) {
                $selectField .= $fieldsValue[$field] . ',';
            }else{
                $selectField .= $field . ',';
            }
        }
        $insertField = rtrim($insertField, ',');
        $selectField = rtrim($selectField, ',');

        return static::execute(<<<SQL
INSERT INTO `{$fullTableName}` ({$insertField})
SELECT {$selectField} FROM `{$fullTableName}`
WHERE {$whereString};
SQL
);
    }

}