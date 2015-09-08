<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use Yii;
use yii\base\DynamicModel as BaseDynamicModel;

/**
 * 动态模型
 *
 * 当模型的属性在运行时动态添加时，就需要用到动态模型
 *
 * 可以像下面一样使用：
 *
 * ```php
 * $model = new DynamicModel(['name', 'email'], ['name' => '姓名', 'email' => '邮箱']);
 * $model->addRule(['name', 'email'], 'string', ['max' => 128])
 *     ->addRule('email', 'email');
 * $model->load(Yii::$app->request->post());
 * ```
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 * @see yii\base\DynamicModel
 */
class DynamicModel extends BaseDynamicModel
{
    /**
     * @var array 动态注入的属性labels
     */
    private $_attributeLabels = [];

    /**
     * 构造函数
     */
    public function __construct(array $attributes = [], array $attributeLabels = [], $config = []) 
    {
        parent::__construct($attributes, $config);

        $this->_attributeLabels = $attributeLabels;
    }

    /**
     * 返回属性的labels
     *
     * 为了增加动态注入属性labels，这里重写了[[\yii\base\Model::attributeLabels]]，如果继承自此类的动态模型有
     * 静态的属性labels，要按照如下方式调用，以防动态添加的属性labels被覆盖掉
     *
     * ```php
     * public function attributeLabels()
     * {
     *     $attributeLabels = [
     *         //...自定义属性...
     *     ];
     *     return \array_merge($attributeLabels, parent::attributeLabels());
     * }
     * ```
     */
    public function attributeLabels()
    {
        return $this->_attributeLabels;
    }

}
