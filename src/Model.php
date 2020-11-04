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
    use \app\helpers\traits\UpdateThrowTPTrait;

    /**
     * 是否需要自动写入时间戳 如果设置为字符串 则表示时间字段的类型
     * @var bool|string
     */
    protected $autoWriteTimestamp;

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

}