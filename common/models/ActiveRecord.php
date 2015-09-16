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
}
