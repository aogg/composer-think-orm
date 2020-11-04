<?php
/**
 * Created by PhpStorm.
 * User: aogg
 * Date: 2020-02-29
 * Time: 12:16
 */

namespace aogg\think\orm\traits;

/**
 * 更新失败就抛出异常
 */
trait UpdateThrowTPTrait
{
    /**
     * update的返回值
     *
     * @var int
     */
    protected $updateResult =  0;

    /**
     * 更新失败就抛出异常
     *
     * @param array $data
     * @param array $where
     * @param array $allowField
     * @return $this
     */
    public static function updateThrow(array $data, $where = [], array $allowField = [])
    {
        $bool = static::update($data, $where, $allowField);
        if (is_int($bool) && !$bool) { // 更新失败
            \think\facade\Db::rollback();
            \think\facade\Log::info('数据已改变导致更新失败，请刷新', array_merge([
                'class_name' => static::class
            ], func_get_args()));

            app_json()->throw(app_json()->make(401, '数据已改变导致更新失败，请刷新'));
        }else if (
            is_object($bool) && method_exists($bool, 'getUpdateResult') &&
            empty($bool->getUpdateResult())
        ){  // 更新失败
            \think\facade\Db::rollback();
            /** @var \think\db\BaseQuery $bool */
            \think\facade\Log::info('数据已改变导致更新失败，请刷新' . var_export(array_merge([
                    'class_name' => static::class,
                    'sql' => $bool->getLastSql(),
                ], func_get_args()), true));

            app_json()->throw(app_json()->make(401, '数据已改变导致更新失败，请刷新'));
        }

        return $bool;
    }

    /**
     * 执行更新并返回bool
     * 成功返回true
     *
     * @param array $data
     * @param array $where
     * @param array $allowField
     * @return bool
     */
    public static function updateBool(array $data, $where = [], array $allowField = [])
    {        $bool = static::update($data, $where, $allowField);
        if (is_int($bool) && !$bool) { // 更新失败
            return false;
        }else if (
            is_object($bool) && method_exists($bool, 'getUpdateResult') &&
            empty($bool->getUpdateResult())
        ){  // 更新失败
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getUpdateResult(): int
    {
        return $this->updateResult;
    }

    protected function checkResult($result): void
    {
        $this->updateResult = $result;
        parent::checkResult($result);
    }
}