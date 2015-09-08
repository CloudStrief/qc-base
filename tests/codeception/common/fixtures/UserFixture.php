<?php

namespace tests\codeception\common\fixtures;

use yii\test\ActiveFixture;

/**
 * 用户基境
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = 'common\models\User';
}
