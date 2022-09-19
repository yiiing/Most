<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * 定时任务服务类
 *
 * Class CrontabService
 * @package Service
 */
class CrontabService
{

    /**
     * 获取数据库配置
     *
     * @return string[]
     */
    public static function getBaseConfig()
    {
//
//        return array(
//            'host' => '127.0.0.1',
//            'charset' => 'utf8mb4',
//            'tablePrefix' => 'aaa_',
//            'database' => 'db_aaa',
//            'username' => 'root',
//            'password' => 'root',
//        );


        return array(
            'host' => '127.0.0.1',
            'charset' => 'utf8mb4',
            'tablePrefix' => 'aaa_',
            'database' => 'aaa_vip_com',
            'username' => 'aaa_vip_com',
            'password' => 'YpT8WKFDZeHJ8F8p',
        );
    }


    /**
     * 证书制作任务计划
     * DoTask...
     *
     * @return void
     */
    public static function doTasks()
    {

        //数据库配置信息
        $db = new \PFinal\Database\Builder(self::getBaseConfig());
        $company = $db->table('company_information')->where('file_type = ?', [2])
            ->orderBy('id asc')
            ->findOne();

        if(null == $company){
            return;
        }

        $typeArr = json_decode($company['type_array']);
        $typeFileArr = json_decode($company['type_file_array']) ?: [];

        //检测交集
        $mapArr = array_diff($typeArr, $typeFileArr);

        //证书制作成功 制作铜牌
        if(empty($mapArr)){

            $flag = 0;
            if (!Storage::exists('/public/'.$company['credit_code'].'/3A铜牌.png')) {
                CertificateService::make3aPrint($company);
                $flag++;
            }

            if (!Storage::exists('/public/'.$company['credit_code'].'/铜牌效果图.png')) {
                CertificateService::make3aShow($company);
                $flag++;
            }

            if($flag == 0){
                //制作已完成
                $db->table('company_information')->update([
                    'file_type' => 3,
                    'updated_at' => time()
                ], 'id = ?', [$company['id']]);
            }
            return;
        }

        //证书名字
        $key = array_rand($mapArr);
        $certificateName = CommonService::getTypeName(false, $mapArr[$key]);
        $suffix = CommonService::getSuffix($mapArr[$key]);


        //制作 企业家或者经理人的证书
        if($mapArr[$key] == 8 || $mapArr[$key] == 9){

            $bool = CertificateService::makeHonestPeople($company, $certificateName, $suffix);
        } else {

            $bool = CertificateService::makeCorporateCredit($company, $certificateName, $suffix);
        }

        if($bool){
            //更新记录
            array_push($typeFileArr, $mapArr[$key]);
            $db->table('company_information')->update([
                'type_file_array' => json_encode($typeFileArr),
                'updated_at' => time()
            ], 'id = ?', [$company['id']]);

            return;
        }

        Log::error($company['name'] . $certificateName . ' 证书制作异常！');
    }




}
