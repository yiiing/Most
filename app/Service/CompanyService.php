<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;


/**
 * 信用证书服务类
 *
 * Class CompanyService
 * @package Service
 */
class CompanyService
{

    /**
     * 新增企业信息
     *
     * @param $name
     * @param $error
     * @return false
     */
    public static function createCompany($name, &$error = '')
    {
        //检索是否存在
        $company = DB::table('company_information')->where('name', [':name' => $name])
            ->first();
        if(null != $company){
            $error = '企业已存在';
            return false;
        }

        //接口查询企业信息
        $apiResponse = CommonService::getCompanyInfo($name);
        if($apiResponse == ''){
            $error = '获取企业信息失败';
            return false;
        }

//        $apiResponse = '{"status": true, "data": {"startDate": "2018-06-15", "registerCapital": "100.000000万人民币", "employeeData": {"total": 2, "list": [{"name": "贾茹", "title": "执行董事"}, {"name": "陈修伟", "title": "监事"}]}, "name": "茸磐（上海）科技有限公司", "partnerData": {"total": 1, "list": [{"partnerName": "贾茹", "totalShouldCapital": "100.000000万人民币", "percent": "100.00%", "partnerType": "自然人股东", "totalRealCapital": "-"}]}, "legalPersonName": "贾茹", "changeRecordData": {"total": 0, "list": []}, "registerData": {"status": "存续（在营、开业、在册）", "businessScope": "计算机、网络、通信设备科技领域内的技术开发、技术咨询、技术服务、技术转让；数据处理、维修；电子产品、通信设备、实验设备销售；设计、制作各类广告；安全防范工程、弱电工程；自有房屋租赁；物业管理。\r\n【依法须经批准的项目，经相关部门批准后方可开展经营活动】", "belongOrg": "松江区市场监管局", "registerCapitalCurrency": "人民币", "address": "上海市松江区叶榭镇叶旺路1号三楼", "regType": "有限责任公司（自然人独资）", "registerNo": "310117003896694", "creditNo": "91310117MA1J300TX9", "orgNo": "MA1J300TX", "businessTerm": "2018-06-15至2038-06-14", "registerCapital": "100.000000万人民币"}}}';

        $apiResponseArr = json_decode(trim($apiResponse) ,true);
        //存入数据库
        $insertData = [
            'name' => $name,
            'credit_code' => $apiResponseArr['data']['registerData']['creditNo'],
            'legal_person' => $apiResponseArr['data']['legalPersonName'],
            'registration_authority' => $apiResponseArr['data']['registerData']['belongOrg'],
            'registered_address' => $apiResponseArr['data']['registerData']['address'],

            'json_company' => $apiResponse,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $insertId = DB::table('company_information')->insertGetId($insertData);
        if($insertId == 0){
            $error = '企业信息创建失败';
            return false;
        }
        //创建基础目录备用 生成查询二维码 / 招投标二维码
        @CertificateService::makeQrCode($insertData['credit_code']);
        @CertificateService::makeCecbidQrCode($name, $insertData['credit_code']);

        return true;
    }

}
