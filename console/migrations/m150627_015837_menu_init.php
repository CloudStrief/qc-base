<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

use yii\db\Schema;
use yii\db\Migration;

/**
 * 菜单初始化迁移
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class m150627_015837_menu_init extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM';
        }

        $this->createTable('{{%menu}}', [
            'menu_id' => Schema::TYPE_PK . ' COMMENT \'菜单ID\'',
            'name' => Schema::TYPE_STRING . '(50) NOT NULL DEFAULT \'\' COMMENT \'菜单名称\'',
            'parent_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT \'父级ID\'',
            'parent_ids' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\' COMMENT \'所有父级ID\'',
            'child_ids' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\' COMMENT \'所有子级ID\'',
            'app' => Schema::TYPE_STRING . '(20) NOT NULL DEFAULT \'\' COMMENT \'APP名称\'',
            'module' => Schema::TYPE_STRING . '(30) NOT NULL DEFAULT \'\' COMMENT \'模块名称\'',
            'controller' => Schema::TYPE_STRING . '(30) NOT NULL DEFAULT \'\' COMMENT \'控制器名称\'',
            'action' => Schema::TYPE_STRING . '(30) NOT NULL DEFAULT \'\' COMMENT \'动作名称\'',
            'params' => Schema::TYPE_STRING . '(30) NOT NULL DEFAULT \'\' COMMENT \'附加参数\'',
            'auth_item' => Schema::TYPE_STRING . '(64) NOT NULL DEFAULT \'\' COMMENT \'认证项\'',
            'auth_rules' => Schema::TYPE_STRING . '(100) NOT NULL DEFAULT \'\' COMMENT \'认证规则\'',
            'status' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 1 COMMENT \'状态:显示为1,隐藏为0\'',
            'sort' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT \'排序\'',
            'type' => Schema::TYPE_SMALLINT . '(1) NOT NULL DEFAULT 0 COMMENT \'菜单类型:只做菜单为0,菜单+权限节点为1\'',
            'remark' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\' COMMENT \'备注\'',
            'create_time' => Schema::TYPE_INTEGER . '(10) NOT NULL DEFAULT 0 COMMENT \'创建时间\'',
            'create_user_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT \'创建人\'',

        ], $tableOptions . ' COMMENT=\'菜单表\'');

    }

    public function down()
    {
        $this->dropTable('{{%menu}}');
    }
}
