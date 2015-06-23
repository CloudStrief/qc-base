<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use Yii;
use \common\search\Search;

/**
 * 通用搜索表单
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class SearchForm extends DynamicModel
{
    /**
     * @var string 搜索的关键字
     */
    public $keywords;
    /**
     * @var string 搜索的关键字类型
     */
    public $keywords_type;
    /**
     * @var Model 搜索表单模型
     */
    public static $searchModel;
    /**
     * @var \yii\widgets\ActiveForm 搜索表单
     */
    public static $searchForm;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $attributes = $this->attributes();
        return [
            [['keywords', 'keywords_type'], 'string'],
            [$attributes, 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = [
            'keywords' => '关键字',
            'keywords_type' => '搜索类型',
        ];
        return \array_merge($attributeLabels, parent::attributeLabels());
    }

    /**
     * 根据搜索属性获取其中所有的属性键值对
     * 也可以设置`$type`属性只获取某种类别的属性，比如只获得`keywords`关键字类别的属性
     *
     * @param array $searchAttributes 搜索属性
     * @param array $attributeLabels 属性labels
     * @param string $type 指定搜索类别
     * @return array 属性键值对
     */
    public static function getDynamicAttributes(array $searchAttributes, array $attributeLabels, $type = null)
    {
        $attributes = [];
        foreach ($searchAttributes as $attribute => $configs) {
            if ($type != null && isset($configs['type']) && $configs['type'] != $type) {
                continue;
            }

            if (isset($configs['label'])) {
                $label = $configs['label'];
            }
            elseif (isset($attributeLabels[$attribute])) {
                $label = $attributeLabels[$attribute];
            }
            else {
                $label = $attribute;
            }
            $attributes[$attribute] = $label;
        }
        return $attributes;
    }

    /**
     * 获取被搜索组件解析过的查询对象
     * 
     * @param array $searchAttributes 搜索属性
     * @param Model $searchModel 搜索模型
     * @param \yii\base\ActiveQuery $query 列表查询对象
     * @return \yii\base\ActiveQuery 解析过的查询对象
     */
    public static function getSearchQuery(array $searchAttributes, $searchModel, $query)
    {
        $query = Search::create('keywords', 'keywords', $searchModel)->parseQuery($query);

        foreach ($searchAttributes as $attribute => $configs) {
            //关键字组件已经单独解析
            if (isset($configs['type']) && $configs['type'] == 'keywords') {
                continue;
            }
            $type = $configs['type'];
            unset($configs['type'], $configs['label']);
            $query = Search::create($type, $attribute, $searchModel, null, $configs)->parseQuery($query);
        }

        return $query;
    }
}
