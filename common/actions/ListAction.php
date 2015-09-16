<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\actions;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\SearchForm;
use common\widgets\ListView;

/**
 * 通用的列表动作类
 * 
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class ListAction extends \yii\base\Action
{
    /**
     * @var string 当前操作模型类名
     */
    public $modelClass;
    /**
     * @var string 要渲染的列表视图
     */
    public $view;
    /**
     * @var string 视图展现形式
     * @see common\widgets\ListView
     */
    public $showType;
    /**
     * @var array 关联with 
     */
    public $with;
    /**
     * @var array 排序 
     */
    public $order;
    /**
     * @var interger 分页每页记录数
     */
    public $pageSize = 15;
    /**
     * @var array 渲染模板时要传递到模板的值
     */
    private $_renderData = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $request = Yii::$app->request;
        $this->modelClass === null && $this->modelClass = $this->controller->modelClass;
        $model = new $this->modelClass;
        $get = $request->get();

        //获取当前的列表展现形式，如果实现了`tree`行为则使用树形结构显示
        if ($this->showType === null) {
            $this->showType = $model->getBehavior('tree') === null ? ListView::TABLE_VIEW : ListView::TREE_VIEW;
        }

        $query = call_user_func([$this->modelClass, 'find']);

        //排序，默认按照索引倒序排列
        if ($this->order !== null) {
            $query->orderBy($this->order);
        }
        elseif($pks = call_user_func([$this->modelClass, 'primaryKey'])) {
            $query->orderBy([$pks[0] => SORT_DESC]);
        }

        if ($this->with !== null) {
            foreach ($this->with as $with) {
                $query->with($with);
            }
        }

        //获取列表属性
        $listAttributes = method_exists($model, 'listAttributes') ? $model->listAttributes() : [];
        //获取搜索属性
        $searchAttributes = method_exists($model, 'searchAttributes') ? $model->searchAttributes() : [];
        //获取属性名称
        $attributeLabels = method_exists($model, 'attributeLabels') ? $model->attributeLabels() : [];
        //获取过滤导航
        $filterNavs = method_exists($model, 'filterNavs') ? $model->filterNavs() : [];

        //执行公共搜索
        $dynamicAttributes = SearchForm::getDynamicAttributes($searchAttributes, $attributeLabels);
        $searchModel = new SearchForm(\array_keys($dynamicAttributes), $dynamicAttributes);
        if ($searchModel->load($request->get())) {
            $query = SearchForm::getSearchQuery($searchAttributes, $searchModel, $query);
            //如果列表进行了搜索，那么无论是否是树形列表，都将以表格形式展现数据
            $this->showType = ListView::TABLE_VIEW;
        }

        //执行导航过滤
        if (!empty($filterNavs) && isset($filterNavs['execFilter']) && $filterNavs['execFilter'] == true) {
            foreach ($filterNavs['filterAttributes'] as $attribute) {
                if (!empty($get[$attribute])) {
                    $query->andWhere([$attribute => $get[$attribute]]);
                }
            }
        }

        $this->_renderData = [
            'get' => $get,
            'model' => $model,
            'listAttributes' => $listAttributes,
            'searchAttributes' => $searchAttributes,
            'attributeLabels' => $attributeLabels,
            'searchModel' => $searchModel,
            'query' => $query,
            'filterNavs' => $filterNavs,
        ];

        $renderMethod = $this->showType . 'Render';
        return $this->$renderMethod();
    }

    /**
     * 表格列表视图的渲染
     */
    public function tableRender()
    {
        $request = Yii::$app->request;
        $pageSize = $request->get('page_size');
        $pageSize = empty($pageSize) ? $this->pageSize : $pageSize;

        $provider = new ActiveDataProvider([
            'query' => $this->_renderData['query'],
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $this->_renderData += [
            'models' => $provider->models, 
            'pages' => $provider->pagination, 
            'pageSize' => $pageSize
        ];

        $this->view = empty($this->view) ? '/common/list' : $this->view;
        return $this->controller->render($this->view, $this->_renderData);
    }

    /**
     * 树形列表视图的渲染
     */
    public function treeRender()
    {
        $models = $this->_renderData['query']->all();
        $this->_renderData += ['models' => $models]; 

        $this->view = empty($this->view) ? '/common/tree-list' : $this->view;
        return $this->controller->render($this->view, $this->_renderData);
    }

}
