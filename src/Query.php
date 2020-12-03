<?php
/**
 * User: aogg
 * Date: 2020/10/22
 */

namespace aogg\think\orm;

/**
 * @method Model getModel()
 */
class Query extends \think\db\Query
{
    protected $cacheLimit = 0;

    /**
     * 是否跳过全局字段
     *
     * @var bool
     */
    protected $skipGlobalField = false;

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

    /**
     * @param bool $skipGlobalField
     * @return $this
     */
    public function setSkipGlobalField(bool $skipGlobalField = true)
    {
        $this->skipGlobalField = $skipGlobalField;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSkipGlobalField(): bool
    {
        return $this->skipGlobalField || (method_exists($this->getModel(), 'isSkipGlobalField') && $this->getModel()->isSkipGlobalField());
    }


}