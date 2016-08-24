<?php
/**
 * Created by IntelliJ IDEA.
 * User: chestr
 * Date: 03.06.16
 * Time: 11:50
 */

namespace TRS\yii2\oauth2server\tools;

use yii\helpers\Json;
use yii\web\HttpException;

class JsonHttpException extends HttpException
{
    /**
     * Constructor.
     * @param integer $status HTTP status code, such as 404, 500, etc.
     * @param string|array $message error message
     * @param integer $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($status, $message = null, $code = 0, \Exception $previous = null)
    {
        if (is_array($message))
            $message = Json::encode($message);

        parent::__construct($status, $message, $code, $previous);
    }
}