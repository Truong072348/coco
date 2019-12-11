<?php
namespace App\Http\Controllers\api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Districts;
use GuzzleHttp\Client;

class ProvinceDistrictWardController extends Controller
{
    public function getProvince(){
        $province_list = Districts::select('ProvinceName', 'ProvinceID')->groupBy('ProvinceName', 'ProvinceID')->get();
        if ($province_list->isEmpty()) {
            return response()->json(
                ['error' => 'provinces are null']
            );
        }
        return response()->json($province_list);
    }
    public function getDistrictByProvince($provinceId) {
        while(Districts::where('ProvinceID', $provinceId)->exists()) {
            $district_list = Districts::select('DistrictName', 'DistrictID')->where('ProvinceID', $provinceId)->groupBy('DistrictID', 'DistrictName')->get();
            $district_name_id_list = [];
            if ($district_list->isEmpty()) {
                return response()->json(
                    ['error' => 'districts are null']
                );
            }
            foreach ($district_list as $item) {
                $district_name_id_list[] = [
                    'district_name' => $item['DistrictName'],
                    'district_id' => $item['DistrictID']
                ];
            }
            return response()->json($district_name_id_list);
        }
        return response()->json(['status' => false]);
    }
    public function getWardbyDistrict($districtId){
        while(Districts::where('DistrictID', $districtId)->exists()) {
            $ward_list = Districts::select('WardName')->where('DistrictID', $districtId)->groupBy('WardName')->get();
            $district_name_list = [];
            if ($ward_list->isEmpty()) {
                return response()->json(
                    ['error' => 'wards are null']
                );
            }
            foreach ($ward_list as $item) {
                $district_name_list[] = [
                    'ward_name' => $item['WardName']
                ];
            }
            return response()->json($district_name_list);
        }

        return response()->json(['status' => false]);
    }

    public function delete($id) {
        $districts = Districts::find($id);
        $districts->delete();

        return response()->json(['status'=> true]);
    }


    public function getDistrict() {

        $client = new \GuzzleHttp\Client(["base_uri" => "https://console.ghn.vn/api/v1/apiv3"]);
        $res = $client->request('POST', 'https://dev-online-gateway.ghn.vn/apiv3-api/api/v1/apiv3/GetDistricts',[ 'json' => [ 'token' => 'TokenStaging' ]]);

        $response = json_decode($res->getBody(), true);

        return response()->json($response);
    }

    public function store(Request $request){
        $dictricts = Districts::create($request->all());

        return  response()->json($dictricts);
    }

    
}