<?php

namespace app\models;

use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\StaleObjectException;
use yii\helpers\VarDumper;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Метод аутентификация пользователя для Rest API
     *
     * @return string|void - строка auth_key или null если данные не верны
     * @throws Throwable
     * @throws Exception
     * @throws StaleObjectException
     */
    public function auth() {
        if ($this->validate()) {
            $token = Yii::$app->security->generateRandomString();
            if ($this->_user->updateAttributes(['auth_key' => $token])) {
                return $token;
            } else {
                return null;
            }
        }
        return null;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Возвращает ошибки валидации в строке
     *
     * @return string|null
     */
    public function getReadableErrors()
    {
        $error_message = '';

        if (!empty($this->errors)) {
            foreach ($this->errors as $attribute => $error) {
                $error_message .= $attribute . ': ' . implode(', ', $error) . ' ';
            }
            return $error_message;
        }
        return null;
    }
}
