<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\controls;

use Yii;

/**
 * 下选框控件
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class DropDownControl extends Control
{
    /**
     * @inheritdoc
     */
    public $class = "select_2";
    /**
     * 选项值
     */
    public $items;

    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        return $this->form->field($this->model, $this->attribute)->hint($this->hint)->dropDownList($this->items, $this->options);
    }

    /**
     * @inheritdoc
     */
    public function renderValue()
    {
        $attribute = $this->attribute;
        $value = $this->model->$attribute;
        return isset($this->items[$value]) ? $this->items[$value] : $value;
    }
}
