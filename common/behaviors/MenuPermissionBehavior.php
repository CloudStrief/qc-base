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
use common\models\Menu;
use common\exceptions\InvalidPropertyException;
use common\rbac\Auth;

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
class MenuPermissionBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_fill_keys([
            ActiveRecord::EVENT_AFTER_INSERT, 
            ActiveRecord::EVENT_AFTER_UPDATE, 
            ActiveRecord::EVENT_AFTER_DELETE,
        ], 'syncPermission');
    }

    /**
     * 同步更改权限
     *
     * @var Event $event 
     */
    public function syncPermission($event)
    {
        $model = $this->owner;
        if (!$model->type == Menu::AUTH_MENU) {
            return ;
        }
        if (empty($model->auth_item)) {
            throw new InvalidPropertyException('菜单模型认证节点属性`auth_item`为空！');
        }
        if ($event->name == ActiveRecord::EVENT_AFTER_INSERT) {
            $this->addPermission($event);
        }
        elseif ($event->name == ActiveRecord::EVENT_AFTER_UPDATE) {
            $this->updatePermission($event);
        }
        elseif ($event->name == ActiveRecord::EVENT_AFTER_DELETE) {
            $this->deletePermission($event);
        }
    }

    /**
     * 新增权限节点
     */
    protected function addPermission($event)
    {
        $auth = Yii::$app->authManager;
        $model = $this->owner;

        //增加主权限
        $permission = $auth->createPermission($model->auth_item);
        $permission->description = $model->name;
        $auth->add($permission);

        //增加附加的规则权限
        if (!empty($model->auth_rules)) {
            $authRules = explode(',', $model->auth_rules);
            foreach ($authRules as $authRule) {
                if (!$rule = $auth->getRule($authRule)) {
                    $rule = Auth::getRuleObject($authRule);
                    $auth->add($rule);
                }
                $additionalPermission = $auth->createPermission($model->auth_item . '-' . $rule->name);
                $additionalPermission->description = $permission->description . ' 附加规则：' . $rule->label;
                $additionalPermission->ruleName = $rule->name;
                $auth->add($additionalPermission);
                $auth->addChild($additionalPermission, $permission);
            }
        }
    }

    /**
     * 更新权限节点
     */
    protected function updatePermission($event)
    {
        $auth = Yii::$app->authManager;
        $model = $this->owner;
        $changedAttributes = $event->changedAttributes;

        //当权限节点有变动时才更新权限节点
        if (isset($changedAttributes['auth_item']) && $changedAttributes['auth_item'] != $model->auth_item) {
            $permission = $auth->createPermission($model->auth_item);
            $permission->description = $model->name;
            $auth->update($changedAttributes['auth_item'], $permission);

            //更新附加节点
            if (!empty($model->auth_rules)) {
                $authRules = explode(',', $model->auth_rules);
                foreach ($authRules as $authRule) {
                    if (!$rule = $auth->getRule($authRule)) {
                        $rule = Auth::getRuleObject($authRule);
                        $auth->add($rule);
                    }
                    else {
                        $rule = Auth::getRuleObject($authRule);
                        $auth->update($rule->name, $rule);
                    }
                    $additionalPermission = $auth->createPermission($model->auth_item . '-' . $rule->name);
                    $additionalPermission->description = $permission->description . ' 附加规则：' . $rule->label;
                    $additionalPermission->ruleName = $rule->name;
                    $auth->update($changedAttributes['auth_item'] . '-' . $rule->name, $additionalPermission);
                }
            }

        }
    }

    /**
     * 删除权限节点
     */
    protected function deletePermission($event)
    {
        $auth = Yii::$app->authManager;
        $model = $this->owner;

        $permission = $auth->getPermission($model->auth_item);
        $auth->remove($permission);

        if (!empty($model->auth_rules)) {
            $authRules = $model->auth_rules;
            foreach ($authRules as $authRule) {
                if (!$rule = $auth->getRule($authRule)) {
                    $rule = Auth::getRuleObject($authRule);
                    $auth->add($rule);
                }
                $additionalPermission = $auth->getPermission($model->auth_item . '-' . $rule->name);
                $auth->remove($additionalPermission);
            }
        }
    }

}
