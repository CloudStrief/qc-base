<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\base\UnknownPropertyException;
use yii\base\InvalidValueException;

/**
 * 公共的AR模型
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * 启用状态
     */
    const STATUS_ENABLE = 1;

    /**
     * 禁用状态
     */
    const STATUS_DISABLE = 0;

    /**
     * 返回状态列表
     */
    public static function getStatusItems()
    {
        return [
            self::STATUS_ENABLE => '启用',
            self::STATUS_DISABLE => '禁用',
        ];
    }

    /**
     * 列表页要搜索的属性字段
     */
    public function searchAttributes()
    {
        return [];
    }

    /**
     * 列表要展示的属性字段
     */
    public function listAttributes()
    {
        return [];
    }

    /**
     * 模型的控件属性信息
     */
    public function controlAttributes()
    {
        return [];
    }

    /**
     * 过滤导航配置
     *
     * 许多时候你需要在某种模型的列表里进行大分类的过滤显示，比如在菜单列表里，我们希望菜单以所属应用
     * 来分开显示，这时你可以直接在模型里配置此属性来快速达到目的，事例如下：
     *
     * ```php
     *  [
     *      'execFilter' => true,
     *      'filterAttributes' => ['app'],
     *      'actions' => [
     *          '基础应用' => ['index', 'app' => 'backend'],
     *          'CRM' => ['index', 'app' => 'crm'],
     *      ],
     *  ];
     *
     * ```
     *
     * 配置的参数如下：
     *
     * - execFilter boolean 是否在前台列表时执行属性过滤
     * - filterAttributes array 需要过滤的属性字段
     * - actions array 导航url数组，key为链接名称，value为`Url::to()`函数参数的合法形式
     *
     * @return array 返回导航配置
     */
    public function filterNavs()
    {
        return [];
    }
}
