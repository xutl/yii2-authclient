<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\authclient;

use Yii;
use yii\authclient\OAuthToken;
use yii\base\Exception;
use yii\authclient\OAuth2;
use yii\web\HttpException;

/**
 * 微信内登录
 * @package xutl\authclient
 */
class WechatMp extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    /**
     * @inheritdoc
     */
    public $refreshTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';

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
        $this->scope = implode(',', [
            'snsapi_userinfo'
        ]);
    }

    /**
     * Composes user authorization URL.
     * @param array $params additional auth GET params.
     * @return string authorization URL.
     */
    public function buildAuthUrl(array $params = [])
    {
        $defaultParams = [
            'appid' => $this->clientId,
            'redirect_uri' => $this->getReturnUrl(),
            'response_type' => 'code',
        ];
        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        if ($this->validateAuthState) {
            $authState = $this->generateAuthState();
            $this->setState('authState', $authState);
            $defaultParams['state'] = $authState;
        }

        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params)).'#wechat_redirect';
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
     * Applies client credentials (e.g. [[clientId]] and [[clientSecret]]) to the HTTP request instance.
     * This method should be invoked before sending any HTTP request, which requires client credentials.
     * @param \yii\httpclient\Request $request HTTP request instance.
     * @since 2.1.3
     */
    protected function applyClientCredentialsToRequest($request)
    {
        $request->addData([
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
        ]);
    }

    /**
     * Gets new auth token to replace expired one.
     * @param OAuthToken $token expired auth token.
     * @return OAuthToken new auth token.
     */
    public function refreshAccessToken(OAuthToken $token)
    {
        $params = [
            'grant_type' => 'refresh_token'
        ];
        $params = array_merge($token->getParams(), $params);

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->refreshTokenUrl)
            ->setData($params);

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
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