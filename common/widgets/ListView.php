<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\base\Widget;
use common\helpers\AttributeHandle;
use common\helpers\Tree;
use yii\base\UnknownPropertyException;
use yii\base\UnknownMethodException;
use yii\base\InvalidConfigException;

/**
 * 列表视图插件，用于渲染通用的列表页面
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class ListView extends Widget
{
    /**
     * 表格展现形式
     */
    const TABLE_VIEW = 'table';
    /**
     * 树形展现形式
     */
    const TREE_VIEW = 'tree';
    /**
     * 列表展现形式，如上常量定义支持普通表格形式和树形形式
     */
    public $showType;
    /**
     * @var ActiveRecord 当前的AR模型
     */
    public $model;
    /**
     * @var array 要显示的列表AR模型数据
     */
    public $models;
    /**
     * @var array 列表属性
     */
    public $listAttributes;
    /**
     * @var string 列表头部元素的模板
     *
     * 其中替换参数含义如下：
     *
     * - `{width}`代表头部元素宽度
     * - `{label}`代表头部元素名称
     */
    public $headerItemTemplate = '<td width="{width}">{label}</td>';
    /**
     * @var string 包含头部元素的行标签
     */
    public $headerRowTag = 'tr';
    /**
     * @var string 包含头部元素的行标签
     */
    public $headerColTag = 'td';
    /**
     * @var string 全选label模板
     */
    public $selectAllLabelTemplate = '<label><input type="checkbox" name="select_all" class="box-select-all" />全选</label>';
    /**
     * @var string 动作按钮模板
     *
     * 其中替换参数含义如下：
     *
     * - `{url}`要执行的动作URL
     * - `{label}`动作显示的名称
     */
    public $actionButtonTemplate = '<button class="{class}" data-url="{url}" type="submit" >{label}</button>';
    /**
     * @var string 包含数据的行标签
     */
    public $dataRowTag = 'tr';
    /**
     * @var string 包含数据的列标签
     */
    public $dataColTag = 'td';
    /**
     * @var Behavior 当前模型所附加的树形结构行为
     */
    private $_treeBehavior;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->model) || empty($this->listAttributes)) {
            throw new InvalidConfigException('列表视图插件的模型参数或者列表属性参数错误！');
        }

        $this->showType = $this->showType === null ? self::TABLE_VIEW : $this->showType;
        if ($this->showType != self::TABLE_VIEW && $this->showType != self::TREE_VIEW) {
            throw new InvalidConfigException('列表视图插件不支持' . $this->showType . '展现形式！');
        }

        //如果时树形视图，则需要对展示数据进行处理，加上树形等级关系
        if ($this->showType == self::TREE_VIEW) {
            $this->_treeBehavior = $this->model->getBehavior('tree');
            if ($this->_treeBehavior === null) {
                throw new InvalidConfigException('树形展现模型没有实现`tree`行为！');
            }
            $this->treeHandle();
        }

        ob_start();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $content = ob_get_clean();
        $headerItemHtml = $this->getHeaderItemHtml();
        $dataHtml = $this->getDataHtml();
        $actionButtonsHtml = $this->getActionButtonsHtml();

        return str_replace(['{header}', '{data}', '{actionButtons}'], [$headerItemHtml, $dataHtml, $actionButtonsHtml], $content);
    }

    /**
     * 获取解析后的列表头部HTML
     */
    public function getHeaderItemHtml()
    {
        $html = '';

        if (empty($this->listAttributes)) {
            return $html;
        }

        $html .= Html::beginTag($this->headerRowTag);
        foreach ($this->listAttributes as $attribute => $configs) {
            $width = isset($configs['width']) ? $configs['width'] : '5%';
            if (isset($configs['handle']) && ($configs['handle'] == 'pkBox' || $configs['handle'] == 'batchDelete')) {
                $label = $this->selectAllLabelTemplate;
            }
            else {
                $label = isset($configs['label']) ? $configs['label'] : $this->model->getAttributeLabel($attribute);
            }

            $html .= Html::beginTag($this->headerColTag, ['width' => $width]);
            $html .= $label;
            $html .= Html::endTag($this->headerColTag);
        }
        $html .= Html::endTag($this->headerRowTag);

        return $html;
    }

    /**
     * 获取操作按钮HTML
     */
    public function getActionButtonsHtml()
    {
        $html = '';
        $actions = [];//当前列表所需操作

        if (empty($this->listAttributes)) {
            return $html;
        }

        foreach ($this->listAttributes as $attribute => $configs) {
            if (!isset($configs['handle'])) {
                continue;
            }
            if ($configs['handle'] == 'pkBox' && isset($configs['args']['actions'])) {
                $actions = $configs['args']['actions'];
            }
            elseif ($configs['handle'] == 'batchDelete') {
                $action = (isset($configs['args']['action'])) ? $configs['args']['action'] : ['label' => '删除', 'url' => ['delete'], 'class' => 'batch-btn'];
                $actions[] = $action;
            }
            elseif ($configs['handle'] == 'batchSort') {
                $action = (isset($configs['args']['action'])) ? $configs['args']['action'] : ['label' => '排序', 'url' => ['sort'], 'class' => 'batch-sort'];
                $actions[] = $action;
            }
        }
        foreach ($actions as $action) {
            $url = ArrayHelper::getValue($action, 'url', '');
            $label = ArrayHelper::getValue($action, 'label', '');
            $class = ArrayHelper::getValue($action, 'class', '');
            $html .= str_replace(['{url}', '{label}', '{class}'], [Url::to($url), $label, $class], $this->actionButtonTemplate);
        }

        return $html;
    }

    /**
     * 获取列表数据HTML
     */
    public function getDataHtml()
    {
        $html = '';

        if (empty($this->listAttributes)) {
            return $html;
        }

        if (empty($this->models)) {
            $html .= Html::beginTag($this->dataRowTag, ['style' => 'text-align:center;']);
            $html .= Html::beginTag($this->dataColTag, ['colspan' => count($this->listAttributes)]);
            $html .= '还没有任何数据...';
            $html .= Html::endTag($this->dataColTag);
            $html .= Html::endTag($this->dataRowTag);
            return $html;
        }

        $model = $this->model;
        $modelClass = $model::ClassName();
        AttributeHandle::$pks = $modelClass::primaryKey();
        foreach ($this->models as $model) {
            $html .= Html::beginTag($this->dataRowTag);
            AttributeHandle::$model = $model;
            foreach ($this->listAttributes as $attribute => $configs) {
                $html .= Html::beginTag($this->dataColTag);

                $html .= $this->parseAttribute($attribute, $model, $configs);

                $html .= Html::endTag($this->dataColTag);
            }
            $html .= Html::endTag($this->dataRowTag);
        }

        return $html;
    }

    /**
     * 根据列表属性参数解析列表属性
     *
     * 程序这里做了以下处理:
     *
     * 1. 检测是否有自定义的属性事件处理`handle`,如果有且为匿名函数则直接调用,否则调用相应的处理事件处理
     * 2. 检测当前模型是否含有此属性，若有则输出,否则抛出异常
     */
    public function parseAttribute($attribute, $model, $configs)
    {
        $attrValue = '';

        if (isset($configs['handle'])) {
            if ($configs['handle'] instanceof \Closure) {
                $attrValue = call_user_func($configs['handle'], $attribute, $model);
            }
            else {
                $handleEvent = $configs['handle'];
                $handleEventName = $handleEvent . 'Event';
                $args = (isset($configs['args'])) ? $configs['args'] : [];
                if (method_exists('common\helpers\AttributeHandle', $handleEventName)) {
                    $args = [$attribute, $args];
                    $attrValue = call_user_func_array(['\common\helpers\AttributeHandle', $handleEventName], $args);
                }
                else {
                    throw new UnknownMethodException($handleEvent . '处理事件不存在！是否调用错误？');
                }
            }
        }
        else if (isset($model[$attribute]) || $model[$attribute] === null) {
            $attrValue = $model[$attribute];
        }
        else {
            throw new UnknownPropertyException($attribute . '属性不存在，且属性处理事件错误！');
        }
        return $attrValue;
    }

    /**
     * 树形处理
     *
     * 这里主要是给列表AR模型的显示属性加上树形等级前缀
     */
    public function treeHandle()
    {
        $tree = $this->_treeBehavior;
        $arrayModels = ArrayHelper::toArray($this->models);
        $this->models = ArrayHelper::index($this->models, $tree->pkAttrName);
        $arrayModels = Tree::getTreeList($arrayModels, $tree->pkAttrName);
        $showAttrName = $tree->showAttrName;
        foreach ($arrayModels as &$model) {
            $spacer = isset($model['_spacer']) ? str_replace(' ', '&nbsp;', $model['_spacer']) : '';
            $model[$showAttrName] = $spacer . $model[$showAttrName];
        }
        $this->models = $arrayModels;
    }

}
