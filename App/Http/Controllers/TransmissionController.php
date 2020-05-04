<?php

namespace App\Http\Controllers;

use App\Data;
use App\Incontact;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TransmissionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $device1 = $request['device1'][0];
        $device2 = $request['device2'][0];
        return  $this->TrasnmitedData($device1, $device2);
    }
    public function TrasnmitedData(array $device1, array $device2)
    {

        $co_ordinates =  array(['latitude_1' => $device1['latitude'], 'longitude_1' => $device1['longitude'], 'latitude_2' => $device2['latitude'], 'longitude_2' => $device2['longitude']]);
        $co_ordinates = $co_ordinates[0];
        $thetaValue = $co_ordinates['longitude_1'] - $co_ordinates['longitude_2'];
        $distance = (sin(deg2rad($co_ordinates['latitude_1'])) * sin(deg2rad($co_ordinates['latitude_2']))) + (cos(deg2rad($co_ordinates['latitude_1'])) * cos(deg2rad($co_ordinates['latitude_2'])) * cos(deg2rad($thetaValue)));
        $miles = acos($distance);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        $calculated_distance = $meters;
        return    $checked_criteria = $this->CheckCriteria($calculated_distance);
        if ($checked_criteria != 0) {
            $details =  $this->storeComputedDetails($device1, $device2, $calculated_distance);
            return    $this->DetailsExist($details['one'], $details['two']);
        } else {
            return 0;
        }
    }

    public function CheckCriteria($finalised_data)
    {

        $default_check_against_distance = 2.0;
        if (($finalised_data <= $default_check_against_distance)) {
            return $finalised_data;
        } else {
            return  0;         // 0 means data can be discarded
        }
    }

    public function storeComputedDetails($device1,  $device2, $distance)
    {

        $already_incontact = $this->has_been_in_Contact($device1['user_id'], $device2['user_id']);
        if ($already_incontact == 1) {
            $time = Session::get('time');
            Incontact::where('user1_id', $device1['user_id'] && 'user2_id', $device2['user_id'])->update(['time' => $time, 'distance' => $distance]);
        } else {

            $device_1 = array('latitude' => $device1['latitude'], 'longitude' => $device1['longitude'], 'distance' => $distance, 'location' => $device1['location'], 'user_id' => $device1['user_id']);
            $device_2 = array('latitude' => $device2['latitude'], 'longitude' => $device2['longitude'], 'distance' => $distance, 'location' => $device2['location'], 'user_id' => $device2['user_id']);
            $data_device1 =  Data::create($device_1);
            $data_device2 = Data::create($device_2);
            return array('one' => $data_device1, 'two' => $data_device2);
        }
    }

    public function DetailsExist($device_one, $device_two)
    {

        $result1 =  DB::table('contacts')->where('user_id', $device_one['user_id'])->count();
        $result2 =   DB::table('contacts')->where('user_id', $device_two['user_id'])->count();

        if (($result1 and $result2) != null) {
            $currentTime = date("H:i:s");
            $range = $this->CalculateRange($device_one['user_id']);
            \App\Contact::where('user_id', $device_one['user_id'])->update(['to' => $currentTime, 'range' => $range]);
            \App\Contact::where('user_id', $device_two['user_id'])->update(['to' => $currentTime, 'range' => $range]);
            $this->Incontact($device_one, $device_two, $range);
            $this->deleteDetails($device_one, $device_two);
        } else {
            $currentTime = date("H:i:s");
            $contact_data_device1 = array('from' => $currentTime, 'to' => 0, 'range' => 0, 'distance' => $device_one['distance'], 'user_id' => $device_one['user_id']);
            $contact_data_device2 = array('from' => $currentTime, 'to' => 0, 'range' => 0, 'distance' => $device_two['distance'], 'user_id' => $device_two['user_id']);
            \App\Contact::create($contact_data_device1);
            \App\Contact::create($contact_data_device2);
            Incontact::create(array('user1_id' => $device_one['user_id'], 'user2_id' => $device_two['user_id'], 'time' => " ", 'distance' => 0));
        }
    }
    public function CalculateRange($user_id)
    {
        $currentTime = date("H:i:s");
        $time_from =  \App\Contact::where('user_id', $user_id)->get()->first();
        $time_from = explode(':', $time_from['from']);
        $minutes_from  = (int) $time_from[1];

        $time_to = explode(':', $currentTime);
        $minutes_to = (int) $time_to[1];

        if ($minutes_to  > $minutes_from) {
            $time =    $minutes_difference = $minutes_to - $minutes_from;
            Session::put('time', $time);
            return $time;
        } else {
            return 0;
        }
    }

    public function Incontact($device_one, $device_two, $range)
    {
        $data = array('user1_id' => $device_one['user_id'], 'user2_id' => $device_two['user_id'], 'time' => $range, 'distance' => $device_two['distance']);
        if ($range >= 1) {
            \App\Incontact::create($data);
        } else {
            return 0;
        }
    }

    public function deleteDetails($device_one, $device_two)
    {
        DB::table('contacts')->where('user_id', $device_one['user_id'])->delete();
        DB::table('contacts')->where('user_id', $device_two['user_id'])->delete();
        DB::table('data')->where('user_id', $device_one['user_id'])->delete();
        DB::table('data')->where('user_id', $device_two['user_id'])->delete();
    }

    public function  has_been_in_Contact($user_one_id, $user_two_id)
    {
        return    DB::table('incontacts')->where('user1_id', $user_one_id && 'user2_id', $user_two_id)->count();
    }
}
