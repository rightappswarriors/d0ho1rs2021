<?php

namespace App\Http\Controllers\Client\Api;
use Session;
use App\Http\Controllers\Controller;
use App\Models\ApplicationForm;
use Illuminate\Http\Request;
use App\Models\Regions;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\CONCatchment;
use App\Models\CONHospital;
use App\Models\Classification;

class ApplicationApiController extends Controller {
    public function check(Request $request) {
        $name = $request->name;
        $applications = ApplicationForm::where('facilityname', $name)->get();
        if(count($applications)) {
            return  response()->json(['message' => 'Facility name no longer available'], 400);
        }
        else {
            return  response()->json(['message' => 'Facility name is safe to use'], 200);
        }
        
    }
    public function fetch(Request $request) {
        $app = [];
        $cities = [];
        $provinces = [];
        $brgy = [];
        $classification = [];
        $subclass = [];
        if($id = $request->appid) {
            $app = ApplicationForm::where('appid', $id)->first();
        }
        if(isset($app->rgnid)) {
            $provinces = Province::where('rgnid', $app->rgnid)->get();
        }
        if(isset($app->provid)) {
            $cities = Municipality::where('provid', $app->provid)->get();
        }
        if(isset($app->cmid)) {
            $brgy = Barangay::where('cmid', $app->cmid)->get();
        }
        if(isset($app->ocid)) {
            $classification = Classification::where('ocid',  $app->ocid)->where('isSub', null)->get();
        }
        if(isset($app->ocid) && isset($app->classid)) {
            $subclass = Classification::where('ocid', $app->ocid)->where('isSub',  $app->classid)->get();
        }
        $con_catchment = CONCatchment::where('appid', $id)->get();
        $con_hospital = CONHospital::where('appid', $id)->get();

        return  response()->json(
                                    [
                                        'application'   => $app,
                                        'provinces'     => $provinces,
                                        'cities'        => $cities,
                                        'brgy'          => $brgy,
                                        'classification'=> $classification,
                                        'subclass'      => $subclass,
                                        'con_catchment' => $con_catchment,
                                        'con_hospital'  => $con_hospital
                                    ], 200
                                );
    }
    public function save(Request $request) {
        if(isset($request->appid)) {
            $appform = ApplicationForm::where('appid', $request->appid)->first();
        }
        else {
            $appform = new ApplicationForm;
        }
        

        $appform->hfser_id              = $request->hfser_id;
        $appform->facilityname          = $request->facilityname;
        $appform->rgnid                 = $request->rgnid;
        $appform->provid                = $request->provid;
        $appform->cmid                  = $request->cmid;
        $appform->brgyid                = $request->brgyid;
        $appform->street_number         = $request->street_number;
        $appform->street_name           = $request->street_name;
        $appform->zipcode               = $request->zipcode;
        $appform->contact               = $request->contact;
        $appform->areacode              = $request->areacode;
        $appform->landline              = $request->landline;
        $appform->faxnumber             = $request->faxnumber;
        $appform->email                 = $request->email;
        $appform->facid                 = $request->facid;
        $appform->cap_inv               = $request->cap_inv;
        $appform->lot_area              = $request->lot_area;
        $appform->noofbed               = $request->noofbed;
        $appform->uid                   = $request->uid;
        $appform->ocid                  = $request->ocid;
        $appform->classid               = $request->classid;
        $appform->subClassid            = $request->subClassid;
        $appform->facmode               = $request->facmode;
        $appform->funcid                = $request->funcid;
        $appform->owner                 = $request->owner;
        $appform->ownerMobile           = $request->ownerMobile;
        $appform->ownerLandline         = $request->ownerLandline;
        $appform->ownerEmail            = $request->ownerEmail;
        $appform->mailingAddress        = $request->mailingAddress;
        $appform->approvingauthoritypos = $request->approvingauthoritypos;
        $appform->approvingauthority    = $request->approvingauthority;
        $appform->hfep_funded           = $request->hfep_funded;
        $appform->draft                 = $request->draft;
        
        // if($request->con_catch) {

        // }

        $appform->save();

        $con_catch = [];
        $con_hospital = [];
        
        foreach($request->con_catch  as $cc) {
            // dd($cc['type']);
            $arr = [
                'appid'         => $appform->appid,
                'type'          => $cc['type'],
                'location'      => $cc['location'],
                'population'    => $cc['population'],
                'isfrombackend' => null
            ];
            array_push($con_catch, $arr);
        }
        foreach($request->con_hospital  as $ch) {
            // dd($cc['type']);
            $arr = [
                'appid'         => $appform->appid,
                'facilityname'  => $ch['facilityname'],
                'location1'     => $ch['location1'],
                'cat_hos'       => $ch['cat_hos'],
                'noofbed1'      => $ch['noofbed1'],
                'license'       => $ch['license'],
                'validity'      => $ch['validity'],
                'date_operation'=> $ch['date_operation'],
                'remarks'       => $ch['remarks']
            ];
            array_push($con_hospital, $arr);
        }
        $conhospital = CONHospital::where('appid', $request->appid)->delete();
        $concatch = CONCatchment::where('appid', $request->appid)->delete();
        CONHospital::insert($con_hospital);
        CONCatchment::insert($con_catch);
        // $appform->save();
        // dd($request);
        // exit;
        return response()->json(
            [
                'applicaiton' => $appform,
                'con_catchment' => $concatch,
                'provinces'     => Province::where('rgnid', $appform->rgnid)->get(),
                'cities'        => Municipality::where('provid', $appform->provid)->get(),
                'brgy'          => Barangay::where('cmid', $appform->cmid)->get(),
                'classification'=> Classification::where('ocid',  $appform->ocid)->where('isSub', null)->get(),
                'subclass'      => Classification::where('ocid', $appform->ocid)->where('isSub',  $appform->classid)->get(),
            ],
            200
        );
    }
}