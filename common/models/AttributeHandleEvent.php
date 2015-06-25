<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\base\UnknownPropertyException;
use yii\base\InvalidValueException;

/**
 * 列表属性处理事件
 *
 * 在模型里配置列表显示的属性时，往往需要做一些处理，我们抽象出最常用的一些处理事件，供
 * 程序直接在配置里使用，以简化列出属性重复处理的工作。目前系统已有以下处理事件：
 *
 * - emptyEvent 为空处理事件，主要为值为空的属性返回一个默认值
 * - joinEvent 联合处理事件，主要用于在一个栏位里显示多个属性值
 * - dateEvent 日期处理事件，主要用于返回属性指定的日期形式
 * - mapEvent 映射处理事件，主要用于根据指定的键值对返回映射后的值
 * - operationEvent 操作处理事件，主要用于返回常用的操作链接
 * - pkBoxEvent 主键多选框事件，主要用于显示含有主键信息的多选框，供需要批量处理的动作使用
 * - batchDelete 批量删除事件，主要用于为列表数据提供批量删除功能
 * - batchSort 批量排序事件，主要用于为列表数据提供批量排序功能
 *
 * 任何处理事件方法都有两个参数：
 *
 * 1. $attribute string 当前处理的属性，可结合静态变量`$model`获取当前的属性值，如`static::$model->$attribute`
 * 2. $args array 处理事件自定义的参数数组，具体参数可查看每个处理事件的参数注释 
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class AttributeHandleEvent
{
    /**
     * @var Model 当前需要处理的静态模型
     */
    public static $model;

    /**
     * 为空处理事件
     *
     * @param string $default 为空时的默认值
     * @return string 属性为空时返回参数设置的默认值，否则返回属性值
     */
    public static function emptyEvent($attribute, array $args = [])
    {
        $model = static::$model;
        $default = ArrayHelper::getValue($args, 'default', '未知');

        return empty($model->$attribute) ? $default : $model->$attribute;
    }

    /**
     * 联合处理事件
     *
     * @param array $joinAttributes 需要联合显示的属性
     * @param string $sepa 联合多个属性值的分隔符，默认为`/`
     * @return string 返回以分隔符相连的多个属性的值
     */
    public static function joinEvent($attribute, array $args = []) 
    {
        $model = static::$model;
        $joinAttributes = ArrayHelper::getValue($args, 'joinAttributes', []);
        $sepa = ArrayHelper::getValue($args, 'sepa', '/');

        if (isset($args['default']) && empty($model->$attribute)) {
            return $args['default'];
        }
        array_unshift($joinAttributes, $attribute);
        $joinValues = [];
        foreach ($joinAttributes as $attribute) {
            if (isset($model->$attribute)) {
                $joinValues[] = $model->$attribute;
            }
            else {
                throw new UnknownPropertyException('访问不存在的属性' . $attribute. '！');
            }
        }
        return implode($sepa, $joinValues);
    }

    /**
     * 日期处理事件
     *
     * @param string $format 日期格式化形式
     * @return string 返回日期函数处理后的字符串
     */
    public static function dateEvent($attribute, array $args = []) 
    {
        $model = static::$model;
        $format = ArrayHelper::getValue($args, 'format', 'Y-m-d H:i:s');

        if (isset($args['default']) && empty($model->$attribute)) {
            return $args['default'];
        }
        return date($format, $model->$attribute);
    }

    /**
     * 映射处理事件
     *
     * @param array $mapData 映射数据，映射数据键值对应属性值，如下
     *
     * ```php
     * [
     *     1 => '启用',
     *     0 => '禁用',
     * ]
     * ```
     *
     * @return string 返回映射后的值
     */
    public static function mapEvent($attribute, array $args = []) 
    {
        $model = static::$model;
        $mapData = ArrayHelper::getValue($args, 'mapData', []);
        if (isset($mapData[$model->$attribute])) {
            return $mapData[$model->$attribute];
        }
        else {
            throw new InvalidValueException('映射字段' . $model->$attribute. '的值没有设置！');
        }
    }

    /**
     * 操作处理事件
     *
     * @param array actions 动作数组，数组中键名代表如下含义：
     *
     * - label 生成动作链接的名称
     * - url 生成动作的链接地址，参数详见[[yii\helpers\Url::to()]]
     * - class 生成动作链接的class属性
     *
     * @return string 所有的动作链接
     */
    public static function operationEvent($attribute, array $args = []) 
    {
        $model = static::$model;
        $actions = ArrayHelper::getValue($args, 'actions', [
            ['label' => '查看', 'url' => ['view']],
            ['label' => '编辑', 'url' => ['update']],
            ['label' => '删除', 'url'=> ['delete'], 'class' => 'link-delete'],
        ]);
        $className = $model::className();
        $pks = $className::primaryKey();
        $ops = [];

        foreach ($pks as $pk) {
            $pkValues[$pk] = $model->$pk;
        }
        foreach ($actions as $action) {
            //组装包含所有主键值的地址
            $url = $action['url'] + $pkValues;
            $url['from'] = Url::to();
            $class = isset($action['class']) ? 'class="' . $action['class'] .'"' : '';
            $ops[] = '<a href="' . Url::to($url) . '" ' . $class . ' >[' . $action['label'] . ']</a> ';
        }
        return implode('&nbsp;', $ops);
    }

    /**
     * 主键多选框事件
     *
     * @param string $name 多选框name属性，默认为`select[]`
     * @param string $class 多选框class属性，默认为`box-select`
     * @param array $actions 生成底部动作按钮数组配置，如下生成一个删除按钮：
     *
     * ```php
     * [
     *     ['label' => '删除', 'url' => ['delete']],
     * ]
     * ```
     *
     * @return string 包含主键值的多选框
     */
    public static function PkBoxEvent($attribute, array $args = [])
    {
        $model = static::$model;
        $inputName = ArrayHelper::getValue($args, 'name', 'select[]');
        $inputClass = ArrayHelper::getValue($args, 'class', 'box-select');

        $className = $model::className();
        $pks = $className::primaryKey();

        foreach ($pks as $pk) {
            $pkValues[$pk] = $model->$pk;
        }
        $pkValues = json_encode($pkValues);
        $tpl = '<input type="checkbox" name="' . $inputName . '" class="' . $inputClass . '" value=\'' . $pkValues . '\' />';
        return $tpl;
    }

    /**
     * 批量删除事件
     * 实际上调用[[pkBoxEvent]]，主要简化删除操作的调用
     *
     * @see pkBoxEvent()
     */
    public static function batchDeleteEvent($attribute, array $args = [])
    {
        return static::PkBoxEvent($attribute, $args);
    }

    /**
     * 批量排序事件
     *
     * @param string $class 生成输入框附加class
     * @return string 返回生成输入框和隐藏主键值
     */
    public static function batchSortEvent($attribute, array $args = [])
    {
        static $num = 0;
        $model = static::$model;
        $inputClass = ArrayHelper::getValue($args, 'class', '');

        $className = $model::className();
        $pks = $className::primaryKey();
        $inputName = 'sort[' . $num . '][sort]';

        foreach ($pks as $pk) {
            $pkValues[$pk] = $model->$pk;
        }
        $pkValues = json_encode($pkValues);
        $tpl = '<input type="text" name="' . $inputName . '" class="input ' . $inputClass . '" value=\'' . $model->$attribute. '\' style="width:30px" /> <input type="hidden" name="sort[' . $num . '][pk]" value=\'' . $pkValues . '\' />';
        $num++;
        return $tpl;
    }
}
