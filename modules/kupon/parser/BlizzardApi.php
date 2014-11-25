<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;

class BlizzardApi extends Apist
{

    public function getBaseUrl()
    {
        return 'https://blizzard.kz';
    }

    public function index()
    {
        return $this->get('/', [
            'city'  => Apist::filter('#main > div.header > div.top_panel > ul > li:nth-child(1) > a')->text()->mb_substr(0, -1),
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