<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\assets;

/**
 * UMeditor富文本编辑器插件资源包
 *
 * @author strief <strief@u-bo.com>
 * @since 0.1
 */
class UMeditorAsset extends AssetBundle
{
    public $css = [
    	'umeditor/themes/default/css/umeditor.css',
    ];
    public $js = [
        'umeditor/umeditor.config.js',
		'umeditor/umeditor.min.js',
    ];
}