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
 * 文本框控件
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class TextControl extends Control
{
    /**
     * @inheritdoc
     */
    public $htmlClass = "input length_5";

    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        if ($this->form !== null && $this->model !== null) {
            return $this->form->field($this->model, $this->attribute)->hint($this->hint)->textInput($this->options);
        }

        if ($this->model !== null) {
            return Html::activeTextInput($this->model, $this->attribute, $this->options);
        }

        return Html::textInput($this->name, $this->value, $this->options);
    }

    /**
     * @inheritdoc
     */
    public function renderValue()
    {
        $attribute = $this->attribute;
        return ($this->model === null) ? $this->value : $this->model->$attribute;
    }
}
