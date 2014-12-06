<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;

class ChocolifeApi extends Apist
{

    public function getBaseUrl()
    {
        return 'https://www.chocolife.me';
    }

                //title
                //shortDescription
                //longDescription
                //originalPrice
                //discountPercent
                //discountPrice
                //boughtCount
                //categories
                //sourceServiceId
                //cityId
                //imagesLinks
                //lastUpdateTimestamp
                //createTimestamp
                //

    public function index()
    {
        return $this->get('/', [
            'city'  => Apist::filter('#js-b-header > div.b-logo__city__wrapper > div.b-city__change > a > span')->text(),
			'coupons' => Apist::filter('body > div.b-deals__wrapper > ul li')->each([
                'title' => Apist::filter('.e-deal__title')->text(),
                'shortDescription' => Apist::filter('.e-deal__text')->text(),
                'longDescription' => 'empty',
				'originalPrice' => Apist::filter('div.b-deal__info > span.e-deal__price.e-deal__price--old')->text(),
                'discountPercent' => Apist::filter('.e-deal__discount')->text(),
                'discountPrice' => Apist::filter('div.b-deal__info > span:nth-child(2)')->text(),
				'pageLink' => Apist::filter('div.b-deal__info > a')->attr('href'),
                'boughtCount' => Apist::filter('div.b-deal__bought > span')->text(),
                'categories' => Apist::current()->attr('data-categories'),
                //'sourceServiceId' => 'chocolife',
                //cityId
                //imagesLinks
                //lastUpdateTimestamp
                //createTimestamp
			]),
            /*'portals'          => Apist::filter('a[title^="Portal:"]')->each([
                'link'  => Apist::current()->attr('href')->call(function ($href)
                {
                    return $this->getBaseUrl() . $href;
                }),
                'label' => Apist::current()->text()
            ]),
            'languages'        => Apist::filter('#p-lang li a[title]')->each([
                'label' => Apist::current()->text(),
                'lang'  => Apist::current()->attr('title'),
                'link'  => Apist::current()->attr('href')->call(function ($href)
                {
                    return 'http:' . $href;
                })
            ]),*/
            //'sister_projects'  => Apist::filter('#mp-sister b a')->each()->text(),
            //'featured_article' => Apist::filter('#mp-tfa')->html()
        ]);
    }
}