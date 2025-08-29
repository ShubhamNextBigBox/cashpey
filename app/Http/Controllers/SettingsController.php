<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
 
class SettingsController extends Controller
{       

        public function settings(Request $request){
            $data['page_info'] = pageInfo('Master Category',$request->segment(1));
            return view('settings.settingsMenu')->with($data);  
        }
    
        /*functions for branches start*/

        public function index(Request $request){
            $data['branch_list'] = DB::table('lms_branches')->orderBy('id', 'desc')->get();
            $data['page_info'] = pageInfo('Branch List',$request->segment(1));
            return view('settings.branches')->with($data); 
        }

        public function getAllBranches(){
           $data = DB::table('lms_branches')->orderBy('id', 'desc')->get();
           return response()->json($data);
           echo  $output =  getAllBranches($data);
           
        }
 
        public function branchStatusUpdate(Request $request){ 
            $query =DB::table('lms_branches')->where('branchId',$request->branchId)->update(['status'=>$request->status]); 
            if($query){
                actLogs('branches','branch status updated',$request->all());
                return response()->json(['response'=>'success','message'=>'Branch status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Branch status updation failed']);
            }
        }
       
        public function branchAdd(Request $request){
            $validator =  Validator::make($request->all(),[
                'branchName' =>'required|string|unique:lms_branches,branchName',
            ]);
            
            if($validator->passes()){
                 $data = [
                    'branchId' => mt_rand(11111,99999),
                    'branchName' => ucwords($request->branchName),
                    'status' => 1,
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ];
                 DB::table('lms_branches')->insert($data);
                 actLogs('branches','branch added',$data);
                 return response()->json(['response'=>'success','message'=>'Branch added successfully']);
            }else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
        }

