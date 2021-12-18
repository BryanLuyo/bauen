<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class common_function extends Model {

    public function RandomString($number) {
        $Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
        $QuantidadeCaracteres = strlen($Caracteres);
        $QuantidadeCaracteres--;
        $Hash = NULL;
        for ($x = 1; $x <= $number; $x++) {
            $Posicao = rand(0, $QuantidadeCaracteres);
            $Hash .= substr($Caracteres, $Posicao, 1);
        }
        //return $Hash . '#' . time();
        return $Hash;
    }

    public function get_table($table_name) {
        $result = DB::table($table_name)
                ->where('is_deleted', '=', 0)
                ->where('is_blocked', '=', 0)
                ->get();
        return $result;
    }
    public function get_table_sort_by($table_name, $field_name, $dir) {
        $result = DB::table($table_name)
                ->where('is_deleted', '=', 0)
                ->where('is_blocked', '=', 0)
                ->orderBy($field_name, $dir)
                ->get();
        return $result;
    }
	 public function get_table_2($table_name) {
        $result = DB::table($table_name)
				->get();
        return $result;
    }
	
    public function update_table($table_name, $data, $field, $id) {
        $result = DB::table($table_name)
                ->where($field, $id)
                ->update($data);
        return $result;
    }

    public function insert_table($table_name, $data) {
        $result = DB::table($table_name)->insertGetId($data);
        return $result;
    }

    public function delete_table($table_name, $id) {
        $result = DB::table($table_name)
                ->where('id', $id)
                ->delete();
        return $result;
    }

    public function find_table_by_field($table_name, $field, $field_value, $id = null, $id_val = null) {
        $result = DB::table($table_name);
        $result->select(DB::raw('count(*) as total_row'));
        $result->where($field, '=', $field_value);
        if (isset($id_val)) {
            $result->where($id, '=', $id_val);
        }
        $finel = $result->first();
        return $finel;
    }

    public function find_details_table_by_field($table_name, $field_name, $field_value, $get_result_type) {
        $result = DB::table($table_name);
        $result->where($field_name, '=', $field_value);
        if ($get_result_type == 1) {
            $value = $result->first();
        } elseif ($get_result_type == 2) {
            $value = $result->get();
        } elseif ($get_result_type == 3) {
            $result->where('status', '=', 1);
            $value = $result->get();
        }
        return $value;
    }

    public function get_dasboard_all() {
        $data['reg_user'] = DB::table('users')->where('status', '=', 1)->count();
        $data['add_posted'] = DB::table('item')->where('status', '=', 1)->count();
        $data['sub_user'] = DB::table('premium_membership')->where('status', '=', '1')->count();
        return $data;
    }

    public function get_dasboard_month() {
        $data['reg_user'] = DB::table('users')->where('status', '=', 1)->whereRaw('YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW())')->count();
        $data['add_posted'] = DB::table('item')->where('status', '=', 1)->whereRaw('YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW())')->count();
        $data['sub_user'] = DB::table('premium_membership')->where('status', '=', '1')->whereRaw('YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW())')->count();
        return $data;
    }

    public function get_dasboard_year() {
        $data['reg_user'] = DB::table('users')->where('status', '=', 1)->whereRaw('YEAR(created_at) = YEAR(NOW())')->count();
        $data['add_posted'] = DB::table('item')->where('status', '=', 1)->whereRaw('YEAR(created_at) = YEAR(NOW())')->count();
        $data['sub_user'] = DB::table('premium_membership')->where('status', '=', '1')->whereRaw('YEAR(created_at) = YEAR(NOW())')->count();
        return $data;
    }

    public function get_dasboard_today() {
        $data['reg_user'] = DB::table('users')->where('status', '=', 1)->whereRaw('YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW()) AND DAY(created_at) = DAY(NOW())')->count();
        $data['add_posted'] = DB::table('item')->where('status', '=', 1)->whereRaw('YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW()) AND DAY(created_at) = DAY(NOW())')->count();
        $data['sub_user'] = DB::table('premium_membership')->where('status', '=', '1')->whereRaw('YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW()) AND DAY(created_at) = DAY(NOW())')->count();
        return $data;
    }

    public function get_user_device($user_id) {
        if ($user_id != "") {
            $user_detail = DB::table('users')->select('device_type', 'device_id')->where('status', '=', 1)->where('id', '=', $user_id)->get();
            if ($user_detail && is_array($user_detail)) {
                return $user_detail[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function count_all_table($table_name, $field, $field_value, $result_type) {
        $from = date('Y-m-d');
        $result = DB::table($table_name);

        if ($result_type == 1) {
            $result->where($field, '=', $field_value);
        } elseif ($result_type == 2) {
            $result->whereBetween('created_at', array($from, $field_value));
        }

        $total = $result->count();
        return $total;
    }

    //     $result->select(DB::raw('count(*) as total_row'));
    //     if ($result_type == 1) {
    //         $result->whereDate($field, '=', $field_value);
    //     } elseif ($result_type == 2) {
    //         $result->whereBetween('created_at', array($from, $field_value));
    //     }
    //     $finel = $result->get();
    //     return $finel;
    // }

    public function curl($url, $data = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (isset($data) && !empty($data)) {
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /*     * ******************************************************************************* */

    function google_maps_search($address, $key = '') {
        $url = sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s', urlencode($address), urlencode($key));
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        return $data;
    }

    function map_google_search_result($geo) {
        if (empty($geo['status']) || $geo['status'] != 'OK' || empty($geo['results'][0])) {
            return null;
        }
        $data = $geo['results'][0];
        $postalcode = '';
        foreach ($data['address_components'] as $comp) {
            if (!empty($comp['types'][0]) && ($comp['types'][0] == 'postal_code')) {
                $postalcode = $comp['long_name'];
                break;
            }
        }
        $location = $data['geometry']['location'];
        $formatAddress = !empty($data['formated_address']) ? $data['formated_address'] : null;
        $placeId = !empty($data['place_id']) ? $data['place_id'] : null;

        $result = [
            'lat' => $location['lat'],
            'lng' => $location['lng'],
            'postal_code' => $postalcode,
            'formated_address' => $formatAddress,
            'place_id' => $placeId,
        ];
        return $result;
    }

    /*     * ******************************************************************************* */

    public function get_map_by_lng_lat($data) {
        //echo '<pre/>'; print_r($data); exit;
        $api_key="AIzaSyCAeL_PSmeFP-hNntq8_v6d5wLf6hBdlhY";
        $map_url ="https://maps.googleapis.com/maps/api/staticmap?key=".$api_key."&size=1500x350&format=JPEG";
        $color = "green";
        $start_point=array();
        $end_point=array();
        $markers = '';
        foreach ($data['markers'] as $marker) {
            $place = $marker['place'];
            $lat = $marker['lat'];
            $long = $marker['long'];
            
            
            $markers .= "&markers=color:$color%7Clabel:$place%7C$lat,$long";
            if (!empty($color)) {
                $color = "yellow";
            }
            if (empty($start_point)) {
                $start_point[] = array(
                    'lat' => $lat,
                    'long' => $long
                );
            } else {
                if (empty($end_point)) {
                    $end_point[] = array(
                        'lat' => $lat,
                        'long' => $long
                    );
                }
            }
        }
        
        $path_points = array_merge($start_point,$end_point);
        $patha="&path=color:red|weight:2";
        foreach($path_points as $path_point){
            $patha.="|".$path_point['lat'].",".$path_point['long'];
        }
        
        $map_url.=$markers;//.$patha;
        $request_id = rand(1, 4000);
        $image_name = time()."request_".$request_id.".jpg";
        copy($map_url,'../uploads/requests/'.$image_name);
        return $image_name;
    }

}
