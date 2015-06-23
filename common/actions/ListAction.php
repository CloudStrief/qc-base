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
    public $modelName;
    /**
     * @var string 当前视图
     */
    public $view = '/common/list';
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
     * @inheritdoc
     */
    public function run()
    {
        $this->modelName === null && $this->modelName = $this->controller->modelName;
        $model = new $this->modelName;
        $request = Yii::$app->request;

        $query = call_user_func([$this->modelName, 'find']);

        //排序，默认按照索引倒序排列
        if ($this->order !== null) {
            $query->orderBy($this->order);
        }
        elseif($pks = call_user_func([$this->modelName, 'primaryKey'])) {
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
        //获取列表处理事件
        $listHandleEvents = method_exists($model, 'listHandleEvents') ? $model->listHandleEvents() : [];

        //执行公共搜索
        $dynamicAttributes = SearchForm::getDynamicAttributes($searchAttributes, $attributeLabels);
        $searchModel = new SearchForm(\array_keys($dynamicAttributes), $dynamicAttributes);
        $searchModel->load($request->get());
        $query = SearchForm::getSearchQuery($searchAttributes, $searchModel, $query);


        //\yii\helpers\VarDumper::dump($query, 10, true);

        //获取分页数
        $pageSize = $request->get('page_size');
        $pageSize = empty($pageSize) ? $this->pageSize : $pageSize;
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
    
        //\yii\helpers\VarDumper::dump($provider->pagination, 10, true);

        return $this->controller->render($this->view, [
            'get' => $request->get(),
            'model' => new $this->modelName,
            'models' => $provider->models,
            'pages' => $provider->pagination,
            'listAttributes' => $listAttributes,
            'searchAttributes' => $searchAttributes,
            'attributeLabels' => $attributeLabels,
            'listHandleEvents' => $listHandleEvents,
            'pageSize' => $pageSize,
            'searchModel' => $searchModel,
        ]);
    }
		
}
