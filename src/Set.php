<?php
/**
 * User: aogg
 * Date: 2020/11/12
 */

namespace aogg\think\orm;

/**
 * 各种功能
 */
class Set
{
    protected $mysqlConnectorEventFinishBool = false;

    /**
     * 全局字段
     * 查询和更新时自带where
     * 创建时增加字段
     *
     * 暂时只支持mysql
     *
     * key => value组合
     * key是字段名
     * value是字段值
     *
     * @var array
     */
    protected static $globalField = [];

    /**
     * 全局字段
     * 强制保存覆盖
     *
     * @var bool
     */
    protected static $globalFieldForceSaveBool = true;

    /**
     * 全局字段
     * 设置与开启
     *
     * @param $field
     * @param $globalFieldValue
     */
    public static function setGlobalField($field, $globalFieldValue)
    {
        static::$globalField[$field] = $globalFieldValue;
    }

    /**
     * handleGlobalField方法
     * 处理保存的数据
     *
     * @param $data
     * @param $field
     * @param $globalFieldValue
     */
    protected static function handleGlobalFieldDataPro(&$data, $field, $globalFieldValue)
    {
        if (static::$globalFieldForceSaveBool){

            $data[$field] = $globalFieldValue;
        } else if(is_string($field) && !isset($data[$field])){

            $data[$field] = $globalFieldValue;
        }
    }

    /**
     * 处理字段的表名
     *
     * @param MysqlBuilder $builder
     * @param Query|\think\db\Query $query
     * @param string $table
     * @return string
     */
    protected static function handleFieldTableName($builder, $query, $table = '')
    {
        $table = $table?:$query->getTable();
        $table = $builder->parseTable($query, $table);

        if (preg_match('/(\s*select.+from)/i', $table)) { // 整体子查询
            return false;
        }

        preg_match('/([^\s]+)(?:\s+([^\s]+))?\s*$/', $table, $matches);
        $lastTableName = (!empty($matches[2]) ? $matches[2] : (!empty($matches[1]) ? $matches[1] : '')) . '.';
        $lastTableName = $lastTableName === '.' ? '' : $lastTableName; // 去无效点

        return $lastTableName;
    }

    /**
     * 处理全局字段
     * 的join
     *
     * @param MysqlBuilder $builder
     * @param Query|\think\db\Query $query
     */
    protected static function handleGlobalFieldJoinPro($builder, $query)
    {
        $join = $query->getOptions('join');

        foreach ($join as &$item) {
            list($table, $type, $on) = $item;
            $lastTableName = static::handleFieldTableName($builder, $query, $table);

            $on = empty($on) ? $on : ($on . ' and');
            if (strpos($on, '=') === false) {// tp bug没有=号时就不能设置（=整数）
                $on .= ' "1"="1" and ';
            }

            foreach (static::$globalField as $field => $globalFieldValue) {

                $on .= " {$lastTableName}`{$field}` = " . (is_string($globalFieldValue)?"'{$globalFieldValue}'":$globalFieldValue) . ' and';
            }

            $on = rtrim($on, ' and');

            $item[2] = $on;
        }

        $query->setOption('join', $join);
    }

    /**
     * 处理全局字段
     *
     * @param MysqlBuilder $builder
     * @param Query|\think\db\Query $query
     * @param string $type
     * @param array $insertAllData
     */
    public static function handleGlobalField($builder, $query, $type, &$insertAllData = [])
    {
        if (empty(static::$globalField)) {
            return;
        }else if($query->isSkipGlobalField()){ // 当前查询跳过全局字段
            return;
        }

        if ($type === 'where') {
            static::handleGlobalFieldJoinPro($builder, $query); // join
            $lastTableName = static::handleFieldTableName($builder, $query);

            if ($lastTableName === false) { // 整体子查询
                return;
            }

            $lastTableName = str_replace('`', '', $lastTableName);

            foreach (static::$globalField as $field => $globalFieldValue) {

                $query->where($lastTableName . $field, $globalFieldValue);
            }

        }else if($type === 'update'){ // 待处理控制data
            $data = $query->getOptions('data');
            static::handleGlobalFieldJoinPro($builder, $query); // join
            $lastTableName = static::handleFieldTableName($builder, $query);

            if ($lastTableName === false) { // 整体子查询
                return;
            }
            $lastTableName = str_replace('`', '', $lastTableName); // 字段不支持表名有`

            foreach (static::$globalField as $field => $globalFieldValue) {
                $query->where($lastTableName . $field, $globalFieldValue);

                static::handleGlobalFieldDataPro($data, $field, $globalFieldValue);
            }

            $query->setOption('data', $data);

        }else if($type === 'insert'){
            $data = $query->getOptions('data');

            foreach (static::$globalField as $field => $globalFieldValue) {
                static::handleGlobalFieldDataPro($data, $field, $globalFieldValue);
            }

            $query->setOption('data', $data);
        }else if($type === 'insertAll'){

            foreach (static::$globalField as $field => $globalFieldValue) {
                foreach ($insertAllData as &$datum) {
                    static::handleGlobalFieldDataPro($datum, $field, $globalFieldValue);
                }
            }

        }

    }

    /**
     * @param bool $globalFieldForceSaveBool
     */
    public static function setGlobalFieldForceSaveBool(bool $globalFieldForceSaveBool)
    {
        self::$globalFieldForceSaveBool = $globalFieldForceSaveBool;
    }

    /**
     * @param bool $mysqlConnectorEventFinishBool
     * @return Set
     */
    public function setMysqlConnectorEventFinishBool(bool $mysqlConnectorEventFinishBool = true)
    {
        $this->mysqlConnectorEventFinishBool = $mysqlConnectorEventFinishBool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMysqlConnectorEventFinishBool(): bool
    {
        return $this->mysqlConnectorEventFinishBool;
    }
}