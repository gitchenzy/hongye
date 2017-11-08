<?php
// 本类由系统自动生成，仅供测试用途
namespace Hy\Controller;
class HelpsController extends CommonController {
    protected function _initialize(){
        parent::_initialize();
        $this -> assign('pos',3);
    }
    public function index(){

        $this -> assign('active',1);
        $this -> display();
    }
    public function helps_2(){

        $this -> assign('active',2);
        $this -> display();
    }
    public function helps_3(){

        $this -> assign('active',3);
        $this -> display();
    }
    public function helps_4(){

        $this -> assign('active',4);
        $this -> display();
    }


}