<?php
use backend\assets\MainAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\widgets\ListView;

/* @var $this \yii\web\View */
/* @var $get array 全局变量$_GET */
/* @var $model ActiveRecord 当前的AR模型 */
/* @var $models array 列表AR模型数组 */
/* @var $listAttributes array 列表属性 */
/* @var $searchAttributes array 搜索属性 */
/* @var $attributeLabels array 属性名称 */
/* @var $searchModel common\models\DynamicModel 搜索动态模型 */

MainAsset::register($this);
$this->title = '用户管理';
?>

<?= $this->render('_search', ['searchAttributes' => $searchAttributes, 'attributeLabels' => $attributeLabels, 'searchModel' => $searchModel]); ?>

<?php ListView::begin([
    'showType' => ListView::TREE_VIEW,
    'model' => $model, 
    'models' => $models, 
    'listAttributes' => $listAttributes,
    'actionButtonTemplate' => '<button class="btn btn_submit mr10 {class}" data-url="{url}" type="submit">{label}</button>',
]) ?>

<div class="table_list">
    <?php $mainForm = ActiveForm::begin([ 'id' => 'main-form', 'action' => '']); ?>
    <table width="100%">
        
        <thead>
                {header}
        </thead>
            
        {data}

    </table>
    <?php ActiveForm::end(); ?>
</div>

<?= $this->render('_tree-page', ['models' => $models]); ?>

<div class="btn_wrap">
    <div class="btn_wrap_pd">
        {actionButtons}
    </div>
</div>

<?php ListView::end(); ?>
