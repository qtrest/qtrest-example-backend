<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;

class ChocolifeApi extends Apist
{

    public function getBaseUrl()
    {
        return 'https://www.chocolife.me';
    }

    public function index()
    {
        return $this->get('/', [
            'city'  => Apist::filter('#js-b-header > div.b-logo__city__wrapper > div.b-city__change > a > span')->text()->mb_substr(0, -1),
			'kupons' => Apist::filter('body > div.b-deals__wrapper > ul li')->each([
				/*'discount' => Apist::current()->filter('.e-deal__discount')->text()->mb_substr(0, -1),
				'price' => Apist::current()->filter('.e-deal__price')->text()->mb_substr(0, -1),*/
				'pageLink' => Apist::current()->filter('.b-deal__info > a')->attr('href')->call(function ($href)
                {
                    return $href;
                }),
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