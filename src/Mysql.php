<?php
/**
 * User: aozhuochao
 * Date: 2020/10/22
 */

namespace aogg\think\orm;


use think\Collection;
use think\db\BaseQuery;

class Mysql extends \think\db\connector\Mysql
{
    protected $cacheLimit = 0;

    public function getQueryClass(): string
    {
        return $this->getConfig('query') ?: Query::class;
    }

    /**
     * 支持换成分页
     *
     * @param BaseQuery $query
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function select(BaseQuery $query): array
    {
        $limit = $query->getOptions('limit');
        $page = $query->getOptions('page');

        $query->limit($this->cacheLimit);
        $query->setOption('page', null);

        $list = parent::select($query);

        $limit = explode(',', $limit?:'');
        $page = explode(',', $page?:'');
        if ($list instanceof Collection) {
            if($page){
                $limit[1] = max(isset($page[1])?$page[1]:0, 0);
                $limit[0] = (max(isset($page[0])?$page[0]:1, 1) - 1) * $limit[1];
            }

            if ($limit) {
                $list = $list->slice(
                    isset($limit[0])?$limit[0]:0,
                    isset($limit[1])?$limit[1]:15
                );
            }
        }else if(is_array($list)){
            if($page){
                $limit[1] = max(isset($page[1])?$page[1]:0, 0);
                $limit[0] = (max(isset($page[0])?$page[0]:1, 1) - 1) * $limit[1];
            }

            if ($limit){
                $list = array_slice(
                    $list,
                    isset($limit[0])?$limit[0]:0,
                    isset($limit[1])?$limit[1]:15
                );
            }
        }

        return $list;
    }

    /**
     * @param int $cacheLimit
     * @return $this
     */
    public function setCacheLimit(int $cacheLimit)
    {
        $this->cacheLimit = $cacheLimit;

        return $this;
    }



}