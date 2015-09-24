<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;

/**
 * my97日期插件资源包
 *
 * @author strief <strief@u-bo.com>
 * @since 0.1
 */
class My97DatePickerAsset extends AssetBundle
{
    public $basePath = '@webroot/bundle';
    public $baseUrl = '@web/bundle';
    public $css = [
    ];
    public $js = [
        'my97DatePicker/WdatePicker.js',
    ];
}