<?php
/**
 * User: aogg
 * Date: 2020/10/23
 */

namespace aogg\think\orm;

/**
 * 
 * @mixin Query
 * @method \think\Paginator|$this[] paginate($listRows = null, $simple = false)
 */
class Model extends \think\Model
{
    use traits\UpdateThrowTPTrait;

    /**
     * 是否需要自动写入时间戳 如果设置为字符串 则表示时间字段的类型
     * @var bool|string
     */
    protected $autoWriteTimestamp;

    /**
     * 是否跳过全局字段
     *
     * @var bool
     */
    protected $skipGlobalField = false;

    public static function getOriginName()
    {
        return (new static)->getName();
    }

    /**
     * 静态获取主键名称
     *
     * @return array|string
     */
    public static function getPkFieldName()
    {
        return (new static)->getPk();
    }

    /**
     * @return bool
     */
    public function isSkipGlobalField()
    {
        return $this->skipGlobalField;
    }

}