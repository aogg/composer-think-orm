<?php
/**
 * User: aogg
 * Date: 2020/10/22
 */

namespace aogg\think\orm;


use think\Collection;
use think\db\BaseQuery;

class Mysql extends \think\db\connector\Mysql
{
    public function __construct(array $config = [])
    {
        if ($config['type'] === Mysql::class) { // 设置为class，会导致报错
            $config['type'] = 'mysql';
        }

        parent::__construct($config);

        /** @var Set $setObject */
        $setObject = app(Set::class);
        if (!$setObject->isMysqlConnectorEventFinishBool()){ // 指通知一次
            $setObject->setMysqlConnectorEventFinishBool();
            event('ConnectorPdoCreateFirst', [$this]); // pdo连接首次创建通知
        }
    }

    /**
     * 获取当前连接器类对应的Query类
     *
     * @access public
     * @return string
     */
    public function getQueryClass(): string
    {
        return $this->getConfig('query') ?: Query::class;
    }

    /**
     * 获取当前连接器类对应的Builder类
     *
     * @access public
     * @return string
     */
    public function getBuilderClass(): string
    {
        return $this->getConfig('builder') ?: MysqlBuilder::class;
    }

    /**
     * 支持换成分页
     *
     * @param BaseQuery $query
     * @return array|Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function select(BaseQuery $query): array
    {
        /** @var Query $query */
        if (empty($query->getCacheLimit())) { // 没有设置缓存一切照旧
            return parent::select($query);
        }

        list($offset, $length) = $query->getOptions('limit');
        $page = $query->getOptions('page');

        $query = $query->limit($query->getCacheLimit());
        $query->setOption('page', null);
        if($page){
            $length = isset($page[1])?$page[1]:0;
            $offset = (max(isset($page[0])?$page[0]:1, 1) - 1) * $length;
        }
        $length = $length >= 0?$length:15;
        $offset = max($offset, 0);

        $list = parent::select($query);

//        $page = explode(',', $page?:'');

        $list = array_slice(
            $list,
            $offset,
            $length
        );
//        if ($list instanceof Collection) {
//            $list = $list->slice(
//                $offset,
//                $length
//            );
//        }else if(is_array($list)){ // 必定array
//        }

        return $list;
    }


}