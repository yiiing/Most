<?php

namespace App\Service;

use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;
use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

/**
 * 信用证书服务类
 *
 * Class CertificateService
 * @package Service
 */
class CertificateService
{

    /**
     * 企业信用 证书生成
     *
     * @param $companyInformation
     * @param $type
     * @param $suffix
     * @return bool
     */
    public static function makeCorporateCredit($companyInformation, $type = '企业信用等级', $suffix = '')
    {

        switch($type){

            case '企业信用等级':
                //bg_img 2480×3508
                $img = Image::make('public/base_bg_v1/企业信用等级.png');
                break;

            case '企业资信等级':
                $img = Image::make('public/base_bg_v1/企业资信等级.png');
                break;

            case '诚信经营示范单位':
                $img = Image::make('public/base_bg_v1/诚信经营示范单位.png');
                break;

            case '质量服务诚信单位':
                $img = Image::make('public/base_bg_v1/质量服务诚信单位.png');
                break;

            case '重合同守信用企业':
                $img = Image::make('public/base_bg_v1/重合同守信用企业.png');
                break;

            case '重服务守信用企业':
                $img = Image::make('public/base_bg_v1/重服务守信用企业.png');
                break;

            case '重质量守信用企业':
                $img = Image::make('public/base_bg_v1/重质量守信用企业.png');
                break;

            case '中国诚信供应商':
                $img = Image::make('public/base_bg_v1/中国诚信供应商.png');
                break;
        }

        // write text
//    $fontPath = 'public/fonts/华康标题宋W9.ttf';
        $fontPath = 'public/fonts/SourceHanSansCN-Normal.ttf';
        //标题
        $title = $companyInformation['name'];
        $font_len = mb_strlen($title); //文字长度
        $x = 1240-48*($font_len);
        $img->text($title, $x, 1082, function($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(96);
            //  $font->color('#6d484b');
            $font->color('#552623');
        });

        //信用代码
        $no = $companyInformation['credit_code'];
        $no_len = mb_strlen("统一社会信用代码：")+strlen($no); //文字长度
        $x = 1240-35*($no_len)/2;
        $img->text('统一社会信用代码：'.$no, $x, 1220, function($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(48);
            $font->color('#552623');
        });


        //黑体纤细
        $fontPathLight = 'public/fonts/SourceHanSans-Light.otf';
        //证书编号
        $dp = $companyInformation['certificate_no'] . $suffix;
        $img->text($dp, 876, 2140, function($font) use ($fontPathLight) {
            $font->file($fontPathLight);
            $font->size(54);
            $font->color('#552623');
        });
        //证书有效日期
        $dp = $companyInformation['braid_date'].'-'. $companyInformation['deadline_date'];
        $img->text($dp, 876, 2340, function($font) use ($fontPathLight) {
            $font->file($fontPathLight);
            $font->size(54);
            $font->color('#552623');
        });

        //插入二维码1
        $qrcodePath = storage_path('app/public/'.$companyInformation['credit_code']).'/qrcode.png';
        $img->insert($qrcodePath, 'top-left', 468, 2666);

        //插入二维码2 站点二维码
        $qrcodePath = storage_path('app/public/'.$companyInformation['credit_code']).'/cecbid_qrcode.png';
        $img->insert($qrcodePath, 'top-left', 868, 2666);

        try{
            $img->save(storage_path('app/public/'.$companyInformation['credit_code']).'/'.$type .'.png');

            //缩小的版本
            $img->resize(496, 702);
            $img->save(storage_path('app/public/'.$companyInformation['credit_code']).'/thumb/'.$type .'.png');

        }catch(Exception $e){
            return false;
        }

        return true;
    }


