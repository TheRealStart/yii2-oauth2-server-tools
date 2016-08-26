<?php
/**
 * User: alegz
 * Date: 6/23/15
 * Time: 1:16 PM
 */

namespace TRS\yii2\oauth2server\tools;

use TRS\yii2\oauth2server\tools\oauth2\User;
use yii\base\Action;
use yii\web\Request;

class AccessRule extends \yii\filters\AccessRule
{
    /** @var array list of scopes, used for setting scope for controller */
    public $scopes = [ ];

    /**
     * Checks whether the Web user is allowed to perform the specified action.
     * @param Action $action the action to be performed
     * @param User $user the user object
     * @param Request $request
     * @return boolean|null true if the user is allowed, false if the user is denied, null if the rule does not apply to the user
     */
    public function allows($action, $user, $request)
    {
        $parentResult = parent::allows($action, $user, $request);

        if (is_null($parentResult))
            return null;
        else
            return ( $parentResult && $this->matchScope($user) );
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function matchScope(User $user)
    {
        if (empty( $this->scopes ) || in_array('public', $this->scopes))
            return true;

        $userScopes   = explode(' ', $user->getIdentity()->scope);
        $intersection = array_intersect($userScopes, $this->scopes);
        return !empty( $intersection );
    }
} 