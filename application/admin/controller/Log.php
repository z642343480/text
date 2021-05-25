<?php

namespace app\admin\controller;

use think\Db;
use QL\QueryList;
use GuzzleHttp\Exception\RequestException;

use GuzzleHttp\Client;

class Log
{
    private $is_auto = 1;

    public function index()
    {
        return 1;
    }
    //    美国
    public function usa() {
        $area='usa';
        $this->getAmzList($area);
    }
    //    英国
    public function uk() {
        $area='uk';
        $this->getAmzList($area);
    }
    //    德国<?php

namespace app\admin\controller;

use think\Db;
use QL\QueryList;
use GuzzleHttp\Exception\RequestException;

use GuzzleHttp\Client;

class Log
{
    private $is_auto = 1;

    public function index()
    {
        return 1;
    }
    //    美国
    public function usa() {
        $area='usa';
        $this->getAmzList($area);
    }
    //    英国
    public function uk() {
        $area='uk';
        $this->getAmzList($area);
    }
    //    德国
    public function de() {
        $area='de';
        $this->getAmzList($area);
    }
    //    日本
    public function jp() {
        $area='jp';
        $this->getAmzList($area);
    }
    //    西班牙
    public function esp() {
        $area='esp';
        $this->getAmzList($area);
    }
    //    意大利
    public function it() {
        $area='it';
        $this->getAmzList($area);
    }
    //    法国
    public function fr() {
        $area='fr';
        $this->getAmzList($area);
    }
    //    墨西哥
    public function mx() {
        $area='mx';
        $this->getAmzList($area);
    }
    //    加拿大
    public function ca() {
        $area='ca';
        $this->getAmzList($area);
    }

    public function getAmzList($area)
    {
        set_time_limit ( 0);
        $table_update_time='';
        $datacount=0;
        $error_data=array();
        $error_e = '';
        $is_error = '';
        $page = ['1_1000', '1001_10000', '10001_50000', '50000'];
        Db::startTrans();
        foreach ($page as $dokey => $doval) {
            if ($doval == '1_1000') {
                $forcount = 2;
            } else {
                $forcount = 17;
            }
            for ($i = 1; $i <= $forcount; $i++) {
                try {
                    $ql = QueryList::get('https://www.amz123.com/'.$area.'topkeywords-' . $i . '-1-.htm?rank=' . $doval . '&uprank=');
                } catch (RequestException $e) {
                    $error_e = $e;
                    $is_error = "get_error";
                    break 2;
                    exit;
                }
                $TempData = $ql->find('.listdata')->texts()->all();
                $UpdateHtml = $ql->find('.banner-form>div')->texts();
                $UpdateText = $UpdateHtml->take(-1)->all();
                $update_time = substr($UpdateText[2], -16);
                $update_time=substr($update_time,0,strlen($update_time)-6);
                $is_existence=0;
                if($table_update_time==''){
                    $is_existence=Db::table($area.'_list')->where('update_time',$update_time)->count();
                    $table_update_time=$update_time;
                }
                if($is_existence){
                    break 2;
                }
                foreach ($TempData as $key => $val) {
                    $fordata = explode('  ', $val);
                    foreach ($fordata as $k1 => $v1) {
                        if ($v1 == "" || $v1 == " " || $v1 == "\n") {
                            unset($fordata[$k1]);
                        }

                    }

                    $data = array_merge($fordata);
                    foreach ($data as $k1 => $v1) {
                        if ($k1 == 0) {
                            $data['key_words'] = $v1;
                        }
                        if ($k1 == 1) {
                            $data['c_rank'] = $v1;
                        }
                        if ($k1 == 2) {
                            $data['l_rank'] = $v1;
                        }
                        $data['chang'] = null;
                        $data['update_time'] = $update_time;
                        $data['is_auto'] = $this->is_auto;
                        unset($data[$k1]);
                    }
                    $TempData[$key] = $data;

                }
                foreach ($TempData as $key => $val) {
                    $TempData[$key]['key_words'] = trim($val['key_words']);
                    foreach ($val as $k1 => $v1) {
                        if ($k1 == 'chang') {
                            $TempData[$key][$k1] = ((int)$val['l_rank']) - ((int)$val['c_rank']);
                        }

                    }
                }

                try {
                    $IsSuccess = Db::table($area.'_list')->insertAll($TempData);
                    $datacount+=count($TempData);
                } catch (\Exception $e) {
                    print_r($TempData);
                    $error_e = $e;
                    $is_error = "insert_error";
                    $error_data=$TempData;
                    break 2;
                }
                sleep(1);
            }
        }
        if ($is_error == '') {
            $logData = ['error_code' => 200, 'error_time' => date("Y-m-d H:i:s"), 'o_type' => $this->is_auto, 'remark' => '任务执行成功', 'is_success' => 1,'data_count'=>$datacount,'area'=>$area];
            $IsSuccess = Db::table('log')->insert($logData);
            Db::commit();
        } else {
            if ($is_error == 'insert_error') {
                Db::rollback();
                $logData = ['error_code' => 10000, 'error_time' => date("Y-m-d H:i:s"), 'o_type' => $this->is_auto, 'remark' => '任务执行失败' . $error_e->getMessage(), 'is_success' => 0,'data_count'=>$datacount,'area'=>$area];
                $IsSuccess = Db::table('log')->insert($logData);

            }
            if ($is_error == 'get_error') {
                Db::rollback();
                if ($e->hasResponse()) {
                    $error_code = $e->getCode();
                    $error_remark = $e->getMessage();
                    $ErrorData = ['error_code' => $error_code, 'error_time' => date("Y-m-d H:i:s"), 'o_type' => $this->is_auto, 'remark' => $error_remark, 'is_success' => 0,'data_count'=>$datacount,'area'=>$area];
                    $IsSuccess = Db::table('log')->insert($ErrorData);

                }
            }
        }

    }

}
