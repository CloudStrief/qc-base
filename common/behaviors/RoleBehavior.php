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
 * RBAC角色行为
 *
 * 为实现此行为的用户模型增加RBAC角色功能
 *
 * ~~~
 * public function behaviors()
 * {
 *     return [
 *             RoleBehavior::className(),
 *     ];
 * }
 * ~~~
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class RoleBehavior extends Behavior
{
    /**
     * @var array 用户所属角色属性
     */
    private $_roles;

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

    /**
     * 分配角色给用户
     *
     * @param Event $event
     * @return boolean 是否成功
     */
    public function assign($event) 
    {
        $auth = Yii::$app->authManager;
        $model = $this->owner;
        $id = $model->getId();

        if (empty($this->roles)) {
            return false;
        }
        foreach ($this->roles as $role) {
            $role = $auth->createRole($role);
            $auth->assign($role, $id);
        }
        return true;
    }

    /**
     * 废除用户所有角色
     *
     * @param Event $event
     * @return boolean 是否成功
     */
    public function revokeAll($event)
    {
        $auth = Yii::$app->authManager;
        $model = $this->owner;
        $id = $model->getId();

        return $auth->revokeAll($id);
    }

    /**
     * 重新分配角色给用户
     *
     * @param Event $event
     * @return boolean 是否成功
     */
    public function reAssign($event)
    {
        $this->revokeAll($event);
        return $this->assign($event);
    }

    /**
     * getter方法，获取的当前用户的角色
     */
    public function getRoles()
    {
        if ($this->_roles === null) {
            $auth = Yii::$app->authManager;
            $model = $this->owner;
            $id = $model->getId();
            $roles = $auth->getRolesByUser($id);
            foreach ($roles as $role) {
                $this->_roles[] = $role->name;
            }
        }
        return $this->_roles;
    }

    /**
     * setter方法，设置当前用户的角色
     */
    public function setRoles($value)
    {
        $this->_roles = $value;
    }

}
