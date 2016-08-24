# yii2-oauth2-server-tools

Set of simple tools that helps to build API based on
`alegz/yii2-oauth2-server` package.

## Installation

Via command line:

`composer require the-real-start/yii2-oauth2-server-tools`

Or add lime to composer.json requirement section:

```
"require": {
    ...
    "the-real-start/yii2-oauth2-server-tools": "*"
    ...
  }
```

## Usage

Package namepsace: `TRS\yii2\oauth2server\tools`

Package provides 5 classes for setting up oauth2-server:

* Abstract class AppIdentity
* Abstract class User
* Class ErrorHandler
* Class AccessRules
* Class JsonHttpException

### AppIdentity

Abstract class for `client credentials` grant_type
(see https://tools.ietf.org/html/rfc6749 for details).
 
 You should declare getIsPublic method for you version of `AppIdentity`
 class. See example below:
 
 ```
 <?php
 
 namespace common\components;
 
 use common\components\enums\Scope;
 use TRS\yii2\oauth2server\tools\oauth2\AppIdentity as BaseAppIdentity
 
 class AppIdentity extends BaseAppIdentity
 {
     /**
      * @inheritdoc
      */
     abstract public function isPublicClient($client_id){
        $app = self::findByClientId($client_id);
        
        return !!$app && $app->scope == Scope::_PUBLIC;
     }
 } 
 ```
 
 Example with setting up module of `yii2-oauth2-server`:
 
 ```
modules'             => [
...
    'oauth2' => [
     'class'               => \filsh\yii2\oauth2server\Module::className(),
     ...
     'storageMap'          => [
         ...
         'client_credentials' => \common\components\AppIdentity::class,
     ],
     ...
 ],
 ...
 ],
 ```

### User

This abstract class extends `yii\web\User` adds `getIsPublic` method and
changes logic around logging user out.

This class requires to declare method `getIsPublic` for `User`

Example of class:

```
<?php

namespace common\components;

use common\enums\Scope;
use TRS\yii2\oauth2server\tools\oauth2\User as BaseUser;

class User extends BaseUser
{
    /**
     * @ingeritdoc
     */
    public function getIsPublic()
    {
        /** @var \common\models\User $identity */
        $identity = $this->getIdentity(false);
        
        return ( $identity->scope == Scope::_PUBLIC );
    }
}

```

Setting up example:

```
'components'          => [
    ...
    'user'                 => [
        'class'           => \common\components\User::className(),
        'identityClass'   => \common\models\User::className(),
        'enableAutoLogin' => true,
    ],
    ...
],
```

### ErrorHandler

Simple error handler that were designed to be used with API.

Setup example in yii2 `main.php` config file in `components` section:

```
...
'errorHandler'         => [
    'class' => \v1\components\oauth2\rest\ErrorHandler::className(),
],
...
```

### AccessRules

Extended version of yii2 `AccessRule`. Adds support of user scope 
filtering.

Example of rule

```
 public function accessRules()
    {
        return [
            [
                'allow'   => true,
                'roles'   => [ '@' ],
                'actions' => [ 'registration', 'send-recovery-email', 'reset-password', 'check-reset-token' ],
                'scopes'  => [ Scope::_PUBLIC ],
            ],
        ];
    }
```

Setup example:

```
public function behaviors()
    {
     $behaviors = parent::behaviors();
     ...
     $behaviors = ArrayHelper::merge(
            $behaviors,
            [
                ...
                'access'            => [
                                         'class'      => AccessControl::className(),
                                         'rules'      => $this->accessRules(),
                                         'ruleConfig' => ['class' => AccessRule::class],
                ],
                ...
            ]
        );

        return $behaviors;
    }
```

In this and previous examples accessRules were declared as abstract
method in base controller.

### JsonHttpException

It's simple wrapper over HTTPException that can accept array as message.

Array is converted to json.

Designed to use with ErrorHandler but you are free to adopt it to your
tools.

Small example

```
/** @var array */
$errors = $model->getErrors();

throw new JsonHttpException(400, $errors);
```

## Perticipation and development

Hope you will find this set of tools helpful.

If you have suggestions welcome to issues on github.

If you wish to improve thia package feel free to submit pull requests.
