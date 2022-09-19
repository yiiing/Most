<?php

namespace App\Service;

use App\Models\CompanyInformation;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as guzzleClient;

/**
 * 公共服务类
 *
 * Class CommonService
 * @package Service
 */
class CommonService
{


    /**
     * 接口获取企业基础信息
     *
     * @param $company
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getCompanyInfo($company)
    {

//        $company = '茸磐（上海）科技有限公司';

        $host = "http://api.81api.com";
        $path = "/getCompanyBaseInfo/{$company}/";
        $url = $host . $path;
        $guzzleClient = new guzzleClient([
            'timeout' => 5.0
        ]);
        // 同步请求方式
        $response = $guzzleClient->get(
            $url,
            [
                'headers' => ['Authorization' => 'APPCODE 0be26cb8bf5e484bb6ebf1a974a390ad'],
                'http_errors' => false
            ]
        );
        $code = $response->getStatusCode();
        $body = $response->getBody();
        $content = $body->getContents();

        if($code == 200) {
            return $content;
        }

        return '';
    }




    /**
     * 获取证书类型别名
     *
     * @param $returnAll
     * @param $key
     * @return string|string[]
     */
    public static function getTypeName($returnAll = false, $key)
    {
        $map = [
            1 => '企业信用等级',
            2 => '企业资信等级',
            3 => '诚信经营示范单位',
            4 => '质量服务诚信单位',
            5 => '重合同守信用企业',
            6 => '重服务守信用企业',
            7 => '重质量守信用企业',
            8 => '诚信企业家',
            9 => '诚信经理人',
            10 => '中国诚信供应商',
        ];

        if ($returnAll) {
            return $map;
        }
        return isset($map[$key]) ? $map[$key] : '';
    }

    /**
     * 证书编号后缀
     *
     * @param $key
     * @return string
     */
    public static function getSuffix($key)
    {
        $map = [
            1 => '',
            2 => '-C',
            3 => '-O',
            4 => '-I',
            5 => '-A',
            6 => '-T',
            7 => '-Q',
            8 => '-E',
            9 => '-M',
            10 => '-S',
        ];

        return isset($map[$key]) ? $map[$key] : '';
    }



    /**
     * --------------------更新时间戳----------------------
     *
     * 获取工单数据库配置信息
     *
     * @return string[]
     */
    public static function getAdminDBConfig()
    {

        return array(
            'host' => '127.0.0.1',
            'charset' => 'utf8mb4',
            'tablePrefix' => 'ey_',
            'database' => 'www_ch315_org',
            'username' => 'www_ch315_org',
            'password' => 'AiSjs78iPSiLbZjP',
        );
    }

    public static function updateTimeToSql()
    {
        $adDB = new \PFinal\Database\Builder(self::getAdminDBConfig());
        $list = $adDB->table('archives')->field('aid')->findAll();

        foreach ($list as $item) {

            $res = $adDB->table('archives')->update([
                'add_time' => mt_rand(1662307200, 1663171200),
                'update_time' => mt_rand(1662307200, 1663171200),
            ], 'aid = ?', [$item['aid']]);

            var_dump($res);
        }

        echo '执行完毕';
    }



}
