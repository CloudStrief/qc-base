<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\controls;

use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use common\helpers\Universal;
use yii\base\InvalidParamException;

/**
 * 多行文本框控件
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class TextareaControl extends Control
{
    /**
     * @inheritdoc
     */
    protected $defaultOptions = ['class' => 'length_5'];
    /**
     * @var integer 查看时截取长度
     */
    public $truncateLength = 30;

    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        if ($this->form !== null && $this->model !== null) {
            return $this->form->field($this->model, $this->attribute)->hint($this->hint)->textarea($this->options);
        }

        if ($this->model !== null) {
            return Html::activeTextarea($this->model, $this->attribute, $this->options);
        }

        return Html::textarea($this->name, $this->value, $this->options);
    }

    /**
     * @inheritdoc
     */
    public function renderValue()
    {
        $attribute = $this->attribute;
        $value = ($this->model !== null) ? $this->model->$attribute : $this->value;

        return StringHelper::truncate($value, $this->truncateLength);
    }
}
