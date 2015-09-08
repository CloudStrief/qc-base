<?php

use tests\codeception\backend\FunctionalTester;
use tests\codeception\backend\_pages\LoginPage;

$I = new FunctionalTester($scenario);
$I->wantTo('确保后台用户管理功能正常');


$loginPage = LoginPage::openBy($I);
$I->amGoingTo('使用正确的用户信息登录表单');
$loginPage->login('admin', 'admin');

$I->amOnPage(Yii::$app->getUrlManager()->createUrl('admin/index'));

$I->expectTo('看见用户管理界面');
$I->seeInTitle('用户管理');

