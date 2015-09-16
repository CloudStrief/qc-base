<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use Yii;
use common\models\ActiveRecord;

/**
 * 菜单模型，对应表"{{%menu}}".
 *
 * @property integer $menu_id 菜单ID
 * @property string $name 菜单名称
 * @property integer $parent_id 父级ID
 * @property string $parent_ids 所有父级ID
 * @property string $child_ids 所有子级ID
 * @property string $app APP名称
 * @property string $module 模块名称
 * @property string $controller 控制器名称
 * @property string $action 动作名称
 * @property string $params 附加参数
 * @property string $auth_item 认证项
 * @property string $auth_rules 认证规则
 * @property integer $status 状态:显示为1,隐藏为0
 * @property integer $sort 排序
 * @property integer $type 菜单类型:只做菜单为0,菜单+权限节点为1
 * @property string $remark 备注
 * @property integer $create_time 创建时间
 * @property integer $create_user_id 创建人
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class Menu extends ActiveRecord
{
    const STATUS_SHOW = 1; //显示菜单
    const STATUS_HIDDEN = 0; //隐藏菜单

    const NORMAL_MENU = 0; //普通菜单
    const AUTH_MENU = 1; //权限菜单

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => '菜单ID',
            'name' => '名称',
            'parent_id' => '父级菜单',
            'parent_ids' => '所有父级ID',
            'child_ids' => '所有子级ID',
            'app' => '所属应用',
            'module' => '模块名称',
            'controller' => '控制器名称',
            'action' => '动作名称',
            'params' => '附加参数',
            'auth_item' => '认证节点',
            'auth_rules' => '认证规则',
            'status' => '状态',
            'sort' => '排序',
            'type' => '菜单类型',
            'remark' => '备注',
            'create_time' => '创建时间',
            'create_user_id' => '创建人',
        ];
    }
}
