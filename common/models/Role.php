<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use Yii;
use yii\rbac\Item;
use common\helpers\Tree;
use yii\helpers\ArrayHelper;

/**
 * 角色模型，对应表"{{%auth_item}}"
 *
 * @property string $name 名称
 * @property integer $type 类型 1为角色 2为权限节点
 * @property string $description 描述
 * @property string $rule_name 规则名称
 * @property string $data 规则数据
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 *
 * @see [[\yii\rbac\Item]]
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class Role extends AuthItem
{
    /**
     * @var string 父级角色
     */
    public $parent;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->type = Item::TYPE_ROLE;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'trim'],
            [['name'], 'required'],
            ['name', 'unique'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['parent', 'description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64]
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['parent'] = '上级角色';
        return $attributeLabels;
    }

    /**
     * @inheritdoc
     */
    public function listAttributes()
    {
        return [
            'name' => [
                'width' => '40%',
                'handle' => function ($attribute, $model) {
                    return $model['_spacer'] . $model[$attribute];
                },
            ],
            'description' => [
                'width' => '30%',
            ],
            'operation' => [
                'label' => '操作',
                'width' => '10%',
                'handle' => 'operation',
                'args' => ['actions' => [
                        ['label' => '编辑', 'url' => ['update']],
                        ['label' => '删除', 'url'=> ['delete'], 'class' => 'link-delete']
                    ]
                ],
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
                    'parent' => [
                        'type' => 'dropDown',
                        'items' => [static::className(), 'getTreeItems'],
                        'options' => ['class' => 'select_5', 'prompt' => '顶级角色'],
                    ],
                    'description' => [
                        'type' => 'textarea',
                    ],
                ]
            ]
        ];
    }

    /**
     * 获取父级角色
     */
    public function getParentItem()
    {
        return $this->hasOne(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * 获取子级角色
     */
    public function getChildItems()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * 取得包含按照等级排列的含有等级前缀角色数组
     *
     * @return array 
     */
    public static function getTreeList()
    {
        $roles = static::find()->joinWith('parentItem')->where(['type' => Item::TYPE_ROLE])->orderBy('created_at DESC')->asArray()->all();

        foreach($roles as &$role) {
            $role['parent'] = (isset($role['parentItem'])) ? $role['parentItem']['parent'] : '';
        }

        $roles = Tree::getTreeList($roles, 'name', 'parent', '_child', '');

        foreach ($roles as &$role) {
            $role['_spacer'] = isset($role['_spacer']) ? str_replace(' ', '&nbsp;', $role['_spacer']) : '';
        }

        return $roles;
    }

    /**
     * 获取角色树形结构项
     *
     * @return array 返回角色名称键值对
     */
    public static function getTreeItems()
    {
        $roles = static::getTreeList();

        foreach ($roles as $role) {
            $items[$role['name']] = str_replace('&nbsp;', ' ', $role['_spacer']) . $role['name'];
        }

        return $items;
    }
}
