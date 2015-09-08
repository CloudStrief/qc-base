<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\search;

use Yii;
use common\controls\Control;

/**
 * 关键字搜索组件
 *
 * 关键字搜索组件主要用于进行模糊搜索查询，比如查询用户名里带有‘王‘字的用户名，默认以
 * 关键字输入框和搜索类型下拉框的形式来展现，你可以设置属性`$keywordsTypeItems`为键值对形式的
 * 需要搜索的类型，如下：
 *
 * ```php
 * [
 *     'username' => '用户名',
 *     'email' => '邮箱',
 * ]
 * ```
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class KeywordsSearch extends Search
{
    /**
     * @var string $keywordsField 关键字字段名称
     */
    public $keywordsAttribute = 'keywords';
    /**
     * @var string $keywordsTypeField 关键字类型字段名称
     */
    public $keywordsTypeAttribute = 'keywords_type';
    /**
     * @var array $keywordsHtmlOptions 关键字文本控件的html选项
     */
    public $keywordsHtmlOptions = ['class' => 'input length_2 mr10'];
    /**
     * @var array $keywordsTypeHtmlOptions 关键字类型下拉框控件的html选项
     */
    public $keywordsTypeHtmlOptions = ['class' => 'select_2 mr10'];
    /**
     * @var array $keywordsTypeItems 关键字类型下拉框控件的元素键值对
     */
    public $keywordsTypeItems = [];
    
    /**
     * @inheritdoc
     */
    public function renderHtml()
    {
        $html = Control::create('label', ['attribute' => $this->keywordsAttribute, 'model' => $this->model])->renderHtml() . '&nbsp;&nbsp;';

        $html .= Control::create('text', ['attribute' => $this->keywordsAttribute, 'model' => $this->model, 'options' => $this->keywordsHtmlOptions])->renderHtml();

        $html .= Control::create('label', ['attribute' => $this->keywordsTypeAttribute, 'model' => $this->model])->renderHtml() . '&nbsp;&nbsp;';

        $html .= Control::create('dropDown', ['attribute' => $this->keywordsTypeAttribute, 'model' => $this->model, 'items' => $this->keywordsTypeItems, 'options' => $this->keywordsTypeHtmlOptions])->renderHtml();

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function parseQuery($query) 
    {
        $keywordsAttribute = $this->keywordsAttribute;
        $keywordsTypeAttribute = $this->keywordsTypeAttribute;

        $keywords = isset($this->model->$keywordsAttribute) ? $this->model->$keywordsAttribute : null;
        $keywordsType = isset($this->model->$keywordsTypeAttribute) ? $this->model->$keywordsTypeAttribute : null;

        if (!empty($keywords) && !empty($keywordsType)) {
            $query->andWhere(['like', $keywordsType, $keywords]);
        }

        return $query;
    }
}
