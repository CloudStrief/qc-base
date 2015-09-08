<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\controls;

use Yii;
use yii\helpers\Html;
use common\helpers\Universal;
use common\helpers\Tree;
use yii\base\InvalidParamException;

/**
 * 树形结构组件
 *
 * 主要用于渲染树形结构的数据呈现方式，默认以带有属性结构的下拉框来展现
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class TreeControl extends DropDownControl
{
    /**
     * @inheritdoc
     */
    protected $defaultOptions = ['class' => 'select_5', 'encodeSpaces' => true];
    /**
     * @var string 展现树形结构的属性字段，主要用于附加空格间隔来展现树形结构，一般是模型的名称或者标题字段
     */
    public $showAttrName;
    /**
     * @var string 主键属性字段
     */
    public $pkAttrName;
    /**
     * @var string 父级属性字段
     */
    public $parentAttrName = 'parent_id';
    /**
     * @var array 额外设置的顶级元素
     */
    public $topItem;

    /*
     * @inheritdoc
     */
    public function init() 
    {
        if ($this->showAttrName === null || $this->pkAttrName === null || $this->parentAttrName === null) {
            throw new InvalidParamException('树形结构组件缺失必要属性！');
        }
        parent::init();
        $this->itemsHandle();
    }

    /**
     * 数据选项值树形处理，主要把选项值转换为树形结构
     */
    public function itemsHandle()
    {
        $this->items = Tree::getTreeList($this->items, $this->pkAttrName, $this->parentAttrName);
        $items = [];
        foreach ($this->items as $item) {
            $items[$item[$this->pkAttrName]] = (isset($item['_spacer']) ? $item['_spacer'] : '') . $item[$this->showAttrName];
        }
        if ($this->topItem !== null) {
            $items = $this->topItem + $items;
        }
        $this->items = $items;
    }
}
