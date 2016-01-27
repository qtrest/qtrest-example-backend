<?php

namespace app\api\components;

use yii\rest\Serializer;
use yii\db\Query;

class CouponSerializer extends Serializer
{

    /**
     * Serializes a model object.
     * @param Arrayable $model
     * @return array the array representation of the model
     */
    protected function serializeModel($model)
    {
        $data = parent::serializeModel($model);
        $data = prepareModel($data);
        return $data;
    }

    protected function serializeModels($models)
    {
        $models = parent::serializeModels($models);

        foreach ($models as $i => $data) {
            $models[$i] = $this->prepareData($data);
        }
        
        return $models;
    }

    private function prepareData($data)
    {
        $query = new Query;
        $serviceBaseUrl = $query->select('serviceBaseUrl')
            ->from('sourceService')
            ->where('id=:id', [':id' => $data['sourceServiceId']])
            ->createCommand()
            ->queryScalar();
        $serviceName = $query->select('serviceName')
            ->from('sourceService')
            ->where('id=:id', [':id' => $data['sourceServiceId']])
            ->createCommand()
            ->queryScalar();
        $cityName = $query->select('cityName')
            ->from('city')
            ->where('id=:id', [':id' => $data['cityId']])
            ->createCommand()
            ->queryScalar();

        $images = array_map('trim', explode(",", $data['imagesLinks']));
        foreach ($images as $i => $image) {
            $images[$i] = substr_count($image, 'http') > 0 ? ($image) : ($serviceBaseUrl . '/' . trim($image));
        }
        if (trim($data['imagesLinks']) == 'empty') {
            $images = array((substr_count($data['mainImageLink'], 'http') > 0 ? ($data['mainImageLink']) : ($serviceBaseUrl . '/' . trim($data['mainImageLink']))));
        } else {
            $images[] = (substr_count($data['mainImageLink'], 'http') > 0 ? ($data['mainImageLink']) : ($serviceBaseUrl . '/' . trim($data['mainImageLink'])));
        }
        $data['imagesLinks'] = $images;
        $data['mainImageLink'] = substr_count($data['mainImageLink'], 'http') > 0 ? ($data['mainImageLink']) : ($serviceBaseUrl . '/' . trim($data['mainImageLink']));
        $data['pageLink'] = substr_count($data['pageLink'], 'http') > 0 ? ($data['pageLink']) : ($serviceBaseUrl . '/' . trim($data['pageLink']));

        $data['serviceName'] = $serviceName;
        $data['cityName'] = $cityName;

        if ($data['boughtCount'] == "") {
            $data['boughtCount'] = "0";
        }

        return $data;
    }

    // /**
    //  * Serializes the validation errors in a model.
    //  * @param Model $model
    //  * @return array the array representation of the errors
    //  */
    // protected function serializeModelErrors($model)
    // {
    //     $this->response->setStatusCode(422, 'Data Validation Failed.');
    //     $result = [];
    //     foreach ($model->getFirstErrors() as $name => $message) {
    //         $result[] = [
    //             'field' => $name,
    //             'message' => $message,
    //         ];
    //     }
    //     return $result;
    // }

}
