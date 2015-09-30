<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\controls;

use Yii;
use yii\helpers\Html;
use common\assets\UMeditorAsset;

/**
 * UMeditor富文本编辑器控件
 *
 * @author strief <strief@u-bo.com>
 * @since 0.1
 */
class UmeditorControl extends TextareaControl
{
	/**
     * @var string 编辑器类型，支持 simple => 简版 , normal => 正常版
     */
    public $editorType = 'normal';

    /**
     * @var int 宽度 
     */
    public $width;

    /**
     * @var int 高度 
     */
    public $height = 200;

    /**
     * @var array 编辑器的配置
     */
    public $editorConfig = [
        'imagePath' => 'upload/', //图片修正地址
    ];

	/**
     * @inheritdoc
     */
    public function renderHtml()
    {
        $this->editorInit();
        $this->registerAsset();

        return parent::renderHtml();
    }

    /**
     * 编辑器初始化
     */
    public function editorInit()
    {
        if (!isset($this->editorConfig['toolbars']) && $this->editorType == 'simple') {
            //工具栏按钮
            $this->editorConfig['toolbar'] = ['fullscreen source undo redo bold italic underline'];
        }
        if (!empty($this->width)) {
            //编辑器宽度
            $this->editorConfig['initialFrameWidth'] = $this->width;
        }
        if (!empty($this->height)) {
            //编辑器高度
            $this->editorConfig['initialFrameHeight'] = $this->height;
        }
    }

    /**
     * 注册js资源
     */
    private function registerAsset()
    {
    	$id = Html::getInputId($this->model, $this->attribute);
        $view = $this->getView();
        //注册资源
        UMeditorAsset::register($view);

        $options = $this->getOptions();

        $js = <<<JS
            var options = {$options}; 
            var {$this->attribute}_editor = UM.getEditor('{$id}', options);
JS;
        $view->registerJs($js);
    }

    /**
     * 获取编辑器选项 
     */
    private function getOptions()
    {
        $options = json_encode($this->editorConfig);
        //去除函数两边的引号
        $options = preg_replace('/"(function().*?)"/', '$1', $options);

        return $options;
    }
}