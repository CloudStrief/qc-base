<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\controls;

use Yii;
use yii\helpers\Html;
use common\assets\ChosenAsset;

/**
 * Chosen自动搜索补全控件
 *
 * @author strief <strief@u-bo.com>
 * @since 0.1
 */
class ChosenControl extends DropDownControl
{
	/**
     * @inheritdoc
     */
    protected $defaultOptions = [
    	'class' => 'select_5 chosen-select',
    	'multiple' => true,
    	'data-placeholder' => '请输入关键字'
    ];

    /**
     * @var array 自动搜索补全控件的配置
     */
    public $chosenConfig = [
        'no_results_text' => '抱歉, 当前没有搜索到任何信息！', //无搜索结果显示的文本
    ];

	/**
     * @inheritdoc
     */
    public function renderHtml()
    {
        $this->registerAsset();

        return parent::renderHtml();
    }

    /**
     * 注册js资源
     */
    private function registerAsset()
    {
        $id = Html::getInputId($this->model, $this->attribute);
        $view = $this->getView();
        //注册资源
        ChosenAsset::register($view);

        $options = $this->getOptions();

		$js = <<<JS
            $('.chosen-select').chosen({$options});
JS;
        $view->registerJs($js);
    }

    /**
     * 获取控件的配置
     */
    private function getOptions()
    {
        $options = json_encode($this->chosenConfig);
        return $options;
    }
}