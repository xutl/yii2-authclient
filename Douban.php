<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\authclient;

use yii\authclient\OAuth2;

class Douban extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://www.douban.com/service/auth2/auth';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://www.douban.com/service/auth2/token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://api.douban.com/';
    /**
     * @inheritdoc
     */
    public $scope = 'douban_basic_common';


    /**
     * @inheritdoc
     */
    protected function initUserAttributes() {
        return $this->api('v2/user/~me', 'GET');
    }

    /**
     * @return array
     * @see http://developers.douban.com/wiki/?title=user_v2#User
     */
    public function getUserInfo()
    {
        return $this->getUserAttributes();
    }

    /**
     * @inheritdoc
     */
    protected function defaultName() {
        return 'douban';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle() {
        return '豆瓣';
    }

    protected function defaultViewOptions() {
        return [ 'popupWidth'=> 1000, 'popupHeight'=> 500 ];
    }
}