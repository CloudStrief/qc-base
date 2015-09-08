<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\behaviors;

use Yii;
use Closure;
use yii\base\Behavior;
use yii\base\Event;
use common\models\Universal;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;

/**
 * 树形结构行为
 *
 * ~~~
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => AttributeBehavior::className(),
 *             'attributes' => [
 *                 ActiveRecord::EVENT_BEFORE_INSERT => [
 *                     'attribute1' => 'value1',
 *                     'attribute2' => 'value2',
 *                 ],
 *             ],
 *             'value' => function ($event) {
 *                 return 'some value';
 *             },
 *         ],
 *     ];
 * }
 * ~~~
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class TreeBehavior extends Behavior
{
    /**
     * 树形行为所依附的组件类的名称
     */
    public $modelClass;
    /**
     * @var array 要展现成树的原始数据
     */
    public $data;
    /**
     * @var string 展现树形结构的属性字段，主要用于附加空格间隔来展现树形结构，一般是模型的名称或者标题字段
     */
    public $showAttrName;
    /**
     * @var string 主键属性字段，如果为null则获取当前模型的第一个主键名称
     */
    public $pkAttrName;
    /**
     * @var string 父级属性字段
     */
    public $parentAttrName = 'parent_id';
    /**
     * @var string 子级集合属性字段
     */
    public $childSetAttrName = 'child_ids';
    /**
     * @var string 父级集合属性字段
     */
    public $parentSetAttrName = 'parent_ids';
    /**
     * @var array 当前涉及到的树形数据
     */

    private $_list;
    public $_child;

    /**
     * @inheritdoc
     */
    public function events()
    {
        //修复节点关系事件
        $events = array_fill_keys([
            ActiveRecord::EVENT_AFTER_INSERT, 
            ActiveRecord::EVENT_AFTER_UPDATE, 
            ActiveRecord::EVENT_AFTER_DELETE,
        ], 'repairTree');

        //删除检测是否含有子集事件
        $events = $events + [ActiveRecord::EVENT_BEFORE_DELETE => 'checkHasChild'];

        return $events;
    }

    /**
     * @inheritdoc
     */
    public function attach($owner) 
    {
        parent::attach($owner);
        $this->treeInit();
    }

    /**
     * 树行为初始化
     */
    private function treeInit()
    {
        if ($this->showAttrName === null) {
            throw new InvalidConfigException('未设置展现树形结构的属性字段');
        }
        $model = $this->owner;
        //获取组件类的名称
        if ($this->modelClass === null) {
            $this->modelClass = get_class($model);
        }
        $modelClass = $this->modelClass;
        //获取主键字段名称
        if ($this->pkAttrName === null) {
            $pks = $modelClass::primaryKey();
            $this->pkAttrName = \array_shift($pks);
        }
    }

    /**
     * 修复当前更新的树的子父级关系
     *
     * @param Event $event 需要修复树结构的事件
     */
    public function repairTree($event)
    {
        $model = $this->owner;
        $modelClass = $this->modelClass;
        $eventName = $event->name;
        $pkAttrName = $this->pkAttrName;
        $parentAttrName = $this->parentAttrName;
        $childSetAttrName = $this->childSetAttrName;
        $parentSetAttrName = $this->parentSetAttrName;
        $changedAttributes = isset($event->changedAttributes) ? $event->changedAttributes : [];
        $query = $modelClass::find();
        //获取树形数据
        if ($this->data === null) {
            $this->data = $modelClass::find()->indexBy($this->pkAttrName)->asArray()->all();
        }

        //根据操作的不同取得所有波及到的任务来更新树结构
        if ($eventName == 'afterInsert' || $eventName == 'afterDelete') {
            if ($model->$parentAttrName) {
                $topParentItem = $this->getTopParent($model);
                $inIds = explode(',', $topParentItem[$childSetAttrName]);
                $inIds[] = $model[$pkAttrName];
                $query->where([$pkAttrName => $inIds]);
            } else {
                $query->where([$pkAttrName => $model[$pkAttrName]]);
            }
        } elseif ($eventName == 'afterUpdate') {
            //判断父级有没有改变,若未变则返回
            if (!array_key_exists($parentAttrName, $changedAttributes) || $model[$parentAttrName] == $changedAttributes[$parentAttrName]) {
                $topParentItem = $this->getTopParent($model);
                $inIds = explode(',', $topParentItem[$childSetAttrName]);
                $query->where([$pkAttrName => $inIds]);
            } else {
                //取得原来父级的顶级父级
                $oldData = clone $model; //注意！这里的model是对象，必须复制一份而不能赋值
                $oldData[$parentAttrName] = $changedAttributes[$parentAttrName];
                $oldTopParentItem = $this->getTopParent($oldData);
                //取得最新父级的顶级任务
                $newTopParentItem = $this->getTopParent($model);

                //如果顶级相同则取其一
                if ($oldTopParentItem[$pkAttrName] == $newTopParentItem[$pkAttrName]) {
                    $inIds = explode(',', $oldTopParentItem[$childSetAttrName]);
                    $inIds[] = $model[$pkAttrName];
                    $query->where([$pkAttrName => $inIds]);
                } else {
                    $oldInIds = explode(',', $oldTopParentItem[$childSetAttrName]);
                    $newInIds = explode(',', $newTopParentItem[$childSetAttrName]);
                    $inIds = array_merge($oldInIds, $newInIds);
                    $query->where([$pkAttrName => $inIds]);
                }
            }
        }

        $this->_list = $query
            ->asArray()
            ->indexBy($pkAttrName)
            ->orderBy([$pkAttrName => SORT_ASC])
            ->all();

        if (is_array($this->_list)) {
            $command = Yii::$app->db->createCommand();

            //这里要先更新父级ID集,然后在更新子集，因为子集的选择需要依赖父级字段，且会有顺序问题，
            //而顺序在许多情况下是不可控的，所以只能等待所有父级全部获取成功后才去更新子级
            foreach ($this->_list as $id => $value) {
                $this->_list[$id][$parentSetAttrName] = $this->getParentIds($id);
            }
            foreach ($this->_list as $id => $value) {
                $parentIds = $this->_list[$id][$parentSetAttrName];
                $this->_list[$id][$childSetAttrName] = $childIds = $this->getChildIds($id);

                $command->update($modelClass::tableName(), [$parentSetAttrName => $parentIds, $childSetAttrName => $childIds], "$pkAttrName = :id", [':id' => $id])->execute();
            }
        }
    }

    /**
     * 获取顶级父级元素
     *
     * @param array|object 当前操作的元素
     * @return array 当前元素的最顶级元素
     */
    public function getTopParent($item)
    {
        $data = $this->data;
        $modelClass = $this->modelClass;
        $parentAttrName = $this->parentAttrName;
        $parentSetAttrName = $this->parentSetAttrName;

        if (empty($item) || $item[$parentAttrName] == 0) {
            return $item;
        }
        $parentItem = $data[$item[$parentAttrName]];

        if ($parentItem[$parentAttrName]) {
            $parentIds = explode(',', $parentItem[$parentSetAttrName]);
            $topParentId = $parentIds[1];
            $topParentItem = $data[$topParentId];
        } else {
            $topParentItem = $parentItem;
        }
        return $topParentItem;
    }

    /**
     * 获取父级ID集合
     * 
     * @param integer $id 当前记录的ID
     * @param string $parentIds 逗号相隔的父级ID集合
     * @return string $parentIds
     */
    protected function getParentIds($id, $parentIds = '')
    {
        $list = $this->_list;
        if (!is_array($list) || !isset($list[$id])) return false;
        $parentId = $list[$id][$this->parentAttrName];
        $parentIds = $parentIds ? $parentId . ',' . $parentIds : $parentId;
        if ($parentId) {
            $parentIds = $this->getParentIds($parentId, $parentIds);
        } else {
            $list[$id][$this->parentSetAttrName] = $parentIds;
        }
        return $parentIds;
    }

    /**
     * 获取子级ID集合
     * 
     * @param integer $id 当前记录的ID
     * @return string $childIds 逗号相隔的父级ID集合
     */
    protected function getChildIds($id)
    {
        $list = $this->_list;
        $childIds = $id;
        if (is_array($list)) {
            foreach ($list as $itemId => $item) {
                if ($item[$this->parentAttrName] && $id != $itemId) {
                    $parentIds = explode(',', $item[$this->parentSetAttrName]);
                    if (in_array($id, $parentIds)) {
                        $childIds .= ',' . $itemId;
                    }
                }
            }
        }
        return $childIds;
    }

    /**
     * 检测是否含有子集，如果有子集则不允许操作
     */
    public function checkHasChild($event)
    {
        if ($this->hasChild()) {
            throw new ForbiddenHttpException('此项含有子集，不允许执行当前操作！');
        }
    }

    /**
     * 是否是顶级元素
     *
     * @return boolean
     */
    public function isTop()
    {
        $model = $this->owner;
        $parentAttrName = $this->parentAttrName;
        return $model->$parentAttrName == 0;
    }

    /**
     * 检测当前对象是否含有子级
     *
     * @return boolean 返回是否含有子级
     */
    public function hasChild() 
    {
        $model = $this->owner;
        $childSetAttrName = $this->childSetAttrName;
        return false !== strpos($model->$childSetAttrName, ',');
    }

    /**
     * 获取所有的子级元素
     *
     * @return array 返回所有子级AR对象
     */
    public function getChildItems()
    {
        $model = $this->owner;
        $modelClass = $this->modelClass;
        $childSetAttrName = $this->childSetAttrName;
        $childSetAttrValue = $model->$childSetAttrName;
        if (!empty($childSetAttrValue)) {
            return $modelClass::findAll(explode(',', $childSetAttrValue));
        }
        else {
            return false;
        }
    }

    /**
     * 获取所有的父级元素
     *
     * @return array 返回所有父级AR对象
     */
    public function getParentItems()
    {
        $model = $this->owner;
        $modelClass = $this->modelClass;
        $parentSetAttrName = $this->parentSetAttrName;
        $parentSetAttrValue = $model->$parentSetAttrName;
        if (!empty($parentSetAttrValue)) {
            return $modelClass::findAll(explode(',', $parentSetAttrValue));
        }
        else {
            return false;
        }
    }
}
