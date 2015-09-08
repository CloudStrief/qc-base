<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\search;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * 搜索组件基类
 *
 * 我们把常用的几种搜索形式抽象成搜索组件，以达到功能的复用，如果要实现自定义的搜索组件，需要组件
 * 实现[[SearchInterface]]接口。
 *
 * 目前系统已经支持的搜索组件如下：
 *
 * - keywords 关键字搜索，主要用于根据某些关键字查询结果
 * - map 映射搜索，主要用于具体的相等查询
 * 
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
abstract class Search extends Component implements SearchInterface
{
    /**
     * @var array 内建的搜索组件列表
     */
    public static $builtInSearch = [
        'keywords' => 'common\search\KeywordsSearch',
        'map' => 'common\search\MapSearch',
    ];
    /**
     * @var string 搜索的属性
     */
    public $attribute;
    /**
     * @var \common\models\DynamicModel 搜索表单模型
     */
    public $model;
    /**
     * @var \yii\widgets\ActiveForm 搜索表单
     */
    public $form;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->attribute === null || $this->model === null) {
            throw new InvalidConfigException('搜索组件缺失必要参数！请检查是否忘记传递！');
        }
    }

    /**
     * 根据指定的类型和参数创建搜索组件
     *
     * @param string|closure $type 控件类型，可参见静态属性`$builtInControls`，参数也可为匿名函数
     * @param string $attribute 模型的属性
     * @param \common\models\DynamicModel $model 搜索表单模型
     * @param \yii\widgets\ActiveForm $form 表单插件
     * @param array $params 创建控件的属性列表
     * @return \common\Search\Search 返回指定搜索组件
     */
    public static function create($type, $attribute, $model, $form = null, $params = [])
    {
        $params['attribute'] = $attribute;
        $params['form'] = $form;
        $params['model'] = $model;
        if ($type instanceof \Closure) {
            $params['class'] = __NAMESPACE__ . '\InlineSearch';
            $params['method'] = $type;
        } else {
            if (isset(static::$builtInSearch[$type])) {
                $type = static::$builtInSearch[$type];
            }
            if (is_array($type)) {
                $params = array_merge($type, $params);
            } else {
                $params['class'] = $type;
            }
        }

        return Yii::createObject($params);
    }

}
