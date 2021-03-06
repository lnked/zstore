<?php

namespace App\Pages;

/**
 * Главная страница
 */
class Main extends Base
{

    public function __construct() {
        parent::__construct();


       
        $this->add(new \App\Widgets\WNoliq("wnoliq"));
        $this->add(new \App\Widgets\WPlannedDocs("wplanned"));
        $this->add(new \App\Widgets\WHLItems("whlitems"));
    }

    public function getPageInfo() {
        return "Статистика на  начало дня";
    }

}
