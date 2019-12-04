<?php
namespace App\Http\Controllers\api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProvinceDistrictWard;
use GuzzleHttp\Client;

class ProvinceDistrictWardController extends Controller
{
    // public function getProvince(){
    //     $province_list = ProvinceDistrictWard::all();
    //     if ($province_list->isEmpty()) {
    //         return response()->json(
    //             ['error' => 'provinces are null']
    //         );
    //     }
    //     return response()->json($province_list);
    // }
    // public function getDistrictByProvince($provinceId) {
    //     while(ProvinceDistrictWard::where('province_id', $provinceId)->exists()) {
    //         $district_list = ProvinceDistrictWard::where('province_id', $provinceId)->get();
    //         $district_name_id_list = [];
    //         if ($district_list->isEmpty()) {
    //             return response()->json(
    //                 ['error' => 'districts are null']
    //             );
    //         }
    //         foreach ($district_list as $item) {
    //             $district_name_id_list[] = [
    //                 'district_name' => $item['district_name'],
    //                 'district_id' => $item['district_id']
    //             ];
    //         }
    //         return response()->json($district_name_id_list);
    //     }
    //     return response()->json(['status' => false]);
    // }
    // public function getWardbyDistrict($districtId){
    //     while(ProvinceDistrictWard::where('district_id', $districtId)->exists()) {
    //         $ward_list = ProvinceDistrictWard::where('district_id', $districtId)->get();
    //         $district_name_list = [];
    //         if ($ward_list->isEmpty()) {
    //             return response()->json(
    //                 ['error' => 'wards are null']
    //             );
    //         }
    //         foreach ($ward_list as $item) {
    //             $district_name_list[] = [
    //                 'ward_name' => $item['ward_name']
    //             ];
    //         }
    //         return response()->json($district_name_list);
    //     }
    //     return response()->json(['status' => false]);
    // }


    public function getDistrict() {

        $client = new \GuzzleHttp\Client(["base_uri" => "https://console.ghn.vn/api/v1/apiv3"]);
        $res = $client->request('POST', 'https://dev-online-gateway.ghn.vn/apiv3-api/api/v1/apiv3/GetDistricts',[ 'json' => [ 'token' => 'TokenStaging' ]]);

        $response = json_decode($res->getBody(), true);

        return response()->json($response);
    }

    
}