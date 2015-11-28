<?php

namespace app\controllers;

use app\models\SourceService;
use app\models\Statistics;
use DateInterval;
use DatePeriod;
use DateTime;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
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
        ];
    }

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

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionReformal()
    {
        return $this->render('reformal');
    }

    public function actionStatistics()
    {
        if (!Yii::$app->request->isAjax) return;
        $currentDay = new DateTime('today');
        $endPeriod = clone $currentDay; //потому что странный пхп меняет исходный объект при присвоении.
        $weekDay = new DateTime('-7 day');
        $weekDays = new DatePeriod($weekDay, new DateInterval('P1D'), $endPeriod->modify('+1 day'));
        $monthDay = new DateTime('-30 day');
        $monthDays = new DatePeriod($monthDay, new DateInterval('P1D'), $endPeriod->modify('+1 day'));
        $data = Statistics::find()->asArray()->all();
        $services = SourceService::find()->select('id, serviceName')->asArray()->all();
        //День
        foreach ($services as $service) {
            $newSum = 0;
            $archiveSum = 0;
            foreach ($data as $i) {
                if ($i['sourceId']===$service['id'] && $i['createDate']===$currentDay->format('Y-m-d')
                    && $i['codeType']==='new'){
                    $newSum += (int)$i['count'];
                }
                if ($i['sourceId']===$service['id'] && $i['createDate']===$currentDay->format('Y-m-d')
                    && $i['codeType']==='archive'){
                    $archiveSum += (int)$i['count'];
                }
            }
            $newDay['new'][$service['serviceName']] = $newSum;
            $newDay['archive'][$service['serviceName']] = $archiveSum;
        }
        //неделя
        foreach ($services as $service) {
            $newSum = 0;
            $archiveSum = 0;
            foreach ($weekDays as $wd) {
                foreach ($data as $i) {
                    if ($i['sourceId'] === $service['id'] && $i['createDate'] === $wd->format('Y-m-d')
                        && $i['codeType']==='new') {
                        $newSum += (int)$i['count'];
                    }
                    if ($i['sourceId'] === $service['id'] && $i['createDate'] === $wd->format('Y-m-d')
                        && $i['codeType']==='archive') {
                        $archiveSum += (int)$i['count'];
                    }
                }
            }
            $newWeek['new'][$service['serviceName']] = $newSum;
            $newWeek['archive'][$service['serviceName']] = $archiveSum;
        }
        //месяц
        foreach ($services as $service) {
            $newSum = 0;
            $archiveSum = 0;
            foreach ($monthDays as $md) {
                foreach ($data as $i) {
                    if ($i['sourceId'] === $service['id'] && $i['createDate'] === $md->format('Y-m-d')
                        && $i['codeType']==='new') {
                        $newSum += (int)$i['count'];
                    }
                    if ($i['sourceId'] === $service['id'] && $i['createDate'] === $md->format('Y-m-d')
                        && $i['codeType']==='archive') {
                        $archiveSum += (int)$i['count'];
                    }
                }
            }
            $newMonth['new'][$service['serviceName']] = $newSum;
            $newMonth['archive'][$service['serviceName']] = $archiveSum;
        }
        foreach ($services as $service) {
            $newArray[] = [
                'name' => $service['serviceName'],
                'today' => isset($newDay['new'][$service['serviceName']])?$newDay['new'][$service['serviceName']]:0,
                'week' => isset($newWeek['new'][$service['serviceName']])?$newWeek['new'][$service['serviceName']]:0,
                'month' => isset($newMonth['new'][$service['serviceName']])?$newMonth['new'][$service['serviceName']]:0,
            ];
            $archiveArray[] = [
                'name' => $service['serviceName'],
                'today' => isset($newDay['archive'][$service['serviceName']])?$newDay['archive'][$service['serviceName']]:0,
                'week' => isset($newWeek['archive'][$service['serviceName']])?$newWeek['archive'][$service['serviceName']]:0,
                'month' => isset($newMonth['archive'][$service['serviceName']])?$newMonth['archive'][$service['serviceName']]:0,
            ];
        }
        $arrayProviderNew = new ArrayDataProvider([
            'allModels' => $newArray,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        $arrayProviderArchived = new ArrayDataProvider([
            'allModels' => $archiveArray,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->renderPartial('_statistics', ['arrayProviderNew' => $arrayProviderNew,
            'arrayProviderArchived' => $arrayProviderArchived
        ]);
    }
}
