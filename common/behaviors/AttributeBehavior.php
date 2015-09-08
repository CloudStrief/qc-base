<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\behaviors;

use Yii;
use Closure;
use yii\base\Behavior;
use yii\base\Event;
use common\models\Universal;
use common\helpers\AttributeHandle;

/**
 * 属性行为，主要用于在指定事件自动给属性赋值，有别于Yii内置的属性行为类，这里实现的属性行为可以单独给
 * 每个属性设置不同的值，如果不存在单独值设置时则获取统一的全局值。使用如下：
 *
 * ~~~
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => AttributeBehavior::className(),
 *             'attributes' => [
 *                 ActiveRecord::EVENT_BEFORE_INSERT => [
 *                     'attribute1' => 'value1',
 *                     'attribute2' => 'value2',
 *                 ],
 *             ],
 *             'value' => function ($event) {
 *                 return 'some value';
 *             },
 *         ],
 *     ];
 * }
 * ~~~
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class AttributeBehavior extends Behavior
{
    /**
     * @var array 需要设置的属性以及属性值，形式如下：
     *
     * ```php
     * [
     *     ActiveRecord::EVENT_BEFORE_INSERT => ['attribute1' => 'value1', 'attribute2' => 'value2'],
     * ]
     * ```
     */
    public $attributes = [];
    /**
     * @var mixed the value that will be assigned to the current attributes. This can be an anonymous function
     * or an arbitrary value. If the former, the return value of the function will be assigned to the attributes.
     * The signature of the function should be as follows,
     *
     * ```php
     * function ($event)
     * {
     *     // return value will be assigned to the attribute
     * }
     * ```
     */
    public $value;


    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_fill_keys(array_keys($this->attributes), 'evaluateAttributes');
    }

    /**
     * Evaluates the attribute value and assigns it to the current attributes.
     * @param Event $event
     */
    public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            $globalValue = ($this->value !== null) ? $this->getValue($this->value, $event) : null;
            foreach ($attributes as $attribute => $value) {
                //判断是否含有执行条件
                if (is_array($value) && isset($value['when']) && $value['when'] instanceof \Closure) {
                    if (!call_user_func($value['when'], $this->owner, $event)) {
                        continue;
                    }
                }
                $value = $this->getValue($attribute, $value, $event);
                if (is_string($attribute)) {
                    $this->owner->$attribute = $value;
                }
            }
        }
    }

    /**
     * 返回属性的值,其中在解析一个值时经历以下步骤：
     *
     * 1. 判断是否是标量，如果是直接返回标量值
     * 2. 判断是否是数组，则其下进行下面检测：
     *    - 如果存在`handle`索引(属性处理方法也存在)，则使用[[AttributeHandle]]进行处理
     *    - 是否是`callable`类型，如果是则直接调用返回
     *    - 如果不符合前两者，则直接返回数组
     * 3. 判断是否是匿名函数，如果是则直接调用返回
     *
     * @param Event $event the event that triggers the current attribute updating.
     * @return mixed the attribute value
     */
    protected function getValue($attribute, $value, $event)
    {
        if (is_scalar($value)) {
            return $value;
        }
        elseif (is_array($value)) {
            $handleClassName = 'common\helpers\AttributeHandle';
            $handleEventName = $value['handle'] . 'Event';
            if (isset($value['handle']) && method_exists($handleClassName, $handleEventName)) {
                $args = isset($value['args']) ? $value['args'] : [];
                AttributeHandle::$model = $this->owner;
                $args = [$attribute, $args];
                return call_user_func_array([$handleClassName, $handleEventName], $args);
            }
            elseif (is_callable($value)) {
                return call_user_func($value);
            }
            elseif (isset($value[0]) && is_callable($value[0])) {
                $args = (isset($value[1])) ? $value[1] : [];
                return call_user_func_array($value[0], $args);
            }
            return $value;
        }
        elseif ($value instanceof Closure) {
            return call_user_func($value, $event);
        }
    }
}
