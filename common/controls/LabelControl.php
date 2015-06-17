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
 * Label控件，用于生成Label标签
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class LabelControl extends Control
{
    /**
     * @var string label标签的内容
     */
    public $text;

    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        if ($this->model !== null) {
            return Html::activeLabel($this->model, $this->attribute, $this->htmlOptions);
        }

        return Html::label($this->text, null, $this->htmlOptions);
    }
}
