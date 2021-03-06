<?php
/**
 * Project: shadowsocks-panel
 * Author: Sendya <18x@loacg.com>
 * Time: 2016/3/23 22:14
 */


namespace Controller;

use Helper\Option;
use Helper\Utils;
use Model\Card;

class Api {

    /**
     * 查询 IP 详细信息
     *
     * @JSON
     */
    public function queryCountry(){
        $ipAddress = Utils::getUserIP();
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apistore/iplookupservice/iplookup?ip='.$ipAddress;
        $header = array(
            'apikey: 8c2732c8237d220bb1a281aa6f9ea7ea',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        echo $res;
        exit();
    }

    /**
     * 淘宝自动发货API
     * 创建卡号
     *
     * @JSON
     */
    public function createCard() {
        $CURR_KEY = $_SERVER['CARD_API_KEY'];
        if(!$CURR_KEY) {
            header("HTTP/1.1 405 Method Not Allowed");
            exit();
        }

        $KEY = Option::get('CREATE_CARD_API_KEY');
        if($KEY == null) {
            $KEY = password_hash( Utils::randomChar(12) . time() , PASSWORD_BCRYPT);
            Option::set('CREATE_CARD_API_KEY', $KEY);
        }


        if(strtoupper($KEY)  == strtoupper($CURR_KEY)) {
            $card = new Card();
            $card->card = substr(hash("sha256", time() . Utils::randomChar(10)) . time(),1, 26);
            $card->add_time = time();
            $card->type = intval(trim($_POST['type']));
            $card->info = trim($_POST['info']);
            $card->status = 1;

            $card->save();
            return array('error' => 0, 'message' => 'success', 'card' => $card);
        } else {
            return array('error' => 1, 'message' => 'Bad Request');
        }

    }
}