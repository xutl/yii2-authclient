<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\authclient;

use Yii;
use yii\base\Exception;
use yii\authclient\OAuth2;

/**
 * Class Wechat
 * @package xutl\authclient
 */
class Wechat extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://open.weixin.qq.com/connect/qrconnect';
    
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://api.weixin.qq.com';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (strpos(Yii::$app->request->userAgent, 'MicroMessenger') !== false) {//微信内打开的
            $this->authUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';
            $this->scope = implode(',', [
                'snsapi_base', 'snsapi_userinfo'
            ]);
        } else {//PC扫码登录
            if ($this->scope === null) {
                $this->scope = implode(',', [
                    'snsapi_login',
                ]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'id' => 'openid',
            'username' => 'nickname',
        ];
    }

    /**
     * @inheritdoc
     */
    public function buildAuthUrl(array $params = [])
    {
        $params = array_merge($params, ['appid' => $this->clientId]);
        return parent::buildAuthUrl($params);
    }

    /**
     * @inheritdoc
     */
    public function fetchAccessToken($authCode, array $params = [])
    {
        $params = array_merge($params, ['appid' => $this->clientId, 'secret' => $this->clientSecret]);
        return parent::fetchAccessToken($authCode, $params);
    }

    /**
     * Handles [[Request::EVENT_BEFORE_SEND]] event.
     * Applies [[accessToken]] to the request.
     * @param \yii\httpclient\RequestEvent $event event instance.
     * @throws Exception on invalid access token.
     * @since 2.1
     */
    public function beforeApiRequestSend($event)
    {
        $event->request->addData(['openid' => $this->getOpenId()]);
        parent::beforeApiRequestSend($event);
    }

    /**
     * 返回OpenId
     * @return mixed
     */
    public function getOpenId()
    {
        return $this->getAccessToken()->getParam('openid');
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('sns/userinfo', 'GET');
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'wechat';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return Yii::t('app', 'Wechat');
    }

    /**
     * @inheritdoc
     */
    protected function defaultViewOptions()
    {
        return [
            'popupWidth' => 800,
            'popupHeight' => 500,
        ];
    }
}