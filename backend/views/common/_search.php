<?php
use yii\widgets\ActiveForm;
use common\controls\Control;
use yii\helpers\Url;
use common\models\SearchForm;
use common\search\Search;
use common\search\KeywordsSearch;
use yii\base\InvalidConfigException;

/* @var $searchAttributes array 搜索属性 */
/* @var $searchModel common\models\DynamicModel 搜索动态模型 */

?>
<?php if (!empty($searchAttributes)): ?>
    <div class="h_a">搜索</div>
    <?php
    $searchForm = ActiveForm::begin([
        'id' => 'search-form',
        'method' => 'GET',
    ]);
    ?>
    <div class="search_type cc mb10">

        <?php
            //渲染关键字搜索
            $keywordsTypeItems = SearchForm::getDynamicAttributes($searchAttributes, $attributeLabels, 'keywords');
            if (!empty($keywordsTypeItems)) {
                echo Search::create('keywords', 'keywords', $searchModel, $searchForm, ['keywordsTypeItems' => $keywordsTypeItems])->renderHtml();
            }
            //渲染其他搜索
            foreach ($searchAttributes as $attribute => $configs) {
                if (!isset($configs['type'])) {
                    throw new InvalidConfigException('搜索属性' . $attribute . '缺少type参数值！');
                }
                if ($configs['type'] == 'keywords') {
                    continue;
                }
                $type = $configs['type'];
                unset($configs['label'], $configs['type']);
                echo Search::create($type, $attribute, $searchModel, $searchForm, $configs)->renderHtml();
            }
        ?>
        
        <button class="btn mr10" type="submit">搜索</button>

        <button class="btn" onclick="javascript:window.location.href='<?= Url::to(['index']) ?>'" >重置</button>
    </div>
    <?php ActiveForm::end(); ?>
<?php endif; ?>
