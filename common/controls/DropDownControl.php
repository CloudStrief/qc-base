<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\controls;

use Yii;
use yii\helpers\Html;

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
    public $htmlClass = "select_2";
    /**
     * 选项值
     */
    public $items;

    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        if ($this->form !== null && $this->model !== null) {
            return $this->form->field($this->model, $this->attribute)->hint($this->hint)->dropDownList($this->items, $this->options);
        }

        if ($this->model !== null) {
            return Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        }

        if (empty($this->options['multiple'])) {
            return Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }
        else {
            return Html::listBox($this->name, $this->value, $this->items, $this->options);
        }
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
