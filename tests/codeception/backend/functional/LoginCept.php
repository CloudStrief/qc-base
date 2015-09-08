<?php

use tests\codeception\backend\FunctionalTester;
use tests\codeception\backend\_pages\LoginPage;

$I = new FunctionalTester($scenario);
$I->wantTo('确保后台登录正常工作');

$loginPage = LoginPage::openBy($I);

$I->amGoingTo('不输入信息直接提交登录表单');
$loginPage->login('', '');
$I->expectTo('看见验证错误信息');
$I->see('用户名不能为空', '.help-block');
$I->see('密码不能为空', '.help-block');

$I->amGoingTo('使用错误的密码提交登录表单');
$I->expectTo('看见验证错误信息');
$loginPage->login('admin', 'wrong');
$I->expectTo('看见验证错误信息');
$I->see('用户名或者密码错误', '.help-block');

$I->amGoingTo('使用正确的用户信息登录表单');
$loginPage->login('admin', 'admin');
$I->expectTo('用户登录成功');
$I->seeLink('[注销]');
$I->dontSeeInTitle('登录');

