<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\controls;

use Yii;
use yii\base\Component;

/**
 * html控件基类
 *
 * 我们把开发中遇到的表单元素和模型结合到一起抽象成控件，这样不仅仅可以直接显示一个表单元素，还可以
 * 完成模型中的表单验证、编辑自动赋值等功能，最大化减少开发工作量。
 * 
 * 比如我们需要显示一个文本框控件：
 *
 * ```php
 * Control::create('text', 'username', $model, $form, ['htmlClass' => 'text-input'])->renderHtml();
 * ```
 *
 * 目前系统已经支持的控件如下：
 *
 * - label 标签控件，对应html里的label标签
 * - text 文本框控件，对应html里type为text的input标签
 * - password 密码控件，对应html里type为password的input标签
 * - radio 单选框控件，对应html里type为radio的input标签
 * - dropDown 下拉框控件，对应html里select标签
 *
 * 大多数控件在渲染html元素时做了以下三个判断，可根据实际功能需求传递`$model`、`$form`属性
 *
 * 1. 如果属性`$model`、`$form`都不为null，则调用[[\yii\widgets\ActiveForm]]来渲染html，此时会含有模型
 *    表单验证、自动赋值、自动生成label等特性
 * 2. 如果属性`$model`不为null，而`$form`为null，则调用[[\yii\helpers\Html]]里与模型绑定的生成方法渲染html，
 *    此时有自动赋值特性，没有表单验证、自动生成label等特性
 * 3. 如果属性`$model`、`$form`都为null，则调用[[\yii\helpers\Html]]里普通方法根据参数渲染html，只生成表单
 *    元素，不带任何附加功能
 *
 * 
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
abstract class Control extends Component
{
    /**
     * @var array 内建的html控件列表
     */
    public static $builtInControls = [
        'label' => 'common\controls\LabelControl',
        'text' => 'common\controls\TextControl',
        'password' => 'common\controls\PasswordControl',
        'radio' => 'common\controls\RadioControl',
        'dropDown' => 'common\controls\DropDownControl',
    ];
    /**
     * @var string 当前解析的模型的属性
     */
    public $attribute;
    /**
     * @var \yii\widgets\ActiveForm 当前模型的表单插件
     */
    public $form;
    /**
     * @var \yii\db\ActiveRecord 当前操作的模型
     */
    public $model;
    /**
     * @var string 提示信息
     */
    public $hint;
    /**
     * @var string 控件的name属性，仅仅在属性`$model`和`$form`都为空时才生效
     */
    public $name;
    /**
     * @var string 控件的value属性，仅仅在属性`$model`和`$form`都为空时才生效
     */
    public $value;
    /**
     * @var array 生成html标签属性的键值对
     */
    public $htmlOptions = [];
    /**
     * @var array 标签默认的html标签属性，不应被外部直接设置，如果`$htmlOptions`属性里设置了相同属性，此设置则被覆盖
     */
    protected $defaultHtmlOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->htmlOptions = array_merge($this->defaultHtmlOptions, $this->htmlOptions);
        $this->name = $this->name === null ? $this->attribute : $this->name;
    }

    /**
     * 根据指定的类型和参数创建控件
     *
     * @param string|closure $type 控件类型，可参见静态属性`$builtInControls`，参数也可为匿名函数
     * @param string $attribute 模型的属性
     * @param \yii\db\ActiveRecord|null $model 模型，可为null
     * @param \yii\widgets\ActiveForm|null $form 表单插件，可为null
     * @param array $params 创建控件的属性列表
     * @return \common\controls\Control 返回指定控件
     */
    public static function create($type, $attribute, $model = null, $form = null, $params = [])
    {
        $params['attribute'] = $attribute;
        $params['form'] = $form;
        $params['model'] = $model;
        if ($type instanceof \Closure) {
            $params['class'] = __NAMESPACE__ . '\InlineControl';
            $params['method'] = $type;
        } else {
            if (isset(static::$builtInControls[$type])) {
                $type = static::$builtInControls[$type];
            }
            if (is_array($type)) {
                $params = array_merge($type, $params);
            } else {
                $params['class'] = $type;
            }
        }

        return Yii::createObject($params);
    }

    /**
     * 返回渲染控件的html
     *
     * 由继承的控件来实现各自的逻辑，所有的子类都应该重新实现此方法
     */
    public function renderHtml()
    {
        return '';
    }

    /**
     * 返回控件的值，供用户查看
     */
    public function renderValue()
    {
        return '';
    }
}
