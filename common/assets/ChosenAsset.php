<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\assets;

/**
 * Chosen自动搜索补全插件资源包
 *
 * @author strief <strief@u-bo.com>
 * @since 0.1
 */
class ChosenAsset extends AssetBundle
{
    public $css = [
    	'chosen/chosen.css',
    ];
    public $js = [
		'chosen/chosen.jquery.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}