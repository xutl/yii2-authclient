<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\authclient;

use Yii;
use yii\httpclient\Client;
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
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'username' => 'nickname',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $user = $this->api('oauth2.0/me', 'GET');
        if (isset($user['error'])) {
            throw new HttpException(400, $user['error'] . ':' . $user['error_description']);
        }
        $userAttributes = $this->api(
            "user/get_user_info",
            'GET',
            [
                'oauth_consumer_key' => $user['client_id'],
                'openid' => $user['openid'],
            ]
        );
        $userAttributes['id'] = $user['openid'];
        return $userAttributes;
    }

    /**
     * Sends the given HTTP request, returning response data.
     * @param \yii\httpclient\Request $request HTTP request to be sent.
     * @return array response data.
     * @throws InvalidResponseException on invalid remote response.
     * @since 2.1
     */
    protected function sendRequest($request)
    {
        $response = $request->send();
        if (!$response->getIsOk()) {
            throw new InvalidResponseException($response, 'Request failed with code: ' . $response->getStatusCode() . ', message: ' . $response->getContent());
        }
        $this->processResult($response);
        return $response->getData();
    }

    /**
     * 处理响应
     * @param Response $response
     * @throws InvalidResponseException
     * @since 2.1
     */
    protected function processResult(Response $response)
    {
        $content = $response->getContent();
        if (strpos($content, "callback") !== 0) {
            throw new InvalidResponseException($response, 'Response is not legitimate.');
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