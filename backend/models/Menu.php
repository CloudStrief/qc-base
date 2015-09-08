<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace backend\models;

use Yii;
use common\models\ActiveRecord;
use common\models\App;
use common\rbac\Auth;
use common\behaviors\AttributeBehavior;
use common\behaviors\TreeBehavior;
use common\behaviors\TreeWaveBehavior;
use common\helpers\Tree;

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
    public function init() 
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'tree' => [
                'class' => TreeBehavior::className(),
                'showAttrName' => 'name',
            ],
            'treeWave' => [
                'class' => TreeWaveBehavior::className(),
                'topDownAttrs' => ['status' => self::STATUS_HIDDEN],
                'bottomUpAttrs' => ['status' => self::STATUS_SHOW],
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => [
                        'create_time' => time(),
                        'create_user_id' => Yii::$app->user->id,
                        'auth_item' => [
                            'handle' => 'join',
                            'args' => [
                                'joinAttributes' => ['app', 'module', 'controller', 'action'],
                            ],
                            'when' => function ($model, $event) {
                                return $model->type == self::AUTH_MENU;
                            }
                        ],
                        'auth_rules' => [
                            'handle' => 'implode',
                        ],
                    ],
                    self::EVENT_BEFORE_UPDATE => [
                        'auth_item' => [
                            'handle' => 'join',
                            'args' => [
                                'joinAttributes' => ['app', 'module', 'controller', 'action'],
                            ],
                            'when' => function ($model, $event) {
                                return $model->type == self::AUTH_MENU;
                            }
                        ],
                        'auth_rules' => [
                            'handle' => 'implode',
                        ],
                    ],
                    self::EVENT_AFTER_FIND => [
                        'auth_rules' => [
                            'handle' => 'explode',
                        ],
                    ],

                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'module', 'controller', 'action', 'params'], 'trim'],
            ['name', 'unique'],
            [['name', 'app', 'status', 'type'], 'required'],
            [['module', 'controller', 'action'], 'required', 'when' => function ($model) {
                return $model->type == self::AUTH_MENU;
            }, 'whenClient' => "function (attribute, value) {
                return $('#menu-type').val() == ". self::AUTH_MENU .";
            }"],
            [['parent_id', 'status', 'sort', 'type', 'create_time', 'create_user_id'], 'integer'],
            [['parent_ids', 'child_ids', 'remark'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 50],
            [['app'], 'string', 'max' => 20],
            [['module', 'controller', 'action', 'params'], 'string', 'max' => 30],
            //[['auth_rules'], 'string', 'max' => 100],
            [['auth_rules', 'auth_item'], 'safe'],
        ];
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

    /**
     * @inheritdoc
     */
    public function searchAttributes()
    {
        return [
            'name' => [
                'type' => 'keywords',
            ],
            'type' => [
                'type' => 'map',
                'items' => [static::className(), 'getTypeItems'],
            ],
            'status' => [
                'type' => 'map',
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
            'sort' => [
                'width' => '5%',
                'handle' => 'batchSort',
            ],
            'menu_id' => [
                'width' => '5%',
            ],
            'name' => [
                'width' => '40%',
            ],
            'type' => [
                'width' => '10%',
                'handle' => 'map',
                'args' => ['items' => [static::className(), 'getTypeItems']],
            ],
            'auth_item' => [
                'width' => '15%',
            ],
            'status' => [
                'width' => '5%',
                'handle' => 'map',
                'args' => [
                    'items' => [static::className(), 'getStatusItems'],
                    'colors' => [self::STATUS_SHOW => 'green', self::STATUS_HIDDEN => 'red'],
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
                    'name' => [
                        'type' => 'text',
                    ],
                    'parent_id' => [
                        'type' => 'tree',
                        'pkAttrName' => 'menu_id',
                        'showAttrName' => 'name',
                        'items' => function () {
                            return static::find()->asArray()->all();
                        },
                        'topItem' => [0 => '顶级菜单'],
                    ],
                    'app' => [
                        'type' => 'dropDown',
                        'items' => [App::className(), 'getAppsItems'],
                    ],
                    'module' => [
                        'type' => 'text',
                    ],
                    'controller' => [
                        'type' => 'text',
                    ],
                    'action' => [
                        'type' => 'text',
                    ],
                    'params' => [
                        'type' => 'text',
                        'hint' => '额外附加到生成地址的参数，比如a=1&b=2',
                    ],
                    'sort' => [
                        'type' => 'text',
                    ],
                    'type' => [
                        'type' => 'dropDown',
                        'items' => [static::className(), 'getTypeItems'],
                    ],
                    'auth_rules' => [
                        'type' => 'checkbox',
                        'items' => [Auth::className(), 'getRuleItems'],
                    ],
                    'remark' => [
                        'type' => 'textarea',
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
     * 返回状态列表
     */
    public static function getStatusItems()
    {
        return [
            self::STATUS_SHOW => '显示',
            self::STATUS_HIDDEN => '隐藏',
        ];
    }

    /**
     * 返回菜单类型列表
     */
    public static function getTypeItems()
    {
        return [
            self::NORMAL_MENU => '常规菜单',
            self::AUTH_MENU => '权限菜单',
        ];
    }

}
