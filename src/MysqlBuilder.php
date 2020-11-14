<?php
/**
 * User: aogg
 * Date: 2020/11/12
 */

namespace aogg\think\orm;


use think\db\Query;

class MysqlBuilder extends \think\db\builder\Mysql
{
    public function select(Query $query, bool $one = false): string
    {
        \aogg\think\orm\Set::handleGlobalField($this, $query, 'where');
        return parent::select($query, $one);
    }

    public function insert(Query $query): string
    {
        \aogg\think\orm\Set::handleGlobalField($this, $query, 'insert');
        return parent::insert($query);
    }

    public function update(Query $query): string
    {
        \aogg\think\orm\Set::handleGlobalField($this, $query, 'update');
        return parent::update($query);
    }

    public function delete(Query $query): string
    {
        \aogg\think\orm\Set::handleGlobalField($this, $query, 'where');
        return parent::delete($query);
    }

    public function insertAll(Query $query, array $dataSet, bool $replace = false): string
    {
        \aogg\think\orm\Set::handleGlobalField($this, $query, 'insertAll', $dataSet);
        return parent::insertAll($query, $dataSet, $replace);
    }

    /**
     * 公开此方法
     *
     * @param Query $query
     * @param mixed $tables
     * @return string
     */
    public function parseTable(Query $query, $tables): string
    {
        return parent::parseTable($query, $tables);
    }

}