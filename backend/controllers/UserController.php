<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace backend\controllers;

use Yii;
use common\models\LoginForm;

/**
 * 用户控制器
 * 
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class UserController extends Controller
{
    public $modelClass = 'common\models\User';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = $this->getCommonActions();
        $actions['create']['scenario'] = 'create';
        $actions['update']['scenario'] = 'update';
        return $actions;
    }
}
