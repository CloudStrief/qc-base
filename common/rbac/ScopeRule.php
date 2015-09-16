<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\rbac;

use Yii;
use yii\rbac\Rule;

/**
 * 通用范围规则
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
abstract class ScopeRule extends Rule
{
    /**
     * @var string 规则标签
     */
    public $label;
}
