<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){

        $this -> assign('title','微信登录');
        $this -> assign('url',C('WX_LOGIN_URL'));
        $this -> assign('appid',C('WX_APP_ID'));
        $this -> display();
    }
    //微信
    public function w_login(){
        //获取到code然后进行交换access_token
        $code = I('code');
        $appid= C('WX_APP_ID');
        $secret=  C('WX_APP_SECRET');

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