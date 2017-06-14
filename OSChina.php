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
 * Class OSChina
 * @package xutl\authclient
 */
class OSChina extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'http://git.oschina.net/oauth/authorize';

    /**
     * @inheritdoc
     */
    public $tokenUrl = 'http://git.oschina.net/oauth/token';

    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'http://git.oschina.net/api';

    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'username' => 'name',
            'email' => 'email',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $user = $this->api('v5/user', 'GET');
        if (isset($user['error'])) {
            throw new HttpException(404, $user['error'] . ':' . $user['error_description']);
        }
        return $user;
    }
}