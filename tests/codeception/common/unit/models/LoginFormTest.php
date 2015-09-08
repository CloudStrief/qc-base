<?php

namespace tests\codeception\common\unit\models;

use Yii;
use tests\codeception\common\unit\DbTestCase;
use Codeception\Specify;
use common\models\LoginForm;
use tests\codeception\common\fixtures\UserFixture;

/**
 * 后台登录测试
 */
class LoginFormTest extends DbTestCase
{

    use Specify;

    public function setUp()
    {
        parent::setUp();

        Yii::configure(Yii::$app, [
            'components' => [
                'user' => [
                    'class' => 'yii\web\User',
                    'identityClass' => 'common\models\User',
                ],
            ],
        ]);
    }

    protected function tearDown()
    {
        Yii::$app->user->logout();
        parent::tearDown();
    }

    /**
     * 测试使用没有的用户登录
     */
    public function testLoginNoUser()
    {
        $model = new LoginForm([
            'username' => 'no_user',
            'password' => 'no_password',
        ]);

        $this->specify('用户将不会登录，因为没有此用户', function () use ($model) {
            expect('login方法应该返回false', $model->login())->false();
            expect('用户应该没有登录进来', Yii::$app->user->isGuest)->true();
        });
    }

    /**
     * 测试使用错误的密码登录
     */
    public function testLoginWrongPassword()
    {
        $model = new LoginForm([
            'username' => 'admin',
            'password' => 'wrong_password',
        ]);

        $this->specify('因为使用错误的密码，用户将不会登录成功', function () use ($model) {
            expect('login方法将返回false', $model->login())->false();
            expect('密码错误信息应该被设置', $model->errors)->hasKey('password');
            expect('用户应该没有登录进来', Yii::$app->user->isGuest)->true();
        });
    }

    public function testLoginCorrect()
    {

        $model = new LoginForm([
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $this->specify('用户应该正常登录', function () use ($model) {
            expect('login方法应该返回true', $model->login())->true();
            expect('密码错误信息应该不被设置', $model->errors)->hasntKey('password');
            expect('用户应该登录成功', Yii::$app->user->isGuest)->false();
        });
    }

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@tests/codeception/common/unit/fixtures/data/models/user.php'
            ],
        ];
    }

}
