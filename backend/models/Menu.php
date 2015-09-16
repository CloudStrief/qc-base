<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace backend\models;

use Yii;
use common\models\App;
use common\rbac\Auth;
use common\behaviors\AttributeBehavior;
use common\behaviors\TreeBehavior;
use common\behaviors\TreeWaveBehavior;
use common\behaviors\MenuPermissionBehavior;
use common\helpers\Tree;

/**
 * 菜单模型，对应表"{{%menu}}".
 *
 * @see \common\models\Menu
 */
class Menu extends \common\models\Menu
{
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
            ],
            MenuPermissionBehavior::className(),
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
     * @inheritdoc
     */
    public function filterNavs()
    {
        return [
            'execFilter' => true,
            'filterAttributes' => ['app'],
            'actions' => [
                '基础应用' => ['index', 'app' => 'backend'],
                'CRM' => ['index', 'app' => 'crm'],
            ],
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
