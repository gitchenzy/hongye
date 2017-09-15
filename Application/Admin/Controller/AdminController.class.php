<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Model\AuthRuleModel;

class AdminController extends Controller {

	protected function _initialize(){
		$user = get_user();

		if ($user == 0) {
			redirect(U("account/Login"));
		}

		$this->assign("user" , $user);
		$config = M("config")->select();
		if ($config!==false) {
			foreach($config as $k => $v) {
				C($k , $v);
			}
		}
		$this->assign("self", __SELF__);
	}
}