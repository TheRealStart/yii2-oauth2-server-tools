<?php
/**
 * Created by PhpStorm.
 * User: alegz
 * Date: 7/7/15
 * Time: 10:58 AM
 */

namespace TRS\yii2\oauth2server\tools\oauth2;

use TRS\RestResponse\jsend\Response;
use yii\base\Exception;
use yii\base\UserException;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

/**
 * @par
 * @inheritdoc
 */
class ErrorHandler extends \yii\base\ErrorHandler
{
    public $defaultName    = 'Error';
    public $defaultMessage = 'Something has gone wrong.';

    /**
     * @param \Exception $exception
     */
    protected function renderException($exception)
    {
        //Getting http code
        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = 500;
        }

        //Getting name
        if ($exception instanceof Exception) {
            $name = $exception->getName();
        } else {
            $name = $this->defaultName ?: \Yii::t('yii', 'Error');
        }

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = $this->defaultMessage ?: \Yii::t('yii', 'An internal server error occurred.');
        }

        if (( $jsonMessage = json_decode($message) ) !== null)
            $message = $jsonMessage;

        $errorData = [
            'exception' => $name,
            'message'   => $message,
            'code'      => $code
        ];

        if (YII_DEBUG)
            $errorData = ArrayHelper::merge($errorData, [
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);

        /**
         * Reason for intval usage is described in quoted messages bellow (27-th January)
         *
         * zair [3:58 PM]
         * @alegz: Привет, с сервера кода сбрасывает ошибку, то значение code = типу float например 400.0.
         *
         * [3:58]
         * нужно инт 400
         */
        if ($code >= 500)
            $response = Response::error($message, $errorData, intval($code));
        else
            $response = Response::fail($errorData, intval($code));

        $response->send('json');
    }
} 