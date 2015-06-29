<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\search;

use Yii;
use common\controls\Control;
use common\helpers\Universal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * 映射搜索组件
 *
 * 映射搜索组件主要用于明确相等关系的查询，比如查找状态为启用的所有记录，默认的展现方式
 * 是以html下拉框的形式，渲染下拉框使用`dropDown`控件，因此你需要传递`dropDown`控件所需的
 * 键值对形式的选项值。对于选项值`$items`属性可接受如下三种类型：
 *
 * 1. array 数组形式，这是最常见的形式，直接给出键值对，如
 *
 *    ```php
 *    [
 *        1 => '启用',
 *        0 => '禁用',
 *    ]
 *    ```
 *
 * 2. Closure 匿名函数，用户可以设置一个匿名函数，实现自己的逻辑来返回第一种数组形式的值，匿名函数
 *    有一个参数为当前的映射搜索组件对象，你可以从中获取必要的值，形式如下：
 *
 *    ```php
 *    function ($mapSearch) {
 *        //此处实现自定义逻辑返回键值对...
 *    }
 *    ```
 *
 * 3. string 字符串形式，如果设置为字符串，则默认此属性为需要查找的键值对的数据表名称，同时
 *    需要提供对应表里`$keyAttribute`键属性和`$valueAttribute`值属性，组件内部会直接查询数据库
 *    返回指定的键值对，如需加入查询条件，则设置`$where`属性，形式如下：
 *
 *    ```php
 *    [
 *        'items' => '{{%test}}',
 *        'keyAttribute' => 'id',
 *        'valueAttribute' => 'name',
 *        'where' => ['status' => 1],
 *    ]
 *    ```
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class MapSearch extends Search
{
    /**
     * @var Callable 映射选项值
     * @see \common\helpers\Universal::getCallableValue()
     */
    public $items;
    /**
     * @var string 键属性
     */
    public $keyAttribute;
    /**
     * @var string 值属性
     */
    public $valueAttribute;
    /**
     * @var string|array 搜索条件
     */
    public $where;
    /**
     * @var array 渲染下拉框控件的html属性
     */
    public $dropDownHtmlOptions = ['class' => 'select_2 mr10'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->items === null) {
            throw new InvalidConfigException('映射搜索组件缺少items选项值参数！是否书写错误？');
        }
        $this->items = Universal::getCallableValue($this->items);
    }

    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        //$html = Control::create('label', $this->attribute, $this->model)->renderHtml() . '&nbsp;&nbsp;';

        if (is_array($this->items)) {
            $items = $this->items;
        }
        elseif (is_string($this->items)) {
            $items = (new Query())
                ->select([$this->keyAttribute, $this->valueAttribute])
                ->from($this->items);
            if ($this->where !== null) {
                $items = $items->where($this->where);
            }
            $items = $items->all();
            $items = ArrayHelper::map($items, $this->keyAttribute, $this->valueAttribute);
        }
        elseif ($this->items instanceof \Closure) {
            $items = call_user_func($this->items, $this);
        }
        else {
            throw new InvalidConfigException('映射搜索组件' . $this->attribute . '属性items选项值参数类型错误！是否书写错误？');
        }

        $itemsLabel = '---' . $this->model->getAttributeLabel($this->attribute) . '---';
        $items = ['' => $itemsLabel] + $items;

        $html = Control::create('dropDown', $this->attribute, $this->model, null, ['items' => $items, 'htmlOptions' => $this->dropDownHtmlOptions])->renderHtml();

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function parseQuery($query)
    {
        $attribute = $this->attribute;
        $value = isset($this->model->$attribute) ? $this->model->$attribute : null;

        if ($value !== null && $value !== '') {
            $query->andWhere([$attribute => $value]);
        }
        
        return $query;
    }
}
