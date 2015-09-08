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
 * 通用的排序动作类
 * 
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class SortAction extends \yii\base\Action
{
    /**
     * @var string 当前操作模型类名
     */
    public $modelClass;
    /**
     * @var string 排序属性
     */
    public $sortAttribute = 'sort';
    /**
     * @var string 成功提示
     */
    public $successMsg = '排序成功';
    /**
     * @var string 错误提示
     */
    public $errorMsg = '排序失败，请稍后再试';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $request = Yii::$app->getRequest();
        $db = Yii::$app->db;
        $modelClass = ($this->modelClass === null) ? $this->controller->modelClass : $this->modelClass;

        $pks = $modelClass::primaryKey();
        $tableName = $modelClass::tableName();
        $pkValues = [];
        $requestMethod = ($request->isGet) ? 'get' : 'post';
        $requestData = $request->$requestMethod();
        
        if (isset($requestData['sort'])) {
            $sorts = $requestData['sort'];
            foreach ($sorts as &$sort) {
                $sort['pk'] = json_decode($sort['pk'], true);
            }
            unset($sort);
        }
        else {
            throw new NotFoundHttpException('排序数据丢失!');
        }

        foreach ($sorts as $sort) {
            $command = $db->createCommand()->update($tableName, [$this->sortAttribute => $sort['sort']], $sort['pk']);

            $command->execute();

            /**
            try {
                $command->execute();
            }
            catch(\Exception $e) {
                return $this->controller->flash(['type' => 'error', 'message' => $this->errorMsg]);
            }
            **/
        }
        return $this->controller->flash(['message' => $this->successMsg]);
    }
}