    /**
     * 诚信 企业家/经理人 证书制作
     *
     * @param $companyInformation
     * @param $people
     * @param $suffix
     * @return bool
     */
    public static function makeHonestPeople($companyInformation, $people = '诚信经理人', $suffix = '')
    {

        //bg_img 2480×3508
        if($people == '诚信企业家'){
            $img = Image::make('public/base_bg_v1/诚信企业家.png');
        } else {
            $img = Image::make('public/base_bg_v1/诚信经理人.png');
        }

        // write text
//    $fontPath = 'public/fonts/华康标题宋W9.ttf';
        $fontPath = 'public/fonts/SourceHanSansCN-Normal.ttf';
        //标题
        $title = $companyInformation['name'];
        $font_len = mb_strlen($title); //文字长度
        $x = 1240-48*($font_len);
        $img->text($title, $x, 1082, function($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(96);
        //  $font->color('#6d484b');
            $font->color('#552623');
        });

        //信用代码
        $no = $companyInformation['credit_code'];
        $no_len = mb_strlen("统一社会信用代码：")+strlen($no); //文字长度
        $x = 1240-35*($no_len)/2;
        $img->text('统一社会信用代码：'.$no, $x, 1220, function($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(48);
            $font->color('#552623');
        });


        //签名
        $fontPathSign = "public/fonts/SOURCEHANSANSSC-BOLD.OTF";
        $sign = $companyInformation['legal_person'] . ' ' .$people;
        $font_len_sign = mb_strlen($sign); //文字长度
        $x = 1288-76*($font_len_sign);
        $img->text($sign, $x, 1825, function($font) use ($fontPathSign) {
            $font->file($fontPathSign);
            $font->size(152);
//        $font->color('#6d484b');
            $font->color('#552623');
        });

        //黑体纤细
        $fontPathLight = 'public/fonts/SourceHanSans-Light.otf';
        //证书编号
        $dp = $companyInformation['certificate_no'] . $suffix;
        $img->text($dp, 876, 2140, function($font) use ($fontPathLight) {
            $font->file($fontPathLight);
            $font->size(54);
            $font->color('#552623');
        });
        //证书有效日期
        $dp = $companyInformation['braid_date'].'-'. $companyInformation['deadline_date'];
        $img->text($dp, 876, 2340, function($font) use ($fontPathLight) {
            $font->file($fontPathLight);
            $font->size(54);
            $font->color('#552623');
        });

        //插入二维码1
        $qrcodePath = storage_path('app/public/'.$companyInformation['credit_code']).'/qrcode.png';
        $img->insert($qrcodePath, 'top-left', 468, 2666);

        //插入二维码2 站点二维码
        $qrcodePath = storage_path('app/public/'.$companyInformation['credit_code']).'/cecbid_qrcode.png';
        $img->insert($qrcodePath, 'top-left', 868, 2666);


        try{
            $img->save(storage_path('app/public/'.$companyInformation['credit_code']).'/'.$people .'.png');

            //缩小的版本
            $img->resize(496, 702);
            $img->save(storage_path('app/public/'.$companyInformation['credit_code']).'/thumb/'.$people .'.png');

        }catch(Exception $e){

            return false;
        }

        return true;
    }


    /**
     * 3a打印PNG图片
     *
     * @return mixed
     */
    public static function make3aPrint($companyInformation)
    {
        //bg_img 2480×3508
        $img = Image::make('public/base_bg_v1/3A铜牌.png');

        //字体加粗版本
        $fontPathNormal = 'public/fonts/SOURCEHANSANSSC-BOLD.OTF';
        //标题
        $title = $companyInformation['name'];
        $font_len = mb_strlen($title); //文字长度
        $x = 2028-84*($font_len);
        $img->text($title, $x, 1245, function($font) use ($fontPathNormal) {
            $font->file($fontPathNormal);
            $font->size(168);
            $font->color('#333333');
        });

        //常规字体
        $fontPath = 'public/fonts/SourceHanSansCN-Normal.ttf';
        //证书编号
        $dp = $companyInformation['certificate_no'];
        $img->text($dp, 975, 2082, function($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(49);
            $font->color('#552623');
        });

        //证书有效日期
        $dp = $companyInformation['braid_date'].'-'. $companyInformation['deadline_date'];
        $img->text($dp, 975, 2174, function($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(49);
            $font->color('#552623');
        });

        try{

            $img->save(storage_path('app/public/'.$companyInformation['credit_code']).'/3A铜牌.png');

        }catch(Exception $e){

            return false;
        }

        return true;
    }


