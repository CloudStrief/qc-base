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
use yii\base\InvalidParamException;

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
    protected $defaultOptions = ['class' => 'select_2', 'encodeSpaces' => true];
    /**
     * @var Callable 选项值
     * @see \common\helpers\Universal::getCallableValue()
     */
    public $items = [];

    /**
     * @inheritdoc
     */
    public function init() 
    {
        parent::init();

        //参数异常判断，方便调试
        if (empty($this->items)) {
            throw new InvalidParamException('属性' . $this->attribute . '的下拉框控件的items选项值为空！');
        }
        $this->items = Universal::getCallableValue($this->items);
    }

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
        if (is_array($value)) {
            $valueArr = '';
            foreach ($value as $v) {
                $valueArr[] = isset($this->items[$v]) ? $this->valueFilter($this->items[$v]) : $v;
            }
            return implode(',', $valueArr);
        }
        else {
            return isset($this->items[$value]) ? $this->valueFilter($this->items[$value]) : $value;
        }
    }

    /**
     * 值过滤
     */
    private function valueFilter($value)
    {
        return str_replace([' ', '└─', '├─'], '', $value);
    }
}
