<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Coupon;

/**
 * CouponSearch represents the model behind the search form about `app\models\Coupon`.
 */
class CouponSearch extends Coupon
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sourceServiceId', 'cityId'], 'integer'],
            [['createTimestamp', 'lastUpdateDateTime', 'fullTextStr', 'recordHash', 'title', 'shortDescription',
                'longDescription', 'conditions', 'features', 'imagesLinks', 'timeToCompletion', 'mainImageLink',
                'originalPrice', 'discountPercent', 'discountPrice', 'discountType', 'boughtCount',
                'sourceServiceCategories', 'pageLink', 'isArchive'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        //echo '<br/><br/><br/><br/><br/><br/>lo:'.$this->isArchive;
        $query = Coupon::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pageSize' => 12,
			],
        ]);

        //echo '2';
//        if (!($this->load($params) && $this->validate())) {
//            return $dataProvider;
//        }
        //echo '3';

//        if (!empty($_GET)) {
//            print_r($params);
//            echo 'dis:'.$this->discountType;
//            //Yii::$app->end();
//        }

        if ($this->fullTextStr > '') {
            $query->orWhere(['like', 'title', $this->fullTextStr])
                ->orWhere(['like', 'shortDescription', $this->fullTextStr])
                ->orWhere(['like', 'longDescription', $this->fullTextStr])
                ->orWhere(['like', 'conditions', $this->fullTextStr])
                ->orWhere(['like', 'features', $this->fullTextStr]);
        }

        $query->andWhere(['>', 'title', '']);

        if (Yii::$app->controller->action->id == 'archive') {
            $this->isArchive = 1;
        }
        if (Yii::$app->controller->action->id == 'actual') {
            $this->isArchive = 0;
        }
        //echo '<br/><br/><br/><br/><br/><br/>lo:'.$this->isArchive;
        //Yii::$app->end();

        if ($this->cityId == 0) {
            $this->cityId = '';
        }
        if ($this->sourceServiceId == 0) {
            $this->sourceServiceId = '';
        }
//        if ($this->discountType == 0) {
//            $this->discountType = '';
//        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sourceServiceId' => $this->sourceServiceId,
            'cityId' => $this->cityId,
            'isArchive' => $this->isArchive,
            'discountType' => $this->discountType,
            'createTimestamp' => $this->createTimestamp,
            'lastUpdateDateTime' => $this->lastUpdateDateTime,
        ]);

//        $query->andFilterWhere(['like', 'recordHash', $this->recordHash])
//            ->andFilterWhere(['like', 'title', $this->title])
//            ->andFilterWhere(['like', 'shortDescription', $this->shortDescription])
//            ->andFilterWhere(['like', 'longDescription', $this->longDescription])
//            ->andFilterWhere(['like', 'conditions', $this->conditions])
//            ->andFilterWhere(['like', 'features', $this->features])
//            ->andFilterWhere(['like', 'imagesLinks', $this->imagesLinks])
//            ->andFilterWhere(['like', 'timeToCompletion', $this->timeToCompletion])
//            ->andFilterWhere(['like', 'mainImageLink', $this->mainImageLink])
//            ->andFilterWhere(['like', 'originalPrice', $this->originalPrice])
//            ->andFilterWhere(['like', 'discountPercent', $this->discountPercent])
//            ->andFilterWhere(['like', 'discountPrice', $this->discountPrice])
//            ->andFilterWhere(['like', 'boughtCount', $this->boughtCount])
//            ->andFilterWhere(['like', 'sourceServiceCategories', $this->sourceServiceCategories])
//            ->andFilterWhere(['like', 'pageLink', $this->pageLink]);

        return $dataProvider;
    }
}
