<?php

namespace app\controllers;

use app\models\Dictionary;
use app\models\UserSession;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use app\models\LoginForm;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['bootstrap'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
            'only' => ['next', 'check', 'start', 'check-start']
        ];

        return $behaviors;

    }


    /**
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }



    private function getUserValues(){
        $test = Yii::$app->session->get('test');
        return $test;
    }

    private function setUserValues($data){
        Yii::$app->session->set('test', $data);
    }

    private function actionError($msg)
    {
        return[
            'message' => $msg,
            'error' => 1
        ];
    }


    public function actionCheck()
    {
        $data = $this->getUserValues();
        if(!$data){
            return $this->actionError('Session not found');
        }

        $model = new UserSession();
        $model->load($data, '');
        $try = $model->incTry();
        $flag = $model->checkValue(Yii::$app->request->get('variant'));
        $this->setUserValues($model->toArray());

        return[
            'result' => $flag,
            'score' => $model->score,
            'errors' => $model->errors,
            'try' => $try
        ];

    }

    public function actionNext()
    {

        $data = $this->getUserValues();

        if(!$data){
            return $this->actionError('Session not found');
        }

        $model = new UserSession();
        $model->load($data, '');
        if(!$model->generateNext()){
            $model->saveResults();
            $this->setUserValues(null);
            return [
                'end' => 1,
                'score' => $model->score,
                'errors' => $model->errors
            ];
        }

        $this->setUserValues($model->toArray());
        return $model->toArray(['variants', 'currentValue', 'score', 'errors']);

    }

    public function actionCheckStart()
    {
        $data = $this->getUserValues();

        if($data){
            return $this->actionError('Тест уже начат');
        }

        return [
            'result' => 1
        ];
    }

    public function actionStart()
    {

        $data = $this->getUserValues();

        if($data){
            return $this->actionError('Тест уже начат');
        }

        $session = UserSession::initSession(Yii::$app->request->get('name'));
        if(!$session){
            return $this->actionError('Session not found');
        }

        $this->setUserValues($session->toArray());

        return[
            'success' => 1
        ];
    }


}
