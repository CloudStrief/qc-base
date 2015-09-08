<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

use yii\db\Schema;
use yii\db\Migration;

/**
 * 应用初始化迁移
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class m150627_010129_app_init extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM';
        }

        $this->createTable('{{%user}}', [
            'user_id' => Schema::TYPE_PK . ' COMMENT \'用户ID\'',
            'username' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\' COMMENT \'用户名\'',
            'email' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\' COMMENT \'邮箱\'',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL DEFAULT \'\' COMMENT \'身份验证密钥,保证cookie安全\'',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\' COMMENT \'加盐的密码\'',
            'password_reset_token' => Schema::TYPE_STRING . ' NOT NULL DEFAULT \'\' COMMENT \'重置密码token\'',

            'status' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 1 COMMENT \'状态:启用为1,禁用为0\'',
            'sort' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT \'排序\'',
            'login_times' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT \'登录次数\'',
            'login_error_times' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT \'登录失败次数\'',
            'last_login_ip' => Schema::TYPE_INTEGER . '(10) NOT NULL DEFAULT 0 COMMENT \'最后登录ip地址\'',
            'last_login_time' => Schema::TYPE_INTEGER . '(10) NOT NULL DEFAULT 0 COMMENT \'最后登录时间\'',
            'last_modify_password_time' => Schema::TYPE_INTEGER . '(10) NOT NULL DEFAULT 0 COMMENT \'最后修改密码时间\'',
            'create_time' => Schema::TYPE_INTEGER . '(10) NOT NULL DEFAULT 0 COMMENT \'创建时间\'',
            'create_user_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT \'创建人\'',
        ], $tableOptions . ' COMMENT=\'后台管理员表\'');

        //新增一个超级管理员
        $this->insert('{{%user}}', [
            'username' => 'admin',
            'email' => 'admin@u-bo.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('admin'),
            'status' => 1,
            'create_time' => time(),
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
