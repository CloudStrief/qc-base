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
use yii\db\ActiveRecord;

/**
 * 树形波动属性处理行为
 *
 * 许多情况下，树形结构的某一节点的某一属性发生变化时，将会波动影响到相应父级或者子级的属性，这时
 * 我们就可以使用此行为来批量自动完成属性的变化。行为里可以设置由上到下波动和由下到上波动两种属性，
 * 分别代表改变所有下级指定属性和改变所有父级的指定属性，一定要注意，**附加此行为的模型一定要先实现
 * `TreeBehavior`行为**！事例如下：
 *
 * ~~~
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => TreeWaveBehavior::className(),
 *             'topDownAttrs' => ['status' => 0],
 *             'bottomUpAttrs' => ['status' => 1],
 *         ],
 *     ];
 * }
 * ~~~
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class TreeWaveBehavior extends Behavior
{
    /**
     * @var array 由上到下波动属性集合
     */
    public $topDownAttrs;
    /**
     * @var array 由下到上波动属性集合
     */
    public $bottomUpAttrs;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'waveHandle',
        ];
    }

    /**
     * 波动属性处理
     */
    public function waveHandle($event)
    {
        if ($this->topDownAttrs !== null) {
            $this->topDownWave($event);
        }
        if ($this->bottomUpAttrs !== null) {
            $this->bottomUpWare($event);
        }
    }

    /**
     * 属性由上到下波动处理
     *
     * 当满足下面三个条件时将对子级波动属性进行变更：
     *
     * 1. 当期模型含有子级
     * 2. 当前模型的波动属性有变动
     * 3. 当前模型的波动属性等于指定的属性值
     *
     * @param Event $event yii\db\AfterSaveEvent
     * @return boolean
     * @see bottomUpWare()
     */
    protected function topDownWave($event)
    {
        $model = $this->owner;
        $changedAttributes = $event->changedAttributes;
        $childSetAttrName = $model->childSetAttrName;
        $topDownAttrs = [];
        foreach ($this->topDownAttrs as $attrName => $attrValue) {
            if ($model->hasChild() && isset($changedAttributes[$attrName]) && $model->$attrName != $changedAttributes[$attrName] && $model->$attrName == $attrValue) {
                $topDownAttrs[$attrName] = $attrValue;
            }
        }
        if (!empty($topDownAttrs)) {
            $childItems = $model->getChildItems();
            return $this->batchSetAttributes($childItems, $topDownAttrs);
        }
    }

    /**
     * 属性由下到上波动处理
     *
     * @see topDownWave()
     */
    protected function bottomUpWare($event)
    {
        $model = $this->owner;
        $changedAttributes = $event->changedAttributes;
        $childSetAttrName = $model->childSetAttrName;
        $bottomUpAttrs = [];
        foreach ($this->bottomUpAttrs as $attrName => $attrValue) {
            if (!$model->isTop() && isset($changedAttributes[$attrName]) && $model->$attrName != $changedAttributes[$attrName] && $model->$attrName == $attrValue) {
                $bottomUpAttrs[$attrName] = $attrValue;
            }
        }
        if (!empty($bottomUpAttrs)) {
            $parentItems = $model->getParentItems();
            return $this->batchSetAttributes($parentItems, $bottomUpAttrs);
        }
    }

    /**
     * 批量设置模型属性
     *
     * @param array $models 要批量设置属性的对象
     * @param array $attributes 要设置的属性映射
     * @return boolean|Exception 如果执行成功则返回`true`，否则抛出异常
     */
    public function batchSetAttributes($models, $attributes)
    {
        $db = Yii::$app->db;
        $model = $this->owner;
        $modelClass = $model->modelClass;
        $tableName = $modelClass::tableName();
        $pkAttrName = $model->pkAttrName;

        foreach ($models as $model) {
            $db->createCommand()->update($tableName, $attributes, "$pkAttrName = :id", [':id' => $model->$pkAttrName])->execute();
        }
        return true;
    }
}
