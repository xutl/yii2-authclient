<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
 namespace xutl\authclient;

 use yii\authclient\OAuth2;

 /**
  * Test Oauth
  * @package xutl\authclient
  */
 class Yuncms extends Oauth2
 {
     /**
      * @inheritdoc
      */
     public $authUrl = 'http://api.yuncms.net/auth/authorize';

     /**
      * @inheritdoc
      */
     public $tokenUrl = 'http://api.yuncms.net/auth/token';

     /**
      * @inheritdoc
      */
     public $apiBaseUrl = 'http://api.yuncms.net';

     /**
      * @inheritdoc
      */
     protected function initUserAttributes()
     {
         return $this->api('v1/account/index', 'GET');
     }

     /**
      * @inheritdoc
      */
     protected function defaultName()
     {
         return 'yuncms';
     }

     /**
      * @inheritdoc
      */
     protected function defaultTitle()
     {
         return 'YUNCMS';
     }
 }