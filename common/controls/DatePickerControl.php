<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\controls;

use Yii;
use yii\helpers\Html;
use common\assets\My97DatePickerAsset;

/**
 * 日期选择控件
 *
 * @author strief <strief@u-bo.com>
 * @since 0.1
 */
class DatePickerControl extends TextControl
{
    /**
     * @var array 日期选择器的配置
     */
    public $dateConfig = [];

    /**
     * @var string 设定触发日期选择器的触发事件类型，比如onclick、onfocus
     */
    public $event = 'onfocus';

    /**
     * @var string 日期的格式 
     * 常见的几种
     * <example>
     * yyyy-MM-dd HH:mm:ss  2008-03-12 19:20:00
     * yy年M月    08年3月
     * yyyyMMdd 20080312
     * 今天是:yyyy年M年d HH时mm分  今天是:2008年3月12日 19时20分
     * H:m:s    19:20:0
     * y年   8年
     * MMMM d, yyyy 三月 12, 2008
     * </example>
     * 更多详细参数请阅读my97官方说明 http://www.my97.net/dp/demo/resource/2.2.asp
     */
    public $format = 'yyyy-MM-dd';

    /**
     * @var string 详情页日期展现格式
     */
    public $viewFormat = 'Y-m-d';

    /**
     * @var bool 输入框是否只读 
     */
    public $readOnly = false;

    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        $this->registerAsset();

        $this->options[$this->event] = 'WdatePicker(' . $this->getOptions() . ')';

        $attribute = $this->attribute;
        $this->model->$attribute = $this->renderValue();

        return parent::renderHtml();
    }

    /**
     * @inheritdoc
     */
    public function renderValue()
    {
        $attribute = $this->attribute;
        $value = ($this->model === null) ? $this->value : $this->model->$attribute;
        $format = (isset($this->dateConfig['viewFormat'])) ? $this->dateConfig['viewFormat'] : $this->viewFormat;
        return \date($format, $value);
    }

    /**
     * 注册js资源
     */
    private function registerAsset()
    {
        $view = $this->getView();
        //注册资源
        My97DatePickerAsset::register($view);
    }

    /**
     * 获取日期选项
     */
    private function getOptions()
    {
        $this->dateConfig['readOnly'] = (isset($this->dateConfig['readOnly'])) ? $this->dateConfig['readOnly'] : $this->readOnly;
        $this->dateConfig['dateFmt'] = (isset($this->dateConfig['dateFmt'])) ? $this->dateConfig['dateFmt'] : $this->format;
        $options = json_encode($this->dateConfig);
        return $options;
    }
}