<?php
/**
 * Created by PhpStorm.
 * User: alegz
 * Date: 6/27/15
 * Time: 4:34 PM
 */

namespace TRS\yii2\oauth2server\tools\oauth2;

use filsh\yii2\oauth2server\models\OauthAccessTokens;
use filsh\yii2\oauth2server\models\OauthRefreshTokens;

abstract class User extends \yii\web\User
{

    /**
     * @param bool $destroySession
     * @return bool
     * @desc Wrappes parent method to remove access token
     */
    public function logout($destroySession = true)
    {
        $parentResult = parent::logout($destroySession);

        if (( $token = $this->getToken() ) == null)
            return $parentResult;

        /** @var OAuthAccessTokens $accessToken */
        $accessToken = OauthAccessTokens::findOne([ 'access_token' => $token ]);
        /** @var OAuthRefreshTokens $refreshToken */
        OauthRefreshTokens::deleteAll([ 'user_id' => $accessToken->user_id, 'scope' => $accessToken->scope, 'client_id' => $accessToken->client_id ]);


        return $parentResult && $accessToken->delete() == 1;
    }

    public function getToken()
    {
        $request = \Yii::$app->getRequest();
        $token   = $request->getHeaders()->get('Authorization');

        if (is_null($token))
            return null;
        else
            return preg_replace('/^Bearer\\s+(.*?)$/', '\1', $token);
    }

    /**
     * @return bool
     */
    abstract public function getIsPublic();
}
