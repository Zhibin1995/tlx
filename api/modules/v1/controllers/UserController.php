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
use common\enums\CacheKeyEnum;
use common\helpers\ResultDataHelper;
use common\models\app\Address;
use common\models\app\Member;
use common\models\common\Provinces;
use Yii;

class UserController  extends OnAuthController
{
    public $modelClass = '';
    protected $optional = ['phone', 'detail','edit'];
    public function actionPhone(){
        $post = $this->getPost();
        $member_id = $post['member_id'];
        $iv = $post['iv'];
        $encryptedData = $post['encryptedData'];
        $auth_key = $post['auth_key'];
        $auth = Yii::$app->cache->get(CacheKeyEnum::API_MINI_PROGRAM_LOGIN . $auth_key);
        if(!$auth){
            return ResultDataHelper::api(403, '登陆已失效');
        }
        $aesKey = $auth['session_key'];

        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj=json_decode( $result );
        var_dump($dataObj);die;
        $userphone = $dataObj->purePhoneNumber;
        $member = Member::findOne($member_id);
        $member->userphone = $userphone;
        return $member->save();
    }
}