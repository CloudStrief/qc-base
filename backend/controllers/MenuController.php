<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace backend\controllers;

use Yii;

/**
 * 菜单控制器
 * 
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class MenuController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = $this->getCommonActions();
        return $actions;
    }
}
