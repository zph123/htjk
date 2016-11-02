<?php
/**
 * Created by PhpStorm.
 * User: SVector
 * Date: 2016.11.1
 * Time: 20:10
 */

namespace app\index\controller;


class Test extends Common
{
    public function onlinetest()
    {
        return view('onlineTest');
    }


    public function add_onlinetest()
    {
var_dump(input());
    }
}