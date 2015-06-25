<?php
use backend\assets\MainAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\AttributeHandleEvent;
use yii\base\UnknownPropertyException;
use yii\base\UnknownMethodException;

/* @var $this \yii\web\View */
/* @var $get array 全局变量$_GET */
/* @var $model ActiveRecord 当前的AR模型 */
/* @var $models array 列表AR模型数组 */
/* @var $pages yii\data\Pagination 分页对象 */
/* @var $listAttributes array 列表属性 */
/* @var $searchAttributes array 搜索属性 */
/* @var $listAttributes array 列表属性 */
/* @var $attributeLabels array 属性名称 */
/* @var $pageSize integer 用户自定义分页数 */
/* @var $searchModel common\models\DynamicModel 搜索动态模型 */

MainAsset::register($this);
//\yii\helpers\VarDumper::dump($models, 10, true);
?>

<?= $this->render('_search', ['searchAttributes' => $searchAttributes, 'attributeLabels' => $attributeLabels, 'searchModel' => $searchModel]); ?>

<div class="table_list">
    <?php $mainForm = ActiveForm::begin([ 'id' => 'main-form', 'action' => '']); ?>
    <table width="100%">
        
        <thead>
            <tr>
                <?php 
                    if ($listAttributes !== []) {
                        foreach ($listAttributes as $attribute => $configs) {
                            $width = isset($configs['width']) ? $configs['width'] : '5%';
                            if (isset($configs['handle']) && ($configs['handle'] == 'pkBox' || $configs['handle'] == 'batchDelete')) {
                                $label = '<input type="checkbox" name="select_all" class="box-select-all" />全选';
                            }
                            else {
                                $label = isset($configs['label']) ? $configs['label'] : $model->getAttributeLabel($attribute);
                            }
                            echo '<td width="' . $width . '" >' . $label . '</td>';
                        }
                    }
                ?>
            </tr>
        </thead>
            <?php if ($listAttributes !== []): ?>
                <?php if (!empty($models)): ?>
                    <?php foreach ($models as $model): ?>
                        <?php AttributeHandleEvent::$model = $model; ?>
                        <tr>
                        <?php foreach ($listAttributes as $attribute => $configs): ?>
                            <td>
                                <?php
                                /**
                                 * 程序这里做了以下处理:
                                 *
                                 * 1. 检测是否有自定义的属性事件处理`handle`,如果有且为匿名函数则直接调用,否则调用相应的处理事件处理
                                 * 2. 检测当前模型是否含有此属性，若有则输出,否则抛出异常
                                 */
                                if (isset($configs['handle'])) {
                                    if ($configs['handle'] instanceof \Closure) {
                                        echo call_user_func($configs['handle'], $attribute, $model);
                                    }
                                    else {
                                        $handleEvent = $configs['handle'];
                                        $handleEventName = $handleEvent . 'Event';
                                        $args = (isset($configs['args'])) ? $configs['args'] : [];
                                        if (method_exists('\common\models\AttributeHandleEvent', $handleEventName)) {
                                            $args = [$attribute, $args];
                                            echo call_user_func_array(['\common\models\AttributeHandleEvent', $handleEventName], $args);
                                        }
                                        else {
                                            throw new UnknownMethodException($handleEvent . '处理事件不存在！是否调用错误？');
                                        }
                                    }
                                }
                                else if (isset($model->$attribute)) {
                                    echo $model->$attribute;
                                }
                                else {
                                    throw new UnknownPropertyException($attribute . '属性不存在，且属性处理事件错误！');
                                }
?>
                            </td>
                        <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <td style="text-align:center;" colspan="<?= count($listAttributes) ?>"><span style="color:red;">还没有任何数据...</span></td>
                <?php endif; ?>
            <?php endif; ?>
    </table>
    <?php ActiveForm::end(); ?>
</div>

<?= $this->render('_page', ['pages' => $pages, 'pageSize' => $pageSize]); ?>

<div class="btn_wrap">
    <div class="btn_wrap_pd">
        <?php
            foreach ($listAttributes as $attribute => $configs) {
                if (!isset($configs['handle'])) {
                    continue;
                }
                if ($configs['handle'] == 'pkBox' && isset($configs['args']['actions'])) {
                    $actions = $configs['args']['actions'];
                    foreach ($actions as $action) {
                        echo '<button class="btn btn_submit mr10 batch-btn" data-url="' . Url::to($action['url']) . '" type="submit" >' . $action['label'] . '</button>';
                    }
                }
                elseif ($configs['handle'] == 'batchDelete') {
                    $action = (isset($configs['args']['action'])) ? $configs['args']['action'] : ['label' => '删除', 'url' => ['delete']];
                    echo '<button class="btn btn_submit mr10 batch-btn" data-url="' . Url::to($action['url']) . '" type="submit" >' . $action['label'] . '</button>';
                }
                elseif ($configs['handle'] == 'batchSort') {
                    $action = (isset($configs['args']['action'])) ? $configs['args']['action'] : ['label' => '排序', 'url' => ['sort']];
                    echo '<button class="btn mr10 batch-sort" data-url="' . Url::to($action['url']) . '" type="submit" >' . $action['label'] . '</button>';
                }
            }
        ?>
    </div>
</div>

