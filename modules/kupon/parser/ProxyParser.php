<?php
/**
 * Created by PhpStorm.
 * User: stalk
 * Date: 22.01.2015
 * Time: 19:09
 */
namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;

class ProxyParser extends Apist {
    protected $baseUrl = 'http://google-proxy.net';

    public function googleProxy() {
        $this->baseUrl = 'http://google-proxy.net';
        $pL = $this->get('/', [
            'data' => Apist::filter('table tbody tr')->each([
                'ip' => Apist::filter('td:nth-child(1)')->text(),
                'port' => Apist::filter('td:nth-child(2)')->text(),
            ])
        ]);
        return $pL['data'];
    }

    //какой-то тупняк на сайте: $(table tbody tr) должен возвращать все tr, а возвращается только первый
    public function freeProxyList() {
        $this->baseUrl = 'http://freeproxylists.net/';
        $pL = $this->get('/', [
            'data' => Apist::filter('.DataGrid tbody tr')->each([
                'ip' => Apist::filter('td:nth-child(1) a')->text(),
                'port' => Apist::filter('td:nth-child(2) a')->text(),
            ])
        ]);
        return $pL['data'];
    }
}