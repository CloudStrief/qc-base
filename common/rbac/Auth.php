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
            ['name' => 'allScope', 'label' => '通用全部范围', 'auth_label' => '全部'],
            ['name' => 'selfScope', 'label' => '通用仅自己范围', 'auth_label' => '仅自己'],
            ['name' => 'subordinateScope', 'label' => '通用自己和下级范围', 'auth_label' => '自己和下属'],
        ];
    }

    /**
     * 返回规则选项值
     */
    public static function getRuleItems()
    {
        $rules = static::getRules();
        return ArrayHelper::map($rules, 'name', 'label');
    }
}
