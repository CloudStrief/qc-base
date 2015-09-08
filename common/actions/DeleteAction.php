<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\actions;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * 通用的删除动作类
 * 
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class DeleteAction extends \yii\base\Action
{
    /**
     * @var string 当前操作模型类名
     */
    public $modelClass;
    /**
     * @var string 成功提示
     */
    public $successMsg = '删除成功';
    /**
     * @var string 错误提示
     */
    public $errorMsg = '删除失败，请稍后再试';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $request = Yii::$app->getRequest();
        $modelClass = ($this->modelClass === null) ? $this->controller->modelClass : $this->modelClass;
        //找到当前模型的所有主键，拼接成数组条件
        $pks = $modelClass::primaryKey();
        $pkValues = [];
        $requestMethod = ($request->isGet) ? 'get' : 'post';
        $requestData = $request->$requestMethod();
        
        //如果存在delete参数则为批量删除
        if (isset($requestData['select'])) {
            $deletes = $requestData['select'];
            foreach ($deletes as &$delete) {
                $delete = json_decode($delete, true);
            }
            unset($delete);
        }
        else {
            foreach ($pks as $pk) {
                $pkValues[$pk] = $requestData[$pk];
            }
            $deletes[] = $pkValues;
        }

        foreach ($deletes as $delete) {
            $model = $modelClass::findOne($delete);
            if ($model === null) {
                throw new NotFoundHttpException('没有找到要删除的记录!');
            }

            if (false === $model->delete()) {
                return $this->controller->flash(['type' => 'error', 'message' => $this->errorMsg]);
            }
        }
        return $this->controller->flash(['message' => $this->successMsg]);

    }
}
