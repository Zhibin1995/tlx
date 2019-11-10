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
        var_dump($auth);
        var_dump($aesCipher);
        var_dump($aesIV);
        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        var_dump($result);
        $dataObj=json_decode( $result );
        var_dump($dataObj);die;
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