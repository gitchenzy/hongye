<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class MassageController extends Controller {
    public function index(){
        $this -> display();
    }
    public function massage(){
        session('index',4);
        
        $this -> display();
    }


}