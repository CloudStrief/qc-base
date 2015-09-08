<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use Yii;

/**
 * 权限子父关系模型，对应表"{{%auth_item_child}}"
 *
 * @property string $parent 父级权限项
 * @property string $child 子级权限项
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class AuthItemChild extends yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item_child}}';
    }
}
