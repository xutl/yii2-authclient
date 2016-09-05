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
 * Class WeChat
 * @package xutl\authclient
 */
class WeChat extends OAuth2
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
        if ($this->scope === null) {
            $this->scope = implode(',', [
                'snsapi_login',
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildAuthUrl(array $params = [])
    {
        $params['appid'] = $this->clientId;
        return parent::buildAuthUrl($params);
    }


    /**
     * @inheritdoc
     */
    public function fetchAccessToken($authCode, array $params = [])
    {
        $params['appid'] = $this->clientId;
        $params['secret'] = $this->clientSecret;
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
        $request = $event->request;
        $data = $request->getData();
        $data['openid'] = $this->getAccessToken()->getParam('openid');
        $request->setData($data);
        parent::beforeApiRequestSend($event);
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
        return '微信';
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