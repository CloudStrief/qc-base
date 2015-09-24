<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\assets;

/**
 * artDialog弹出窗资源包
 *
 * @author legendjw <legendjw@yeah.net>
 * @since 0.1
 */
class ArtDialogAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/artDialog-temp';
    public $css = [
        'css/ui-dialog.css',
    ];
    public $js = [
        'dist/dialog-min.js',
        'dist/dialog-plus-min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
