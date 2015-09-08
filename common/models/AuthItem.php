<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use Yii;

/**
 * 权限项模型，对应表"{{%auth_item}}"
 *
 * @property string $name 名称
 * @property integer $type 类型 1为角色 2为权限节点
 * @property string $description 描述
 * @property string $rule_name 规则名称
 * @property string $data 规则数据
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 *
 * @see [[\yii\rbac\Item]]
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class AuthItem extends yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => '名称',
            'type' => '类型',
            'description' => '描述',
            'rule_name' => '规则名称',
            'data' => '规则数据',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

}
