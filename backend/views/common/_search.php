<?php
use yii\widgets\ActiveForm;
use common\controls\Control;

/* @var $searchAttributes array 搜索属性 */
/* @var $searchForm common\models\DynamicModel 搜索动态模型 */

?>
<?php if (!empty($searchAttributes)): ?>
    <div class="h_a">搜索</div>
    <?php
    $form = ActiveForm::begin([
        'id' => 'search-form',
        'fieldConfig' => [
        ]
    ]);
    ?>
    <?php if (isset($searchAttributes['keywords'])): ?>
    <div class="search_type cc mb10">

        <?= Control::create('label', 'keywords', $searchForm)->renderHtml() ?>&nbsp;&nbsp;


        <?= Control::create('text', 'keywords', $searchForm, null, ['htmlOptions' => ['class' => 'input length_2 mr10']])->renderHtml() ?>

        <?= Control::create('label', 'keywordsField', $searchForm)->renderHtml() ?>&nbsp;&nbsp;


        <?= Control::create('dropDown', 'keywordsField', $searchForm, null, ['items' => $searchAttributes['keywords'], 'htmlOptions' => ['class' => 'select_2 mr10']])->renderHtml() ?>

        <button class="btn" type="submit">搜索</button>

    </div>
    <?php endif; ?>
    <?php ActiveForm::end(); ?>
<?php endif; ?>
