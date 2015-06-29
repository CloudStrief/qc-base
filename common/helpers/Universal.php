<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\helpers;

use Yii;

/**
 * 通用函数库
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class Universal
{
    /**
     * 获取调用`callable`类型变量的结果值
     *
     * 有些配置性质的参数我们不希望直接调用函数返回即时结果，而是希望在运行时真正使用到配置的值
     * 的时候再动态调用获取配置值，这个时候我们要求配置参数为如下类似的`callable`类型：
     *
     * - Closure 匿名函数
     * - Callback 回调类型，如全局函数、对象方法、静态方法等
     * - array 增加参数的扩展回调类型，如`[['common\models\Test', 'test'], ['arg1', 'arg2']]`，
     *   数组下标0为Callback类型数据，下标1为调用函数的参数数组
     *
     * @return mixed 如果是合法的可调用类型则返回调用后的值，否则返回原参数值
     */
    public static function getCallableValue($callable)
    {
        if (is_callable($callable)) {
            return call_user_func($callable);
        }
        elseif (is_array($callable) && isset($callable[0]) && is_callable($callable[0])) {
            $args = (isset($callable[1])) ? $callable[1] : [];
            return call_user_func_array($callable[0], $args);
        }
        else {
            return $callable;
        }
    }
}
