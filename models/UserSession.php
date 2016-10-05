<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 04.10.16
 * Time: 11:31
 */

namespace app\models;


use yii\base\Model;
use yii\helpers\ArrayHelper;

class UserSession extends Model
{
    /**
     *
     * Очередность вопросов
     *
     * @var []
     */
    public $queue;

    /**
     *
     * Имя пользователя
     *
     * @var string
     */
    public $user;

    /**
     *
     * правильные ответы
     *
     * @var int
     */
    public $score;


    /**
     *
     * ошибки
     *
     * @var int
     */
    public $errors;


    /**
     *
     * Идентификатор текущего
     *
     * @var int
     */
    public $right;

    /**
     *
     * Текущие варианты
     *
     * @var []
     */
    public $variants;


    /**
     *
     * Вопрос
     * @var string
     */
    public $currentValue;


    /**
     *
     * Идентификатор вопроса
     * @var int
     */
    public $questionID;


    /**
     * Попытка
     *
     * @var int
     */
    public $try;

    public function scenarios()
    {
        $scenario = parent::scenarios();
        $scenario[self::SCENARIO_DEFAULT] = $this->attributes();
        return $scenario;
    }

    /**
     * @param $name
     * @return UserSession
     */
    public static function initSession($name)
    {

        if (empty($name)) {
            return null;
        }

        $session = new UserSession();
        $session->user = $name;
        $dictionary = new Dictionary();
        $session->queue = $dictionary->getTestQueue();
        $session->score = 0;
        $session->errors = 0;
        $session->nextValues();

        return $session;
    }

    public function generateNext()
    {
        if (!count($this->variants) || $this->errors > 2) {
            return false;
        }

        return true;
    }


    public function nextValues()
    {
        if (!count($this->queue)) {
            $this->variants = [];
            return false;
        }

        $next = array_pop($this->queue);
        $dictionary = new Dictionary();
        $variants = $dictionary->getVariants($next);
        $this->right = ArrayHelper::getValue($variants, 'right');
        $this->variants = ArrayHelper::getValue($variants, 'elements');
        $this->currentValue = ArrayHelper::getValue($variants, 'value');
        $this->questionID = $next;
        $this->try = 0;

        return true;
    }

    public function checkValue($variant)
    {
        $variant = (int)$variant;
        if ($this->try > 2) {
            return 0;
        }

        if ($variant != $this->right) {
            if ($this->try > 1) {
                $this->errors++;
                $error = new Errors();
                $error->question_id = $this->questionID;
                $error->save();
                $this->nextValues();
            }
            // Сохраняем неправильную попытку
            return 0;
        } else {
            $this->score++;
            $this->try = 2;
            $this->nextValues();
            return 1;
        }


    }

    public function checkAvailability()
    {
        if ($this->errors > 2) {
            return false;
        }

        return true;
    }

    public function saveResults()
    {
        $session = new Sessions();
        $session->name = $this->user;
        $session->errors = $this->errors;
        $session->success = $this->score;
        $session->save();
    }

    public function incTry()
    {
        return ++$this->try;
    }


}