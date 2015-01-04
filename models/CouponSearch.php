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
        //echo '<br/><br/><br/><br/><br/><br/>';
        if (!isset($params['CouponSearch'])) {
            $params['CouponSearch'] = [];
        }
        if (!isset($params['CouponSearch']['isArchive'])) {
            if (Yii::$app->controller->action->id == 'archive') {
                //$this->isArchive = 1;
                $params['CouponSearch']['isArchive'] = 1;
            }
            if (Yii::$app->controller->action->id == 'actual') {
                //$this->isArchive = 0;
                $params['CouponSearch']['isArchive'] = 0;
            }
        }
        //print_r($params);
        $query = Coupon::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->fullTextStr > '') {
            $query->orWhere(['like', 'title', $this->fullTextStr])
                ->orWhere(['like', 'shortDescription', $this->fullTextStr])
                ->orWhere(['like', 'longDescription', $this->fullTextStr])
                ->orWhere(['like', 'conditions', $this->fullTextStr])
                ->orWhere(['like', 'features', $this->fullTextStr]);
        }

        $query->andWhere(['>', 'title', '']);

        if ($this->cityId == 0) {
            $this->cityId = '';
        }
        if ($this->sourceServiceId == 0) {
            $this->sourceServiceId = '';
        }
        if ($this->discountType == 0) {
            $this->discountType = '';
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sourceServiceId' => $this->sourceServiceId,
            'cityId' => $this->cityId,
            'isArchive' => $this->isArchive,
            'discountType' => $this->discountType,
            'createTimestamp' => $this->createTimestamp,
            'lastUpdateDateTime' => $this->lastUpdateDateTime,
        ]);

        return $dataProvider;
    }
}
