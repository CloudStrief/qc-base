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
 * 多选框控件
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class CheckboxControl extends Control
{
    /**
     * @inheritdoc
     */
    protected $defaultOptions = ['class' => 'switch_list'];
    /**
     * @var Callable 选项值
     * @see \common\helpers\Universal::getCallableValue()
     */
    public $items;

    /**
     * @inheritdoc
     */
    public function init() 
    {
        parent::init();

        //参数异常判断，方便调试
        if (empty($this->items)) {
            throw new InvalidParamException('属性' . $this->attribute . '的多选框控件的items选项值为空！');
        }
        $this->items = Universal::getCallableValue($this->items);
    }

    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        if ($this->form !== null && $this->model !== null) {
            return $this->form->field($this->model, $this->attribute)->hint($this->hint)->checkboxList($this->items, $this->options);
        }

        if ($this->model !== null) {
            return Html::activeCheckboxList($this->model, $this->attribute, $this->items, $this->options);
        }

        return Html::checkboxList($this->name, $this->value, $this->items, $this->options);
    }

    /**
     * @inheritdoc
     */
    public function renderValue()
    {
        $attribute = $this->attribute;
        $values = ($this->model !== null) ? $this->model->$attribute : $this->value;
        if (empty($values)) {
            return '';
        }

        $valueLabels = [];
        foreach ($values as $value) {
            $valueLabels[] = isset($this->items[$value]) ? $this->items[$value] : $value;
        }

        return \implode(',', $valueLabels);
    }
}
