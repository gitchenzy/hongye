<?php
// 本类由系统自动生成，仅供测试用途
namespace Hy\Controller;
class LoginController extends CommonController {
    protected function _initialize(){
        parent::_initialize();
    }
    public function index(){

        $info['appid'] = 'wxd624dd8270d22b25';
        $info['url'] = 'http://www.hybbms.com/index.php/Hy/Login/w_login';
        $this->assign('info',$info);
        $this -> display();
    }

    public function w_login(){
        //获取到code然后进行交换access_token
        $code = I('code');
        $appid= 'wxd624dd8270d22b25';
        $secret=  'cc07647d342712a9e0aa5f5e50a25eeb';

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
        $info = curl_request($url);
        $info = json_decode($info);
        $token = $info -> access_token;
        $openid = $info -> openid;
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}&lang=zh_CN";
        $data = curl_request($url);
        $data = json_decode($data);

        //线判断是否有，没有则注册
        $res = M('users') -> where(['openid'=>$data -> openid,'del'=>0]) -> find();
        if($res){
            $current_user['user_id'] = $res['id'];
            session("user" , $current_user);
            $back_url = session('back_url');
            if($back_url){
                session('back_url',null);
                $this->redirect($back_url);
            }else{
                $this->redirect( 'Center/index');
            }


        }else{
            $user['pic'] = $data -> headimgurl;
            $user['nick_name'] = $this -> filter($data -> nickname);
            $user['openid'] = $data -> openid;
            $user['sex'] = $data -> sex;
            $user['reg_time'] = time();
            $user['login_time'] = time();
            $user['status'] = 1;
            $user['grade_id'] = M('user_grade') -> where(['level'=>1]) -> getfield('id');
            $user_id = M('users') -> add($user);
            $current_user['user_id'] = $user_id;
            session("user" , $current_user);//写入Session 登录成功
            if($user_id){
                $this->redirect( 'Center/index');
            }
        }

    }
    //过滤微信昵称
    private function filter($str) {
        if($str){
            $name = $str;
            $name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $name);
            $name = preg_replace('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S','?', $name);
            $return = json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#ie","",json_encode($name)));
            if(!$return){
                return json_decode($return);
            }
        }else{
            $return = '';
        }
        return $return;

    }


}