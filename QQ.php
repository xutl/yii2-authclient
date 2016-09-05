<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\authclient;

use Yii;
use yii\web\HttpException;
use yii\httpclient\Response;
use yii\authclient\OAuth2;
use yii\authclient\InvalidResponseException;

/**
 * Class Qq
 * @package xutl\authclient
 */
class Qq extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://graph.qq.com/oauth2.0/authorize';

    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://graph.qq.com/oauth2.0/token';

    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://graph.qq.com';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(',', ['get_user_info']);
        }
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('oauth2.0/me', 'GET');
    }

    /**
     * Fetches access token from authorization code.
     * @param string $authCode authorization code, usually comes at $_GET['code'].
     * @param array $params additional request params.
     * @return OAuthToken access token.
     * @throws HttpException on invalid auth state in case [[enableStateValidation]] is enabled.
     */
    public function fetchAccessToken($authCode, array $params = [])
    {
        if ($this->validateAuthState) {
            $authState = $this->getState('authState');
            if (!isset($_REQUEST['state']) || empty($authState) || strcmp($_REQUEST['state'], $authState) !== 0) {
                throw new HttpException(400, 'Invalid auth state parameter.');
            } else {
                $this->removeState('authState');
            }
        }

        $defaultParams = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $authCode,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->getReturnUrl(),
        ];

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge($defaultParams, $params));

        /** @var Response $response */
        $response = $request->send();
        if (!$response->getIsOk()) {
            throw new InvalidResponseException($response, 'Request failed with code: ' . $response->getStatusCode() . ', message: ' . $response->getContent());
        }
        $this->processResult($response);
        $response = $response->getData();

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * @param Response $response
     */
    protected function processResult(Response $response)
    {
        $content = $response->getContent();
        if (strpos($content, "callback") !== 0) {
            return;
        }
        $lpos = strpos($content, "(");
        $rpos = strrpos($content, ")");
        $content = substr($content, $lpos + 1, $rpos - $lpos - 1);
        $content = trim($content);
        $response->setContent($content);
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'qq';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'QQ';
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