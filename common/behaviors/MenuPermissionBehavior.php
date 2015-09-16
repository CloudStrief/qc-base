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
use yii\db\ActiveRecord;

/**
 * RBAC权限菜单行为
 *
 * 为实现此行为的菜单模型增加RBAC权限节点操作
 *
 * ~~~
 * public function behaviors()
 * {
 *     return [
 *             MenuPermission::className(),
 *     ];
 * }
 * ~~~
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class MenuPermission extends Behavior
{
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'assign',
            ActiveRecord::EVENT_AFTER_UPDATE => 'reAssign',
            ActiveRecord::EVENT_AFTER_DELETE => 'revokeAll',
        ];
    }

    public function syncPermission($event)
    {
        $model = $this->owner;
        if (!$model->type == Menu::AUTH_MENU) {
            return ;
        }
        if (empty($model->auth_item)) {
        }
        if ($event->name == ActiveRecord::EVENT_AFTER_INSERT) {
            $this->addPermission();
        }

    }

    /**
     * 新增权限节点
     */
    protected function addPermission()
    {
    }

}
