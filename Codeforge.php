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
 class Codeforge extends Oauth2
 {
     /**
      * @inheritdoc
      */
     public $authUrl = 'https://www.codeforge.cn/oauth2/auth/authorize';

     /**
      * @inheritdoc
      */
     public $tokenUrl = 'https://www.codeforge.cn/oauth2/auth/token';

     /**
      * @inheritdoc
      */
     public $apiBaseUrl = 'https://api.codeforge.cn';

     /**
      * @inheritdoc
      */
     protected function initUserAttributes()
     {
         return $this->api('user/account/index', 'GET');
     }

     /**
      * @inheritdoc
      */
     protected function defaultName()
     {
         return 'codeforge';
     }

     /**
      * @inheritdoc
      */
     protected function defaultTitle()
     {
         return 'CodeForge';
     }
 }