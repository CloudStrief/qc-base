<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\models;

use Yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;

/**
 * 应用模型
 *
 * @property integer $app_id 应用ID
 * @property string $name 应用名称
 */
class App extends Object
{
    /**
     * 获取所有应用信息
     */
    public static function getApps()
    {
        return [
            [
                'app_id' => 'backend',
                'name' => '基础应用',
            ],
            [
                'app_id' => 'crm',
                'name' => 'CRM应用',
            ]
        ];
    }

    /**
     * 获取应用选项值
     */
    public static function getAppsItems()
    {
        $apps = static::getApps();
        return ArrayHelper::map($apps, 'app_id', 'name');
    }
}
