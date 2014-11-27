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
            [['createTimestamp', 'lastUpdateDateTime', 'recordHash', 'title', 'shortDescription', 'longDescription', 'conditions', 'features', 'imagesLinks', 'timeToCompletion', 'mainImageLink', 'originalPrice', 'discountPercent', 'discountPrice', 'boughtCount', 'sourceServiceCategories', 'pageLink'], 'safe'],
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
        $query = Coupon::find();
		
		$query->andWhere(['>', 'title', '']);
		//$query->limit(50);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pageSize' => 12,
			],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sourceServiceId' => $this->sourceServiceId,
            'cityId' => $this->cityId,
            'createTimestamp' => $this->createTimestamp,
            'lastUpdateDateTime' => $this->lastUpdateDateTime,
        ]);

        $query->andFilterWhere(['like', 'recordHash', $this->recordHash])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'shortDescription', $this->shortDescription])
            ->andFilterWhere(['like', 'longDescription', $this->longDescription])
            ->andFilterWhere(['like', 'conditions', $this->conditions])
            ->andFilterWhere(['like', 'features', $this->features])
            ->andFilterWhere(['like', 'imagesLinks', $this->imagesLinks])
            ->andFilterWhere(['like', 'timeToCompletion', $this->timeToCompletion])
            ->andFilterWhere(['like', 'mainImageLink', $this->mainImageLink])
            ->andFilterWhere(['like', 'originalPrice', $this->originalPrice])
            ->andFilterWhere(['like', 'discountPercent', $this->discountPercent])
            ->andFilterWhere(['like', 'discountPrice', $this->discountPrice])
            ->andFilterWhere(['like', 'boughtCount', $this->boughtCount])
            ->andFilterWhere(['like', 'sourceServiceCategories', $this->sourceServiceCategories])
            ->andFilterWhere(['like', 'pageLink', $this->pageLink]);

        return $dataProvider;
    }
}
