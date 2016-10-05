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
        /*return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];*/
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    private function getUserValues(){
        $test = Yii::$app->session->get('test');
        return $test;
    }

    private function setUserValues($data){
        Yii::$app->session->set('test', $data);
    }

    public function actionError($msg)
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

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new Dictionary();
        print_r($model->getTestQueue());
        print_r($model->getVariants(12));
        /*$model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);*/
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
