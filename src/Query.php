<?php
/**
 * User: aogg
 * Date: 2020/10/22
 */

namespace aogg\think\orm;


class Query extends \think\db\Query
{
    protected $cacheLimit = 0;

    /**
     * 查询缓存
     * 支持分页
     *
     * @access public
     * @param mixed             $limit
     * @param mixed             $key    缓存key
     * @param integer|\DateTime $expire 缓存有效期
     * @param string            $tag    缓存标签
     * @return $this
     */
    public function cacheLimit($limit, $key = true, $expire = null, string $tag = null)
    {
        $this->cache($key, $expire, $tag);

        $this->cacheLimit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getCacheLimit()
    {
        return $this->cacheLimit;
    }


}