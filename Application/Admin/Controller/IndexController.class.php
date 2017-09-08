<?php
namespace Admin\Controller;

class IndexController extends AdminController {

    /*登录就可以*/
    static protected $allow_login = array('index' , 'dash');

    protected function _initialize(){
        parent::_initialize();
    }

    public function index(){
        $this->display();
    }
}