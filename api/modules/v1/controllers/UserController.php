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
    protected $optional = ['phone', 'img-upload','edit'];
    public function actionPhone(){
        $post = $this->getPost();
        $app_id = Yii::$app->debris->config('miniprogram_appid');
        $secret = Yii::$app->debris->config('miniprogram_secret');
        $code = $post['code'];
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$app_id}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
        $res = Yii::$app->services->pay->httpRequest($url);
        $res_arr = json_decode($res,true);
        if($res_arr['errcode']){
            return ResultDataHelper::api(403, $res_arr['errmsg']);
        }
        $member_id = $post['member_id'];
        $iv = $post['iv'];
        $encryptedData = $post['encryptedData'];
        $aesKey = $res_arr['session_key'];
        $aesIV=base64_decode($iv);
        $aesCipher=base64_decode($encryptedData);
        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj=json_decode( $result );
        $userphone = $dataObj->purePhoneNumber;
        $member = Member::findOne($member_id);
        $member->userphone = $userphone;
        return $member->save();
    }
    //统一上传图片接口
    public function actionImgUpload()
    {
        $file = $_FILES['file'];
        $fileData = file_get_contents($file['tmp_name']);
        $string = strrev($file['name']);
        $array = explode('.',$string);
        $ext= '.'.strrev($array[0]);
        $name = time() . rand(1, 9999) . $ext;
        $url = 'https://tlx.c63.top/attachment/uploads/images/';
        $dir = realpath('/web/attachment') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($dir . $name, $fileData);
        return $url . $name;
    }
}