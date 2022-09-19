<?php

namespace App\Http\Controllers;

use App\Service\CertificateService;
use App\Service\CompanyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageCompanyController extends Controller
{


    /**
     * 新增企业
     *
     * @param $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCompany($name)
    {
        $name =  urldecode($name);

        $error = '';
        $bool = CompanyService::createCompany($name, $error);

        if($bool) {
            return response()->json(['status' => true, 'data' => '', 'code' => 200, 'msg' => '新增成功']);
        }

        return response()->json(['status' => false, 'data' => '', 'code' => 1002, 'msg' => $error]);
    }


    /**
     * 证书制作
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function certificateMakeStart(Request $request)
    {

        $companyId = $request->get('id');
        $company = DB::table('company_information')->find($companyId);
        if(null == $company){
            return response()->json(['status' => false, 'data' => '', 'code' => 1010, 'msg' => '企业不存在']);
        }
        //状态检测
        if($company->file_type != 1){
            return response()->json(['status' => false, 'data' => '', 'code' => 1011, 'msg' => '暂不可操作']);
        }

        //证书类型
        $type = $request->get('type', '10');

        //生成 证书编号 certificate_no
        $numbers=range(1,$type);
        $deadlineDate = Carbon::parse('+3 year')->toDateString();
        $updateData = [
            'certificate_no' => 'DPZX'.date('Ymd', time()).substr($company->credit_code, '-4'),
            'braid_date' => Carbon::now()->format('Y年m月d日'),
            'deadline_date' => Carbon::parse($deadlineDate)->subDay(1)->format('Y年m月d日'),
            'type' => $type,
            'file_type' => 2, //开始制作
            'type_array' => json_encode($numbers),
            'updated_at' => time(),
        ];

        $int = DB::table('company_information')->where('id', $companyId)->update($updateData);

        //数据更新操作
        if($int == 0){
            return response()->json(['status' => false, 'data' => '', 'code' => 1011, 'msg' => '数据更新失败']);
        }

        return response()->json(['status' => true, 'data' => 'SUCCESS', 'code' => 200, 'msg' => '操作成功']);
    }



    /**
     * 重启证书制作
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function certificateMakeReStart(Request $request)
    {
        $companyId = $request->get('id');
        $company = DB::table('company_information')->find($companyId);
        if(null == $company){
            return response()->json(['status' => false, 'data' => '', 'code' => 1010, 'msg' => '企业不存在']);
        }
        //暂时不可以重启制作
        if(empty($company->verify_address)){
            return response()->json(['status' => false, 'data' => '', 'code' => 1011, 'msg' => '暂时不可操作']);
        }

        //更新数据
        $updateData = [
            'type_file_array' => json_encode([]),
            'file_type' => 2, //开始制作
            'updated_at' => time(),
        ];

        $int = DB::table('company_information')->where('id', $companyId)->update($updateData);

        //数据更新操作
        if($int == 0){
            return response()->json(['status' => false, 'data' => '', 'code' => 1011, 'msg' => '数据更新失败']);
        }

        //重新制作 二维码内容
        @CertificateService::makeCecbidQrCodeReboot($company->verify_address, $company->credit_code);
        return response()->json(['status' => true, 'data' => 'SUCCESS', 'code' => 200, 'msg' => '操作成功']);
    }





    /**
     * 下载证书物料 TODO...
     *
     * @return void
     * @throws \ZipStream\Exception\FileNotFoundException
     * @throws \ZipStream\Exception\FileNotReadableException
     * @throws \ZipStream\Exception\OverflowException
     */
    public function downloadResourcePack()
    {

//        $arrayCache = Cache::pull('manageUser');
//        $manageUser = DB::table('manage_user')->where('id', [':id' => $arrayCache->userId])->first();

        return CertificateService::downloadResourcePack();
    }





}
