<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace backend\controllers;

use Yii;
use yii\widgets\ActiveForm;
use yii\rbac\Item;
use common\models\Role;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

/**
 * 角色控制器
 * 
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class RoleController extends Controller
{
    public $modelClass = 'common\models\Role';

    /**
     * 角色列表
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new $this->modelClass;

        //获取列表属性
        $listAttributes = method_exists($model, 'listAttributes') ? $model->listAttributes() : [];
        //获取属性名称
        $attributeLabels = method_exists($model, 'attributeLabels') ? $model->attributeLabels() : [];

        $models = Role::getTreeList();

        return $this->render('index', [
            'get' => $request->get(),
            'model' => $model,
            'models' => $models,
            'listAttributes' => $listAttributes,
            'attributeLabels' => $attributeLabels,
        ]);
    }

    /**
     * 创建角色
     */
    public function actionCreate()
    {
        $auth = Yii::$app->authManager;
        $model = new $this->modelClass();
        $model->loadDefaultValues();

        $request = Yii::$app->getRequest();

        //新增后是否留在新增页面
        $jumpCreate = $request->post('jump_create');
        $url = $jumpCreate == 1 ? ['create'] : ['index'];

        //获取当前模型的控件属性
        $controlAttributes = method_exists($model, 'controlAttributes') ? $model->controlAttributes() : [];


        if ($model->load($request->post())) {
            $role = $auth->createRole($model->name);
            $role->description = $model->description;

            if ($model->validate()) {
                if ($auth->add($role)) {
                    //如果父级角色存在，则添加子父级关系
                    if (!empty($model->parent)) {
                        $parentRole = $auth->getRole($model->parent);
                        $auth->addChild($parentRole, $role);
                    }
                    return $this->flash(['message' => '新增角色成功', 'url' => $url]);
                }
                else {
                    return $this->flash(['type' => 'error', 'message' => '新增角色失败', 'url' => $url]);
                }
            }
            elseif ($request->isAjax) {
                //如果是ajax请求则返回错误信息而不是直接跳转到原页面
                $errors = ActiveForm::validate($model);
                return $this->flash(['type' => 'error', 'message' => '新增角色失败', 'time' => 3000, 'data' => ['errors' => $errors]]);
            }
        }

        return $this->render('/common/create', [
            'controlAttributes' => $controlAttributes,
            'get' => $request->get(),
            'model' => $model,
        ]);
    }

    /**
     * 更新角色
     */
    public function actionUpdate()
    {
        $auth = Yii::$app->authManager;
        $modelClass = $this->modelClass;
        $request = Yii::$app->getRequest();
        $requestMethod = ($request->isGet) ? 'get' : 'post';
        $name = $request->$requestMethod('name');
        $from = $request->$requestMethod('from');

        $role = $auth->getRole($name);
        if (empty($role)) {
            throw new NotFoundHttpException('没有找到相应的角色!');
        }
        $model = $modelClass::find()->where(['name' => $name])->with('parentItem')->one();
        $model->parent = isset($model->parentItem) ? $model->parentItem->parent : '';

        //获取当前模型的控件属性
        $controlAttributes = method_exists($model, 'controlAttributes') ? $model->controlAttributes() : [];

        if ($model->load($request->post())) {
            $role = $auth->createRole($model->name);
            $role->description = $model->description;

            if ($model->validate()) {
                if ($auth->update($name, $role)) {
                    //先删除之前的父级
                    if (isset($model->parentItem) && $model->parent != $model->parentItem->parent) {
                        $oldParentRole = $auth->getRole($model->parentItem->parent);
                        $auth->removeChild($oldParentRole, $role);
                    }
                    //如果父级角色存在，则添加子父级关系
                    if (!empty($model->parent)) {
                        if (!isset($model->parentItem) || (isset($model->parentItem) && $model->parent != $model->parentItem->parent)) {
                            $parentRole = $auth->getRole($model->parent);
                            $auth->addChild($parentRole, $role);
                        }
                    }
                    return $this->flash(['message' => '更新角色成功', 'url' => $from]);
                }
                else {
                    return $this->flash(['type' => 'error', 'message' => '更新角色失败', 'url' => $from]);
                }
            }
            elseif ($request->isAjax) {
                //如果是ajax请求则返回错误信息而不是直接跳转到原页面
                $errors = ActiveForm::validate($model);
                return $this->flash(['type' => 'error', 'message' => '更新角色失败', 'time' => 3000, 'data' => ['errors' => $errors]]);
            }
        }

        return $this->render('/common/create', [
            'controlAttributes' => $controlAttributes,
            'get' => $request->get(),
            'model' => $model,
            'pks' => ['name'],
            'from' => $from,
        ]);
    }

    /**
     * 删除角色
     */
    public function actionDelete()
    {
        $auth = Yii::$app->authManager;
        $request = Yii::$app->getRequest();
        $name = $request->get('name');
        $modelClass = $this->modelClass;

        $role = $auth->getRole($name);
        if (empty($role)) {
            throw new NotFoundHttpException('没有找到相应的角色!');
        }
        $model = $modelClass::find()->where(['name' => $name])->with('childItems')->one();
        if (!empty($model['childItems'])) {
            throw new ForbiddenHttpException('角色下含有子角色不允许删除!');
        }

        if ($auth->remove($role)) {
            return $this->flash(['message' => '删除角色成功']);
        }
        else {
            return $this->flash(['type' => 'error', 'message' => '删除角色失败']);
        }
    }
}
