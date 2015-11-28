<?php
/**
 * Created by PhpStorm.
 * User: stalk
 * Date: 28.11.2015
 * Time: 0:11
 */
use yii\grid\GridView;

echo GridView::widget([
    'id' => 'gridNew',
    'dataProvider' => $arrayProviderNew,
    'layout' => '{items}',
    'columns' => [
        ['attribute' => 'name', 'label' => 'Сервис'],
        ['attribute' => 'today', 'label' => 'Сегодня'],
        ['attribute' => 'week', 'label' => 'За неделю'],
        ['attribute' => 'month', 'label' => 'За месяц']
    ]
]);
echo GridView::widget([
    'id' => 'gridArchive',
    'dataProvider' => $arrayProviderArchived,
    'layout' => '{items}',
    'options' => ['style' => 'display:none'],
    'columns' => [
        ['attribute' => 'name', 'label' => 'Сервис'],
        ['attribute' => 'today', 'label' => 'Сегодня'],
        ['attribute' => 'week', 'label' => 'За неделю'],
        ['attribute' => 'month', 'label' => 'За месяц']
    ]
]);