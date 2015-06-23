<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\controls;

use Yii;
use yii\helpers\Html;
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
    protected $defaultHtmlOptions = ['class' => 'select_2'];
    /**
     * @var array 选项值
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
    }

    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        if ($this->form !== null && $this->model !== null) {
            return $this->form->field($this->model, $this->attribute)->hint($this->hint)->dropDownList($this->items, $this->htmlOptions);
        }

        if ($this->model !== null) {
            return Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->htmlOptions);
        }

        if (empty($this->htmlOptions['multiple'])) {
            return Html::dropDownList($this->name, $this->value, $this->items, $this->htmlOptions);
        }
        else {
            return Html::listBox($this->name, $this->value, $this->items, $this->htmlOptions);
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
