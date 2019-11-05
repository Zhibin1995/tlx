<?php
/**
 * tlx2
 * PhpStorm
 * @author zhibin
 * @date 2019-10-22
 * Class GoodController.php
 */

namespace api\modules\v1\controllers;


use api\controllers\OnAuthController;
use common\models\app\Address;
use common\models\app\Member;
use common\models\common\Provinces;

class UserController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['phone', 'detail','edit'];
    public function actionPhone(){
        $post = $this->getPost();
        $member_id = $post['member_id'];
        $userphone = $post['userphone'];
        $member = Member::findOne($member_id);
        $member->userphone = $userphone;
        return $member->save();
    }
}