<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\exceptions;

/**
 * InvalidPropertyException 代表由错误的属性引发的异常
 *
 * @author legendjw <legendjww@gmail.com>
 * @since 0.1
 */
class InvalidPropertyException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Invalid Property';
    }
}
