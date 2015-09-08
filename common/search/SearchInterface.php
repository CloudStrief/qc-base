<?php
/**
 * @link http://www.u-bo.com
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\search;

/**
 * 搜索组件接口
 *
 * 所有的搜索组件都必须实现此处定义的搜索组件接口
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
interface SearchInterface
{
    /**
     * 返回渲染搜索组件的html
     */
    public function renderHtml();

    /**
     * 解析搜索组件的数据库查询
     *
     * @param \yii\base\ActiveQuery $query 查询对象
     * @return \yii\base\ActiveQuery 返回解析后的查询对象
     */
    public function parseQuery($query);
}