    /**
     * 3a铜牌展示图
     *
     * @return mixed
     */
    public static function make3aShow($companyInformation)
    {
        //bg_img 2480×3508
        $img = Image::make('public/base_bg_v1/铜牌效果图.png');

        //字体加粗版本
        $fontPathNormal = 'public/fonts/SOURCEHANSANSSC-BOLD.OTF';
        //标题
        $title = $companyInformation['name'];
        $font_len = mb_strlen($title); //文字长度
        $x = 2028-84*($font_len);
        $img->text($title, $x, 1245, function($font) use ($fontPathNormal) {
            $font->file($fontPathNormal);
            $font->size(168);
            $font->color('#333333');
        });

        //常规字体
        $fontPath = 'public/fonts/SourceHanSansCN-Normal.ttf';
        //证书编号
        $dp = $companyInformation['certificate_no'];
        $img->text($dp, 975, 2082, function($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(49);
            $font->color('#552623');
        });

        //证书有效日期
        $dp = $companyInformation['braid_date'].'-'. $companyInformation['deadline_date'];
        $img->text($dp, 975, 2174, function($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(49);
            $font->color('#552623');
        });

        try{
            $img->save(storage_path('app/public/'.$companyInformation['credit_code']).'/铜牌效果图.png');

            //缩小的版本
            $img->resize( 406, 300);
            $img->save(storage_path('app/public/'.$companyInformation['credit_code']).'/thumb/'.'铜牌效果图.png');

        }catch(Exception $e){

            return false;
        }

        return true;
    }


    /**
     * 下载资源包
     *
     * @return void
     * @throws \ZipStream\Exception\FileNotFoundException
     * @throws \ZipStream\Exception\FileNotReadableException
     * @throws \ZipStream\Exception\OverflowException
     */
    public static function downloadResourcePack()
    {

        // enable output of HTTP headers
        $options = new \ZipStream\Option\Archive();
        $options->setSendHttpHeaders(true);

        // create a new zipstream object
        $zip = new \ZipStream\ZipStream('example.zip', $options);

        // add a file named 'some_image.jpg' from a local file 'path/to/image.jpg'
        $zip->addFileFromPath('铜牌效果图.jpg', 'public/base_bg/铜牌效果图.jpg');
        $zip->addFileFromPath('诚信企业家.jpg', 'public/base_bg/诚信企业家.jpg');


        // finish the zip stream
        $zip->finish();
    }


    /**
     * 制作查询二维码
     *
     * @param $credit_code
     * @return void
     * @throws \Exception
     */
    public static function makeQrCode($credit_code)
    {
        $writer = new PngWriter();
        // Create QR code
        $qrCode = QrCode::create('https://credit.ch315.org/company/'.$credit_code)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(310)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);
        Storage::makeDirectory('public/'.$credit_code);
        Storage::makeDirectory('public/'.$credit_code . '/thumb');  // 缩略图目录
        $result->saveToFile( storage_path('app/public/'.$credit_code).'/qrcode.png');
    }


    /**
     * 中国招投标信用公示
     *
     * @return void
     * @throws Exception
     */
    public static function makeCecbidQrCode($name, $credit_code)
    {
        $writer = new PngWriter();
        // Create QR code
        $qrCode = QrCode::create('http://credit.cecbid.org.cn/m/searchfor?company='.$name)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(310)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);

        $result->saveToFile( storage_path('app/public/'.$credit_code).'/cecbid_qrcode.png');
    }


    /**
     * 新版本的二维码信息
     *
     * @param $url
     * @param $credit_code
     * @return void
     * @throws Exception
     */
    public static function makeCecbidQrCodeReboot($url, $credit_code)
    {
        $writer = new PngWriter();
        // Create QR code
        $qrCode = QrCode::create($url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(310)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);
        $result->saveToFile( storage_path('app/public/'.$credit_code).'/cecbid_qrcode.png');
    }

}
