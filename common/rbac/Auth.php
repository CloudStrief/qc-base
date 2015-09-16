<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\rbac;

use Yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\base\NotSupportedException;

/**
 * 通用权限管理
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class Auth extends Object
{
    /**
     * 获取所有的范围规则
     */
    public static function getRules()
    {
        return [
            'selfScope' => ['class' => 'common\rbac\SelfScopeRule'],
            'subordinateScope' => ['class' => 'common\rbac\SubordinateScopeRule'],
        ];
    }

    /**
     * 返回规则选项值
     */
    public static function getRuleItems()
    {
        $rules = static::getRules();
        $items = [];
        foreach ($rules as $rule) {
            if (isset($rule['class'])) {
                $rule = new $rule['class'];
                $items[$rule->name] = $rule->label;
            }
        }
        return $items;
    }

    /**
     * 返回指定的规则对象
     *
     * @var string $name 规则的名称
     * @return ScopeRule 范围规则对象
     */
    public static function getRuleObject($name)
    {
        $rules = static::getRules();
        if (isset($rules[$name])) {
            return new $rules[$name]['class'];
        }
        else {
            throw new NotSupportedException('试图创建不支持的规则`' . $name . '`！');
        }
    }
}
