<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use Yii;
use common\models\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\IdentityTrait;
use common\behaviors\AttributeBehavior;
use common\behaviors\RoleBehavior;

/**
 * 用户模型，对应表"{{%user}}"
 *
 * @property integer $used_id 用户ID
 * @property string $username 用户名
 * @property string $email 邮箱
 * @property string $auth_key 身份验证密钥,保证cookie安全
 * @property string $password_hash 加盐的密码
 * @property string $password_reset_token 重置密码token
 * @property integer $status 状态,启用为1禁用为0
 * @property integer $login_times 登录次数
 * @property integer $login_error_times 登录失败次数
 * @property integer $last_login_ip 最后登录ip地址
 * @property integer $last_login_time 最后登录时间
 * @property integer $last_modify_password_time 最后修改密码时间
 * @property integer $create_time 创建时间
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class User extends ActiveRecord implements IdentityInterface
{
    use IdentityTrait;
    /**
     * @var string 获取用户输入的密码
     */
    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        //新增生成密码
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'generatePassword']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            RoleBehavior::className(),
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['create_time' => function ($event) {
                        return time();
                    }],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     *
     * 用户模型共有3个场景:
     *
     * - 新增用户:必须要填写密码
     * - 编辑用户:可以不填写密码
     * - 修改密码:只要激活用户和密码字段
     */
    public function scenarios()
    {
        $scenario = parent::scenarios();
        $scenario['create'] = ['username', 'password', 'email', 'status', 'roles'];
        $scenario['update'] = ['username', 'email', 'status', 'roles'];
        $scenario['resetPwd'] = ['username', 'password'];
        return $scenario;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'trim'],
            [['username', 'email', 'status', 'roles'], 'required'],
            [['username', 'email'], 'unique'],
            ['email', 'email'],
            ['sort', 'number'],
            ['password', 'required', 'on' => ['create', 'resetPwd']],
            ['password', 'string', 'min' => 6, 'max' => 32],
            [['frontend_user_id', 'status', 'login_times', 'login_error_times', 'last_login_ip', 'last_login_time', 'last_modify_password_time', 'create_time'], 'integer'],
            [['username', 'email', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ID',
            'username' => '用户名',
            'email' => '邮箱',
            'auth_key' => '身份验证密钥,保证cookie安全',
            'password' => '密码',
            'password_hash' => '加盐的密码',
            'password_reset_token' => '重置密码token',
            'status' => '状态',
            'login_times' => '登录次数',
            'login_error_times' => '登录失败次数',
            'last_login_ip' => '最后登录ip地址',
            'last_login_time' => '最后登录时间',
            'last_modify_password_time' => '最后修改密码时间',
            'create_time' => '创建时间',
            'sort' => '排序',
            'roles' => '所属角色',
        ];
    }

    /**
     * @inheritdoc
     */
    public function searchAttributes()
    {
        return [
            'username' => [
                'type' => 'keywords',
                'label' => '用户名',
            ],
            'email' => [
                'type' => 'keywords',
                'label' => '邮箱',
            ],
            'status' => [
                'type' => 'map',
                'label' => '状态',
                'items' => [static::className(), 'getStatusItems'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function listAttributes()
    {
        return [
            'pk' => [
                'width' => '3%',
                'handle' => 'batchDelete',
            ],
            'sort' => [
                'width' => '3%',
                'handle' => 'batchSort',
            ],
            'user_id' => [
                'width' => '3%',
            ],
            'username' => [
                'width' => '5%',
            ],
            'roles' => [
                'width' => '15%',
                'handle' => 'implode',
            ],
            'email' => [
                'width' => '10%',
            ],
            'login_times' => [
                'label' => '登录次数/错误次数',
                'width' => '10%',
                'handle' => 'join',
                'args' => [
                    'default' => '未登录过', 
                    'joinAttributes' => ['login_error_times'],
                    'containsSelf' => true,
                ],
            ],
            'last_login_time' => [
                'label' => '最后登录时间/IP',
                'width' => '10%',
                'handle' => function ($attribute, $model) {
                    return empty($model->$attribute) ? '未登录过' : date('Y-m-d H:i:s', $model->$attribute) . '/' . $model->last_login_ip;
                },
            ],
            'last_modify_password_time' => [
                'width' => '10%',
                'handle' => 'date',
                'args' => ['default' => '未修改过'],
            ],
            'create_time' => [
                'width' => '10%',
                'handle' => 'date',
                'args' => ['default' => '未知'],
            ],
            'status' => [
                'width' => '5%',
                'handle' => 'map',
                'args' => [
                    'items' => [static::className(), 'getStatusItems'],
                    'colors' => [self::STATUS_ENABLE => 'green', self::STATUS_DISABLE => 'red'],
                ],
            ],
            'operation' => [
                'label' => '操作',
                'width' => '10%',
                'handle' => 'operation',
            ],
        ];

    }

    /**
     * @inheritdoc
     */
    public function controlAttributes()
    {
        return [
            [
                'id' => 'basic',
                'name' => '基本属性',
                'attributes' => [
                    'username' => [
                        'type' => 'text',
                    ],
                    'password' => [
                        'type' => 'password',
                        'hint' => '新增用户时必须填写密码，修改用户时如果为空则不修改，否则则修改密码。',
                    ],
                    'roles' => [
                        'type' => 'dropDown',
                        'items' => [Role::className(), 'getTreeItems'],
                        'options' => ['class' => 'select_5', 'multiple' => true],
                    ],
                    'email' => [
                        'type' => 'text',
                    ],
                    'status' => [
                        'type' => 'radio',
                        'items' => [static::className(), 'getStatusItems'],
                    ],
                ]
            ]
        ];
    }

    /**
     * 生成密码
     *
     * @param Event $event 模型事件
     */
    public function generatePassword($event)
    {
        if (!empty($this->password)) {
            $this->setPassword($this->password);
            //更新修改密码时间
            if ($this->scenario == 'update') {
                $this->last_modify_password_time = time();
            }
        }

        $this->generateAuthKey();
    }

}
