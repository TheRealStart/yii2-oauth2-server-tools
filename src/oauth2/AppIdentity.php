<?php
/**
 * Created by PhpStorm.
 * User: alegz
 * Date: 6/23/15
 * Time: 5:53 PM
 */

namespace TRS\yii2\oauth2server\tools\oauth2;

use filsh\yii2\oauth2server\models\OauthClients;
use OAuth2\Storage\ClientCredentialsInterface;

abstract class AppIdentity implements ClientCredentialsInterface
{
    /**
     * @param $client_id
     * @return null|OauthClients
     */
    static private function findByClientId($client_id)
    {
        return OauthClients::findOne([ 'client_id' => $client_id ]);
    }

    /**
     * @param $client_id
     * @param null $client_secret
     * @return bool
     */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        return OauthClients::find()
            ->where([ 'client_id' => $client_id, 'client_secret' => $client_secret ])
            ->exists();
    }

    /**
     * @param $client_id
     * @return bool
     *
     * Idea of method is close to method `is_guest` in User class (for yii2 user manager) but
     * when using client credentials user is not a guest.
     * In this case guest differs from not guest based on application scope.
     * In this method you check your scope and return your check result.
     */
    abstract public function isPublicClient($client_id);

    /**
     * @param $client_id
     * @param $grant_type
     * @return bool
     */
    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        $app = self::findByClientId($client_id);

        return !!$app && in_array($grant_type, explode(' ', $app->grant_types));
    }

    /**
     * @param $client_id
     * @return array|OauthClients|null
     */
    public function getClientDetails($client_id)
    {
        $app = self::findByClientId($client_id);

        if ($app)
            return [
                'user_id' => $app->user_id,
                'scope'   => $app->scope
            ];
        else
            return $app;
    }

    /**
     * @param $client_id
     * @return OauthClients|null|string
     */
    public function getClientScope($client_id)
    {
        $app = self::findByClientId($client_id);

        if ($app)
            return $app->scope;
        else
            return $app;
    }
} 