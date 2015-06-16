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

        <?= Control::createControl('label', 'keywords', $searchForm)->renderHtml() ?>&nbsp;&nbsp;

        <?= Control::createControl('text', 'keywords', $searchForm, null, ['htmlClass' => 'input length_2 mr10'])->renderHtml() ?>

        <?= Control::createControl('label', 'keywordsField', $searchForm)->renderHtml() ?>&nbsp;&nbsp;

        <select class="select_2 mr10" name="flag">
            <option value="">模块分类</option>
            <option value="forum" >版块</option>
            <option value="html" >自定义html</option>
            <option value="image" >图片</option>
            <option value="link" >友情链接</option>
            <option value="searchbar" >搜索条</option>
            <option value="tag" >话题</option>
            <option value="thread" >帖子</option>
            <option value="user" >用户</option>
        </select>
        <button class="btn" type="submit">搜索</button>
    </div>
    <?php endif; ?>
    <?php ActiveForm::end(); ?>
<?php endif; ?>
