<?php
/**
 * @link http://www.u-bo.com/
 * @copyright 南京友博网络科技有限公司 
 * @license http://www.u-bo.com/license/
 */

namespace common\helpers;

use Yii;

/**
 * 树形结构处理
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class Tree
{
    /**
     * 转换数组到树状结构
     * 
     * @param array $list 列表数据
     * @param type $pk 主键名称
     * @param type $pid 父级名称
     * @param type $child 子类存放名称
     * @param type $root 根节点
     * @return array 树形数组
     */
    public static function listToTree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0) 
    {
        $tree = array();
        if (is_array($list)) {
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = & $list[$key];
            }
            foreach ($list as $key => $data) {
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = & $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = & $refer[$parentId];
                        $parent[$child][] = & $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * 转换树状结构的数组为普通列表,并加上等级以及前缀
     * 
     * @param type $tree 树状结构数组
     * @param type $level 树深度
     * @param type $child 树子集名称
     * @param type $icon 空格前缀元素
     * @return type
     */
    public static function treeToList($tree, $child = '_child', $level = 0, $icon = ['    ', '├─ ', '│', '└─ ']) 
    {
        static $list = [];
        $level++;
        $i = 0;
        $count = count($tree);
        foreach ($tree as $t) {
            $i++;
            if ($level == 1) {
                $blank = '';
            }
            elseif ($level == 2) {
                $blank = str_repeat($icon[0], $level - 1) . (($i == $count) ? $icon[3] : $icon[1]);
            }
            else {
                $blank = str_repeat($icon[0] . $icon[2], $level - 1) . $icon[0] . (($i == $count) ? $icon[3] : $icon[1]);
            }
            if (isset($t[$child])) {
                $temp = $t;
                unset($temp[$child]);
                $temp['_level'] = $level;
                $temp['_spacer'] = $blank;
                $list[] = $temp;
                $list = self::treeToList($t[$child], $child, $level, $icon);
            } else {
                $t['_level'] = $level;
                $t['_spacer'] = $blank;
                $list[] = $t;
            }
        }
        return $list;
    }

    /**
     * 重新排列具有子父级关系的数组
     */
    public static function getTreeList($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0, $icon = ['    ', '├─ ', '│', '└─ ']) 
    {
        $tree = self::listToTree($list, $pk, $pid, $child, $root);
        $list = self::treeToList($tree, $child, 0, $icon);
        return $list;
    }
}
