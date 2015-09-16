<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\rbac;

use Yii;
use common\rbac\ScopeRule;

/**
 * 通用仅自己范围规则
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class SelfScopeRule extends ScopeRule
{
    /**
     * @inheritdoc
     */
    public $name = 'selfScope';
    /**
     * @inheritdoc
     */
    public $label = '仅自己范围';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        return true;
    }
}