        public function branchEdit(Request $request)
        {
            $query =  DB::table('lms_branches')->select('branchId','branchName')->where('branchId',$request->branchId)->get();
            if($query->isNotEmpty()){
                return response()->json(['response'=>'success','values'=>$query]);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

       
        public function branchUpdate(Request $request)
        {   
            $validator =  Validator::make($request->all(),[
                'branchName' =>'required|string',
                'branchId' =>'required',
                'updatedOn' => dt()
            ]);
             if($validator->passes()){
                 actLogs('branches','branch status updated',$request->all());
                $query =  DB::table('lms_branches')->where('branchId',$request->branchId)->update(['branchName'=>$request->branchName,'updatedOn' => dt()]);
                if($query){
                    return response()->json(['response'=>'success','message'=>'Branch updated successfully']);
                }
            }
            else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
             
        }
         
        public function branchDelete(Request $request)
        {
            $query =  DB::table('lms_branches')->where('branchId',$request->branchId)->delete();
            if($query){
                actLogs('branches','branch delete',$request->all());
                return response()->json(['response'=>'success','message'=>'Branch deleted successfully']);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

    /*functions for branches end*/
 
      /*functions for modules start*/

        public function modules(Request $request){
            $data['modulesData'] = DB::table('lms_modules')->orderBy('id', 'desc')->get();
            $data['page_info'] = pageInfo('Modules List',$request->segment(1));
            return view('settings.modules')->with($data); 
        }
 
        public function modulesStatusUpdate(Request $request){ 
            $query =DB::table('lms_modules')->where('modulesId',$request->modulesId)->update(['status'=>$request->status]); 
            if($query){
                 actLogs('settings','modules status updated',$request->all());
                return response()->json(['response'=>'success','message'=>'Modules status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Modules status updation failed']);
            }
        }
       
        public function modulesAdd(Request $request){
            $validator =  Validator::make($request->all(),[
                'modulesName' =>'required|string|unique:lms_modules,modulesName',
                'modulesUrl' =>'required',
            ]);

             
            if($validator->passes()){

                 $data = [
                    'modulesId' => mt_rand(11111,99999),
                    'modulesParentId' => (!empty($request->moduleParent)? $request->moduleParent : null),
                    'modulesName' => ucwords($request->modulesName),
                   // 'modulesUrl' => Str::slug($request->modulesUrl),
                    'modulesUrl' => $request->modulesUrl,
                    'status' => 1,
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ];
                 DB::table('lms_modules')->insert($data);
                 actLogs('settings','modules added',$data);
                 return response()->json(['response'=>'success','message'=>'Modules added successfully']);
            }else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
        }

        public function modulesEdit(Request $request)
        {
            $query =  DB::table('lms_modules')->select('modulesId','modulesName','modulesParentId','modulesUrl')->where('modulesId',$request->modulesId)->get();
            if($query->isNotEmpty()){
                return response()->json(['response'=>'success','values'=>$query]);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

       
        public function modulesUpdate(Request $request)
        {   
            $validator =  Validator::make($request->all(),[
                'modulesName' =>'required|string',
                'modulesUrl' =>'required',
                'modulesId' =>'required',
            ]);
             if($validator->passes()){
                $query =  DB::table('lms_modules')->where('modulesId',$request->modulesId)->update(['modulesName'=>$request->modulesName,'modulesParentId'=>$request->moduleParent,'modulesUrl'=>$request->modulesUrl,'updatedOn' => dt()]);
                if($query){
                    actLogs('settings','modules update',$request->all());
                    return response()->json(['response'=>'success','message'=>'Modules updated successfully']);
                }
            }
            else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
             
        }
         
        public function modulesDelete(Request $request)
        {
            $query =  DB::table('lms_modules')->where('modulesId',$request->modulesId)->delete();
            if($query){
                actLogs('settings','modules delete',$request->all());
                return response()->json(['response'=>'success','message'=>'Modules deleted successfully']);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

    /*functions for modules end*/


     /*functions for derpartments start*/

      public function departments(Request $request){
            $data['departmentsData'] =  DB::table('lms_departments')->orderBy('id','desc')->get();
            $data['page_info'] = pageInfo('Departments List',$request->segment(1));
            return view('settings.departments')->with($data); 
        }


        public function departmentsAdd(Request $request){
            $validator =  Validator::make($request->all(),[
                'departmentsName' =>'required|string|unique:lms_departments,departmentsName', 
            ]);
 
            if($validator->passes()){
                 $data = [
                    'departmentsId' => mt_rand(11111,99999),
                    'departmentsName' => $request->departmentsName,
                    'status' => 1,
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ];
                 DB::table('lms_departments')->insert($data);
                 actLogs('settings','department added',$data);
                 return response()->json(['response'=>'success','message'=>'Departments added successfully']);
            }else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
        }

        public function departmentsEdit(Request $request)
        {
            $query =  DB::table('lms_departments')->select('departmentsId','departmentsName')->where('departmentsId',$request->departmentsId)->get();
            if($query->isNotEmpty()){
                return response()->json(['response'=>'success','values'=>$query]);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

       
        public function departmentsUpdate(Request $request)
        {   
            $validator =  Validator::make($request->all(),[
                'departmentsName' =>'required|string', 
                'updatedOn' => dt(),
            ]);
             if($validator->passes()){
                $query =  DB::table('lms_departments')->where('departmentsId',$request->departmentsId)->update(['departmentsName'=>$request->departmentsName,'updatedOn'=>dt()]);
                if($query){
                    actLogs('settings','department update',$request->all());
                    return response()->json(['response'=>'success','message'=>'Departments updated successfully']);
                }
            }
            else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
             
        }
         
        public function departmentsStatusUpdate(Request $request){ 
            $query =DB::table('lms_departments')->where('departmentsId',$request->departmentsId)->update(['status'=>$request->status]); 
            if($query){
                actLogs('settings','department status update',$request->all());
                return response()->json(['response'=>'success','message'=>'Departments status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Departments status updation failed']);
            }
        }
         
        public function departmentsDelete(Request $request)
        {
            $query =  DB::table('lms_departments')->where('departmentsId',$request->departmentsId)->delete();
            if($query){
                actLogs('settings','department delete',$request->all());
                return response()->json(['response'=>'success','message'=>'Department deleted successfully']);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

     /*functions for derpartments ends*/

        public function designations(Request $request){
            $data['designationsData'] = DB::table('lms_designations')->orderBy('id', 'desc')->get();
            $data['departmentsData'] = DB::table('lms_departments')->where('status','1')->orderBy('id', 'desc')->get();

            $data['page_info'] = pageInfo('Designations List',$request->segment(1));
            return view('settings.designations')->with($data); 
        }
 
        public function designationsStatusUpdate(Request $request){ 
            $query =DB::table('lms_designations')->where('designationsId',$request->designationsId)->update(['status'=>$request->status]); 
            if($query){
                actLogs('settings','designation status update',$request->all());
                return response()->json(['response'=>'success','message'=>'Designations status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Designations status updation failed']);
            }
        }
       
        public function designationsAdd(Request $request){
            $validator =  Validator::make($request->all(),[
                'designationsName' =>'required|string|unique:lms_designations,designationsName',
            ]);
 
            if($validator->passes()){
                 $data = [
                    'designationsId' => mt_rand(11111,99999),
                    'designationsParentId' => (!empty($request->designationsParent)? $request->designationsParent : null),
                    'designationsName' => ucwords($request->designationsName),
                    'status' => 1,
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ];
                 DB::table('lms_designations')->insert($data);
                 actLogs('settings','designation added',$data);
                 return response()->json(['response'=>'success','message'=>'Designations added successfully']);
            }else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
        }

        public function designationsEdit(Request $request)
        {
            $query =  DB::table('lms_designations')->select('designationsId','designationsName','designationsParentId')->where('designationsId',$request->designationsId)->get();
            if($query->isNotEmpty()){
                return response()->json(['response'=>'success','values'=>$query]);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

       
        public function designationsUpdate(Request $request)
        {   
            $validator =  Validator::make($request->all(),[
                'designationsName' =>'required|string',
                'designationsId' =>'required',
            ]);
             if($validator->passes()){
                $query =  DB::table('lms_designations')->where('designationsId',$request->designationsId)->update(['designationsName'=>$request->designationsName,'designationsParentId'=>$request->designationsParent,'updatedOn' => dt()]);
                if($query){
                    actLogs('settings','designation update',$request->all());
                    return response()->json(['response'=>'success','message'=>'Designations updated successfully']);
                }
            }
            else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
             
        }
         
        public function designationsDelete(Request $request)
        {
            $query =  DB::table('lms_designations')->where('designationsId',$request->designationsId)->delete();
            if($query){
                actLogs('settings','designation delete',$request->all());
                return response()->json(['response'=>'success','message'=>'Designations deleted successfully']);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

    /*functions for designations end*/
 
    /*functions for company start*/

        public function organisation(Request $request){
            $data['cmpData'] =  DB::table('lms_company_informations')->orderBy('id','desc')->limit(1)->first();
            $data['apiData'] =  DB::table('lms_api')->orderBy('id','desc')->get();
            $data['page_info'] = pageInfo('Organisation Information',$request->segment(1));
            return view('settings.company')->with($data); 
        }

        public function organisationAdd(Request $request){
           
            if(!empty($request->id)){
                $validator =  Validator::make($request->all(),[
                'companyName' =>'required',
                'empIdPrefixes' =>'required',
                'loanNoPrefixes' =>'required',
                'domain' =>'required',
                'email' =>'required',
                ]);
            }else{
                $validator =  Validator::make($request->all(),[
                'companyName' =>'required',
                'empIdPrefixes' =>'required|unique:lms_users_details,userID',
                'loanNoPrefixes' =>'required',
                'logo' =>'required',
                 ]); 
            }

            if($request->hasFile('logo')){ 
               $file =  $request->file('logo');
               $logoName =  $file->getClientOriginalName();
               $filePath =$file->storeAs('logo',$logoName,'public');
               $logo = $filePath;
            }else{
               $logo = $request->oldLogo ?? null;
            }
             
             if($validator->passes()){
                if(!empty($request->id)){
                    $data = [
                        'empIdPrefixes'=>$request->empIdPrefixes,
                        'companyName' =>$request->companyName,
                        'domain' =>$request->domain,
                        'email' =>$request->email,
                        'loanNoPrefixes'=>$request->loanNoPrefixes,
                        'gst'=>$request->gst,
                        'address'=>$request->address,
                        'logo'=>$logo,
                        'accountNo'=>$request->accountNo,
                        'accountName'=>$request->accountName,
                        'bankName'=>$request->bankName,
                        'bankIfsc'=>$request->bankIfsc,
                        'updatedOn' => dt(),
                        ];

                    actLogs('settings','company information',$data);    
                    $query = DB::table('lms_company_informations')->where('id',$request->id)->update($data);
                    if($query){
                        return redirect()->back()->with('success',"Data has been updated");
                    }
                    return redirect()->back()->with('error',"There is some errors.");
                }else{
                    $data = [
                        'empIdPrefixes'=>$request->empIdPrefixes,
                        'companyName' =>$request->companyName,
                        'loanNoPrefixes'=>$request->loanNoPrefixes,
                        'gst'=>$request->gst,
                        'logo'=>$logo,
                        'address'=>$request->address,
                        'accountNo'=>$request->accountNo,
                        'accountName'=>$request->accountName,
                        'bankName'=>$request->bankName,
                        'bankIfsc'=>$request->bankIfsc,
                        'addedOn' => dt(),
                        'addedBy' => Session::get('userID')
                        ];
                    actLogs('settings','company added',$data);    
                    $query = DB::table('lms_company_informations')->insert($data);
                   if($query){
                        return redirect()->back()->with('success',"Organisation has been added");
                    }
                    return redirect()->back()->with('error',"There is some errors.");
                }
            }
            else{
                return redirect()->back()->withErrors($validator)->with('active',"prefixesAdd");   
            }
        }

     /*functions for company end*/

    /*functions for branch start*/

        public function branchTarget(Request $request){
            $data['branches'] =  DB::table('lms_cities')->where('status',1)->orderBy('addedOn','desc')->get();
            $data['branches_target'] =  DB::table('lms_branch_target')->orderBy('id','desc')->get();
            $data['page_info'] = pageInfo('Branch Target',$request->segment(1));
            return view('settings.branchTarget')->with($data); 
        }


            public function branchTargetAdd(Request $request){
                $validator =  Validator::make($request->all(),[
                    'branchId' =>'required', 
                    'target' =>'required',
                ]);
     
                if($validator->passes()){
                     $data = [
                        'branchID' => $request->branchId,
                        'target' => $request->target,
                        'addedOn' => dt(),
                        'addedBy' => Session::get('userID')
                     ];
                     DB::table('lms_branch_target')->insert($data);
                     actLogs('settings','branch target',$data);    
                     return response()->json(['response'=>'success','message'=>'Branch target added successfully']);
                }else{
                     return response()->json(['response'=>'failed','error'=>$validator->errors()]);
                }
            }

            public function branchTargetEdit(Request $request)
            {
                $query =  DB::table('lms_branch_target')->select('branchID','target')->where('branchID',$request->branchID)->get();
                if($query->isNotEmpty()){
                    return response()->json(['response'=>'success','values'=>$query]);
                }else{
                    return response()->json(['response'=>'failed','message'=>'No data found']);
                }
            }

           
            public function branchTargetUpdate(Request $request)
            {   
                $validator =  Validator::make($request->all(),[
                    'branchId' =>'required|string',
                    'target' =>'required',
                    'updatedOn' => dt(),
                ]);
                 if($validator->passes()){
                    $query =  DB::table('lms_branch_target')->where('branchID',$request->branchId)->update(['branchID'=>$request->branchId,'target'=>$request->target,'updatedOn'=>dt()]);
                    if($query){
                        actLogs('settings','branch target update',$request->all());    
                        return response()->json(['response'=>'success','message'=>'Branch Target updated successfully']);
                    }
                }
                else{
                     return response()->json(['response'=>'failed','error'=>$validator->errors()]);
                }
                 
            }
             
            public function branchTargetDelete(Request $request)
            {
                $query =  DB::table('lms_branch_target')->where('branchID',$request->branchID)->delete();
                if($query){
                    actLogs('settings','branch target delete',$request->all());    
                    return response()->json(['response'=>'success','message'=>'Branch target deleted successfully']);
                }else{
                    return response()->json(['response'=>'failed','message'=>'No data found']);
                }
            }
     

        public function sanctionTarget(Request $request){
            $data['users'] =  DB::table('users')
                            ->whereNotIn('role', ['Super Admin'])  // Exclude "Super Admin"
                            ->whereIn('role', ['Credit Manager', 'Sr. Credit Manager'])  // Include "Credit Manager" and "Sr. Credit Manager"
                            ->orderBy('id', 'desc')
                            ->get();

            $data['sanction_target'] =  DB::table('lms_sanction_target')->orderBy('id','desc')->get();
            $data['page_info'] = pageInfo('Sanction Target',$request->segment(1));
            return view('settings.sanctionTarget')->with($data); 
        }


            public function sanctionTargetAdd(Request $request){
                $validator =  Validator::make($request->all(),[
                    'userId' =>'required', 
                    'target' =>'required',
                ]);
     
                if($validator->passes()){
                     $data = [
                        'targetID' => mt_rand(11111,99999),
                        'userID' => $request->userId,
                        'target' => $request->target,
                        'addedOn' => dt(),
                        'addedBy' => Session::get('userID')
                     ];
                     actLogs('settings','sanction target added',$data);    
                     DB::table('lms_sanction_target')->insert($data);
                     return response()->json(['response'=>'success','message'=>'Sanction target added successfully']);
                }else{
                     return response()->json(['response'=>'failed','error'=>$validator->errors()]);
                }
            }

            public function sanctionTargetEdit (Request $request)
            {
                $query =  DB::table('lms_sanction_target')->select('userID','target','targetID')->where('targetID',$request->targetID)->get();
                if($query->isNotEmpty()){
                    return response()->json(['response'=>'success','values'=>$query]);
                }else{
                    return response()->json(['response'=>'failed','message'=>'No data found']);
                }
            }

           
            public function sanctionTargetUpdate(Request $request)
            {   
                $validator =  Validator::make($request->all(),[
                    'userId' =>'required|string',
                    'target' =>'required',
                    'updatedOn' => dt()
                ]);
                 if($validator->passes()){
                    $query =  DB::table('lms_sanction_target')->where('targetID',$request->targetID)->update(['userID'=>$request->userId,'target'=>$request->target,'updatedOn'=>dt()]);
                    if($query){
                        actLogs('settings','sanction target update',$request->all());    
                        return response()->json(['response'=>'success','message'=>'Sanction Target updated successfully']);
                    }
                }
                else{
                     return response()->json(['response'=>'failed','error'=>$validator->errors()]);
                }
                 
            }
             
            public function sanctionTargetDelete(Request $request)
            {
                $query =  DB::table('lms_sanction_target')->where('targetID',$request->targetID)->delete();
                if($query){
                    actLogs('settings','sanction target delete',$request->all());    
                    return response()->json(['response'=>'success','message'=>'Sanction target deleted successfully']);
                }else{
                    return response()->json(['response'=>'failed','message'=>'No data found']);
                }
            }

     /*functions for target ends*/


     /*functions for states start*/

            public function states(Request $request){
                $data['statesData'] = DB::table('lms_states')->orderBy('id', 'desc')->get();
                $data['page_info'] = pageInfo('States List',$request->segment(1));
                return view('settings.states')->with($data); 
            }
     
            public function statesStatusUpdate(Request $request){ 
                $query =DB::table('lms_states')->where('stateID',$request->stateID)->update(['status'=>$request->status]); 
                if($query){
                    actLogs('settings','states status update',$request->all());    
                    return response()->json(['response'=>'success','message'=>'States status updated successfully']);
                 }
                else{   
                   return response()->json(['response'=>'error','message'=>'States status updation failed']);
                }
            }
           
            public function statesAdd(Request $request){
                $validator =  Validator::make($request->all(),[
                    'stateName' =>'required|string|unique:lms_states,stateName',
                    'branchCode' =>'required|unique:lms_states,branchCode',
                ]);

                if($validator->passes()){
                     $data = [
                        'stateID' => mt_rand(11111,99999),
                        'stateName' => ucwords($request->stateName),
                        'branchCode' =>  $request->branchCode,
                        'stampDuty' =>  $request->stampDuty,
                        'status' => 1,
                        'addedOn' => dt(),
                     //   'addedBy' => Session::get('userID')
                     ];
                     DB::table('lms_states')->insert($data);
                     actLogs('settings','states added',$request->all()); 
                     return response()->json(['response'=>'success','message'=>'States added successfully']);
                }else{
                     return response()->json(['response'=>'failed','error'=>$validator->errors()]);
                }
            }

            public function statesEdit(Request $request)
            {
                $query =  DB::table('lms_states')->select('stateID','stateName','branchCode','stampDuty')->where('stateID',$request->stateID)->get();
                if($query->isNotEmpty()){
                    return response()->json(['response'=>'success','values'=>$query]);
                }else{
                    return response()->json(['response'=>'failed','message'=>'No data found']);
                }
            }

           
            public function statesUpdate(Request $request)
            {   
                $validator =  Validator::make($request->all(),[
                    'stateName' =>'required|string',
                    'stateID' =>'required',
                    'branchCode' =>'required',
                ]);
                 if($validator->passes()){
                    $query =  DB::table('lms_states')->where('stateID',$request->stateID)->update(['stateName'=>$request->stateName,'branchCode'=>$request->branchCode,'stampDuty'=>$request->stampDuty]);
                    if($query){
                        actLogs('settings','states update',$request->all()); 
                        return response()->json(['response'=>'success','message'=>'states updated successfully']);
                    }
                }
                else{
                     return response()->json(['response'=>'failed','error'=>$validator->errors()]);
                }
                 
            }
             
            public function statesDelete(Request $request)
            {
                $query =  DB::table('lms_states')->where('stateID',$request->stateID)->delete();
                if($query){
                    actLogs('settings','states delete',$request->all()); 
                    return response()->json(['response'=>'success','message'=>'States deleted successfully']);
                }else{
                    return response()->json(['response'=>'failed','message'=>'No data found']);
                }
            }

    /*functions for states start*/
   
     /*functions for cities start*/

        public function cities(Request $request){
            $data['statesData'] = DB::table('lms_states')->orderBy('id', 'desc')->get();
            $data['citiesData'] = DB::table('lms_cities')->orderBy('addedOn','desc')->get();
            $data['page_info'] = pageInfo('Cities List',$request->segment(1));
            return view('settings.cities')->with($data); 
        }
 
        public function citiesStatusUpdate(Request $request){ 
            $query =DB::table('lms_cities')->where('cityID',$request->id)->update(['status'=>$request->status]); 
            if($query){
                actLogs('settings','cities status update',$request->all()); 
                return response()->json(['response'=>'success','message'=>'Cities status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Cities status updation failed']);
            }
        }
       
        public function citiesAdd(Request $request){
            $validator =  Validator::make($request->all(),[
                'cityName' =>'required|string|unique:lms_cities,cityName',
                'branchCode' =>'required|unique:lms_cities,branchCode',
            ]);
 
            if($validator->passes()){
                 $data = [
                    'stateID' => (!empty($request->stateID)? $request->stateID : null),
                    'cityName' => ucwords($request->cityName),
                    'branchCode' => $request->branchCode,
                    'status' => 1,
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ];
                 DB::table('lms_cities')->insert($data);
                 actLogs('settings','cities added',$request->all()); 
                 return response()->json(['response'=>'success','message'=>'Cities added successfully']);
            }else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
        }

        public function citiesEdit(Request $request)
        {
            $query =  DB::table('lms_cities')->select('cityID','cityName','stateID','branchCode')->where('cityID',$request->id)->get();
            if($query->isNotEmpty()){
                return response()->json(['response'=>'success','values'=>$query]);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

       
        public function citiesUpdate(Request $request)
        {   
            $validator =  Validator::make($request->all(),[
                'cityName' =>'required',
                'branchCode' =>'required',
                'id' =>'required',
            ]);
             if($validator->passes()){
                $query =  DB::table('lms_cities')->where('cityID',$request->id)->update(['cityName'=>$request->cityName,'stateID'=>$request->stateID,'branchCode'=>$request->branchCode]);
                if($query){
                    actLogs('settings','cities update',$request->all()); 
                    return response()->json(['response'=>'success','message'=>'Cities updated successfully']);
                }
            }
            else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
             
        }
         
        public function citiesDelete(Request $request)
        {
            $query =  DB::table('lms_cities')->where('cityID',$request->id)->delete();
            if($query){
                actLogs('settings','cities delete',$request->all()); 
                return response()->json(['response'=>'success','message'=>'Cities deleted successfully']);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

    /*functions for cities end*/


     /*functions for types of lead status ends*/

      public function leadsStatus(Request $request){
            $data['statusData'] =  DB::table('lms_leads_status')->orderBy('id','desc')->get();
            $data['page_info'] = pageInfo('Leads Status',$request->segment(1));
            return view('settings.leadsStatus')->with($data); 
        }


        public function leadsStatusAdd(Request $request){
            $validator =  Validator::make($request->all(),[
                'name' =>'required|string|unique:lms_leads_status,name', 
            ]);
 
            if($validator->passes()){
                 $data = [
                    'name' => ucwords($request->name),
                    'status' => 1,
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ];
                 DB::table('lms_leads_status')->insert($data);
                 actLogs('settings','leads status added',$data); 
                 return response()->json(['response'=>'success','message'=>'Leads status added successfully']);
            }else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
        }

        public function leadsStatusEdit(Request $request)
        {
            $query =  DB::table('lms_leads_status')->select('id','name')->where('id',$request->id)->get();
            if($query->isNotEmpty()){
                return response()->json(['response'=>'success','values'=>$query]);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

       
        public function leadsStatusUpdate(Request $request)
        {   
            $validator =  Validator::make($request->all(),[
                'name' =>'required|string', 
                'updatedOn' => dt(),
            ]);
             if($validator->passes()){
                $query =  DB::table('lms_leads_status')->where('id',$request->id)->update(['name'=>ucwords($request->name),'updatedOn'=>dt()]);
                if($query){
                    actLogs('settings','leads status update',$data); 
                    return response()->json(['response'=>'success','message'=>'Leads status updated successfully']);
                }
            }
            else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
             
        }
         
        public function leadsStatusStatusUpdate(Request $request){ 
            $query =DB::table('lms_leads_status')->where('id',$request->id)->update(['status'=>$request->status]); 
            if($query){
                actLogs('settings','leads status update',$request->all());
                return response()->json(['response'=>'success','message'=>'Leads status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Leads status updation failed']);
            }
        }
         
        public function leadsStatusDelete(Request $request)
        {
            $query =  DB::table('lms_leads_status')->where('id',$request->id)->delete();
            if($query){
                actLogs('settings','leads status delete',$request->all());
                return response()->json(['response'=>'success','message'=>'Leads status deleted successfully']);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

     /*functions for types of lead status ends*/

 
      /*functions for roi ends*/
      public function roi(Request $request){
            $data['roiData'] =  DB::table('lms_roi')->orderBy('id','desc')->get();
            $data['page_info'] = pageInfo('ROI',$request->segment(1));
            return view('settings.roi')->with($data); 
        }


        public function roiAdd(Request $request){
            $validator =  Validator::make($request->all(),[
                'roi' =>'required|unique:lms_roi,roi', 
            ]);
 
            if($validator->passes()){
                 $data = [
                    'roi' => $request->roi,
                    'status' => 1,
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ];
                 DB::table('lms_roi')->insert($data);
                 actLogs('settings','roi added',$data);
                 return response()->json(['response'=>'success','message'=>'ROI added successfully']);
            }else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
        }

        public function roiEdit(Request $request)
        {
            $query =  DB::table('lms_roi')->select('id','roi')->where('id',$request->id)->get();
            if($query->isNotEmpty()){
                return response()->json(['response'=>'success','values'=>$query]);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

       
        public function roiUpdate(Request $request)
        {   
            $validator =  Validator::make($request->all(),[
                'roi' =>'required', 
                'updatedOn' => dt(),
            ]);
             if($validator->passes()){
                $query =  DB::table('lms_roi')->where('id',$request->id)->update(['roi'=>$request->roi,'updatedOn'=>dt()]);
                if($query){
                    actLogs('settings','roi update',$request->all());
                    return response()->json(['response'=>'success','message'=>'ROI updated successfully']);
                }
            }
            else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
             
        }
         
        public function roiStatusUpdate(Request $request){ 
            $query =DB::table('lms_roi')->where('id',$request->id)->update(['status'=>$request->status]); 
            if($query){
                actLogs('settings','roi status update',$request->all());
                return response()->json(['response'=>'success','message'=>'ROI status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'ROI status updation failed']);
            }
        }
         
        public function roiDelete(Request $request)
        {
            $query =  DB::table('lms_roi')->where('id',$request->id)->delete();
            if($query){
                actLogs('settings','roi delete',$request->all());
                return response()->json(['response'=>'success','message'=>'ROI deleted successfully']);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

     /*functions for roi ends*/


         /*functions for approval matrix start*/

        public function approvalMatrixList(Request $request){
            $data['approvalMatrixList'] = DB::table('lms_approval_matrix')->orderBy('id', 'desc')->get();
            $data['departmentsData'] = DB::table('lms_departments')->where(['status'=>'1'])->orderBy('id', 'desc')->get();
            $data['designationsData'] = DB::table('lms_designations')->where(['status'=>'1'])->orderBy('id', 'desc')->get();
            $data['users'] = DB::table('users')->where(['status'=>'1'])->orderBy('id', 'desc')->get();
            $data['page_info'] = pageInfo('Approval Matrix List',$request->segment(1));
            return view('settings.approvalMatrix')->with($data); 
        }

        public function getUsersByDesignation($designations)
        {
            $users = DB::table('lms_users_details')->where('designation', $designations)->pluck('fullName','userID');
            return response()->json($users);
        }
       
        public function approvalMatrixStatusUpdate(Request $request){ 
            $query =DB::table('lms_approval_matrix')->where('approvalID',$request->approvalID)->update(['status'=>$request->status]); 
            if($query){
                actLogs('settings','approval matrix status update',$request->all());
                return response()->json(['response'=>'success','message'=>'Approval matrix status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Approval matrix status updation failed']);
            }
        }
       
        public function approvalMatrixAdd(Request $request){
            $validator =  Validator::make($request->all(),[
                'department' =>'required',
                'designation' =>'required',
                'users' =>'required',
                'minLoanAmount' =>'required',
                'maxLoanAmount' =>'required',
 
            ]);
            
            if($validator->passes()){
                 $data = [
                    'approvalID' => randomNo(0,9),
                    'department' => $request->department,
                    'designation' => $request->designation,
                    'users' => json_encode($request->users),
                    'rangeFrom' => $request->minLoanAmount,
                    'rangeTo' => $request->maxLoanAmount,
                    'status' => 1,
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ];

                 actLogs('settings','approval matrix added',$data);
                 DB::table('lms_approval_matrix')->insert($data);
                 return response()->json(['response'=>'success','message'=>'Approval Matrix added successfully']);
            }else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
        }

        public function approvalMatrixEdit(Request $request)
        {
            $query =  DB::table('lms_approval_matrix')->where('approvalID',$request->approvalID)->get();
            if($query->isNotEmpty()){
                return response()->json(['response'=>'success','values'=>$query]);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

       
        public function approvalMatrixUpdate(Request $request)
        {   
            $validator =  Validator::make($request->all(),[
                'department' =>'required',
                'designation' =>'required',
                'users' =>'required',
                'minLoanAmount' =>'required',
                'maxLoanAmount' =>'required',
                'approvalID' =>'required',
            ]);
             if($validator->passes()){    
                $data = [
                    'department' => $request->department,
                    'designation' => $request->designation,
                    'users' => json_encode($request->users),
                    'rangeFrom' => $request->minLoanAmount,
                    'rangeTo' => $request->maxLoanAmount,
                    'updatedOn' => dt(),
                 ];

                $query =  DB::table('lms_approval_matrix')->where('approvalID',$request->approvalID)->update($data);
                if($query){
                    actLogs('settings','approval matrix update',$data);
                    return response()->json(['response'=>'success','message'=>'Approval Matrix updated successfully']);
                }
            }
            else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
             
        }
         
        public function approvalMatrixDelete(Request $request)
        {
            $query =  DB::table('lms_approval_matrix')->where('approvalID',$request->approvalID)->delete();
            if($query){
                 actLogs('settings','approval matrix delete',$request->all());
                return response()->json(['response'=>'success','message'=>'Approval Matrix deleted successfully']);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }

    /*functions for approval matrix end*/

     /* function for change password starts*/
 
     public function changePassword(Request $request){

       $data['page_info'] = pageInfo('Change Password',$request->segment(1));
       return view('settings.changePassword')->with($data);  
     }

     public function updatePassword(Request $request){
           $validator = Validator::make($request->all(), [
            'currentPass' => 'required',
            'newPass' => 'required',
            'confPass' => 'required|same:newPass', 
            ], [
                'currentPass.required' => 'Current password is required.',
                'newPass.required' => 'New password is required.',
                'confPass.required' => 'Please confirm your new password.',
                'confPass.same' => 'The confirmation password does not match the new password.',
            ]);

             if($validator->passes()){

                 $user = Auth::user();
                // Check if the current password matches
                if (!Hash::check($request->currentPass, $user->password)) {
                  return back()->withErrors(['currentPass' => 'The provided password does not match our records.']);

                }
                // Update the password
                $user->password = Hash::make($request->newPass);
                $user->save();
                actLogs('settings','password update',$request->all());
                return redirect()->back()->with('success','Password updated successfully!');
             }

             return redirect()->back()->withErrors($validator)->withInput();
            
     }
     /* function for change password ends*/
    
     /*functions for optional modules start*/

        public function optionalModules(Request $request){
            $data['optionalModulesList'] = DB::table('lms_optional_modules')->orderBy('id', 'desc')->get();
            $data['page_info'] = pageInfo('Optional Modules List',$request->segment(1));
            return view('settings.optionalModulesList')->with($data); 
        }

        public function optionalModulesStatusUpdate(Request $request){ 
            $query =DB::table('lms_optional_modules')->where('id',$request->id)->update(['status'=>$request->status,'updatedBy'=>getUserID(),'updatedOn'=>dt()]); 
            if($query){
                actLogs('settings','optional modules status update',$request->all());
                return response()->json(['response'=>'success','message'=>'Optional Modules status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Optional Modules status updation failed']);
            }
        }

        
    /*functions for optional modules  ends*/


      /*functions for draggable menu start*/
public function draggableMenu(Request $request) {
    // Fetch all parent modules, ordered by 'position' in ascending order
    $parentModules = DB::table('lms_modules')
        ->where('status', 1)
        ->whereNull('modulesParentId')  // Only fetch parent modules (where parent ID is NULL)
        ->orderBy('position', 'asc')  // Order by 'position' in ascending order for parent menus
        ->get();

    // Fetch all sub-modules, ordered by 'position' in ascending order
    $subModules = DB::table('lms_modules')
        ->where('status', 1)
        ->whereNotNull('modulesParentId')  // Only fetch submenus (where parent ID is NOT NULL)
        ->orderBy('position', 'asc')  // Order by 'position' in ascending order for submenus
        ->get();

    // Initialize the array to store the combined parent and sub-menu data
    $combinedMenus = [];

    // Iterate through parent modules
    foreach ($parentModules as $parentModule) {
        // Add the parent module to the combinedMenus array
        $combinedMenus[] = [
            'id' => $parentModule->modulesId,
            'name' => $parentModule->modulesName,
            'position' => $parentModule->position, // Add position to track its order
            'subMenus' => []  // Initialize an empty array for submenus
        ];
    }

    // Iterate through sub-modules and associate them with their parent
    foreach ($subModules as $subModule) {
        // Find the parent index by matching 'modulesParentId'
        $parentIndex = array_search($subModule->modulesParentId, array_column($combinedMenus, 'id'));

        // If the parent exists, add the sub-menu to the parent’s subMenus array
        if ($parentIndex !== false) {
            $combinedMenus[$parentIndex]['subMenus'][] = [
                'id' => $subModule->modulesId,
                'name' => $subModule->modulesName,
                'position' => $subModule->position, // Add position to submenus as well
            ];
        }
    }

    // Prepare the data for the view
    $data['combinedMenus'] = $combinedMenus;
    $data['page_info'] = pageInfo('Draggable Menu', $request->segment(1));

    return view('settings.menuDragg')->with($data);
}


 
      /*functions for draggable menu start*/
  public function updateMenuOrder(Request $request) {
    $menuOrder = $request->input('menuOrder');
    foreach ($menuOrder as $index => $menuId) {
        DB::table('lms_modules')
            ->where('modulesId', $menuId)
            ->update(['position' => $index]);
    }
    return response()->json(['status' => 'success']);
}

public function updateSubMenuOrder(Request $request) {
    $subMenuOrder = $request->input('subMenuOrder');
    foreach ($subMenuOrder as $index => $subMenuId) {
        DB::table('lms_modules')
            ->where('modulesId', $subMenuId)
            ->update(['position' => $index]);
    }
    return response()->json(['status' => 'success']);
}


}
