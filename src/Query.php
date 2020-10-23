<?php
/**
 * User: aozhuochao
 * Date: 2020/10/22
 */

namespace aogg\think\orm;


class Query extends \think\db\Query
{

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
        
        $mysqlConnection = $this->getConnection();

        if ($mysqlConnection instanceof Mysql) {
            $mysqlConnection->setCacheLimit($limit);
        }
        

        return $this;
    }



}