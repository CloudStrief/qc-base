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
/* @var $attributeLabels array 属性名称 */

MainAsset::register($this);
$this->title = '角色管理';
?>


<?php ListView::begin([
    'showType' => ListView::TABLE_VIEW,
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

<?= $this->render('/common/_tree-page', ['models' => $models]); ?>

<div class="btn_wrap">
    <div class="btn_wrap_pd">
        {actionButtons}
    </div>
</div>

<?php ListView::end(); ?>
