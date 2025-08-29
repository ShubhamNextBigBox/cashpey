<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;  


class UsersController extends Controller
{

        public function index(Request $request){
            $data['page_info'] = pageInfo('Users Master',$request->segment(1));
            return view('users.usersMenu')->with($data);  
        }


     /*functions for users start*/

        public function addUsers(Request $request){
           /*Custom Helper for getting auto generated employee id*/
            $userIDData = getEmpIDAuto(); 
            /*Custom Helper ends*/
            $branchesData = DB::table('lms_cities')->where('status', '1')->orderBy('addedOn', 'desc')->get();
            $departmentsData = DB::table('lms_departments')->where('status', '1')->orderBy('id', 'desc')->get();
            $users = DB::table('users')->orderBy('id', 'desc')->get();
            $page_info = pageInfo('Add Users', $request->segment(1));
            
            // Use compact to create an array of data
            $data = compact('branchesData', 'departmentsData', 'users','userIDData', 'page_info');
            
         
            // Pass the data to the view
            return view('users.addUsers', $data);

        }

        public function addPersonalDetails(Request $request){
            $validator =  Validator::make($request->all(),[
                'userID' =>'required',
                'fullName' =>'required',
                'mobile' =>'required|numeric',
                'email' =>'required|email',
                'dateOfBirth' =>'required',
                'gender' =>'required',
                'bloodGroup' =>'required',
                'address' =>'required',
            ]);
             if($validator->passes()){
                  $data = $request->all();
                if ($request->hasFile('profile')) {

                        $file = $request->file('profile');
                        // Generate a unique name for the file
                        $fileName = $file->getClientOriginalName();
                        // Store the file in the 'public/uploads' directory
                        $filePath = $file->storeAs('users', $fileName, 'public');
 
                }else{
                    $filePath = $request->oldProfile;
                }

                 if (array_key_exists('profile', $data)) {
                    unset($data['profile']);
                }
                 $data['profile'] = $filePath;

                 Session::put('personalDetails',$data); 
                 return response()->json(['response'=>'success','action'=>'professionalDetails']);
            }
            else{
                  return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
            
        }

        public function addOfficialDetails(Request $request){
            $validator =  Validator::make($request->all(),[
                'officialMobile' =>'required|numeric|min:10',
                'officialEmail' =>'required|email',
                'dateOfJoining' =>'required',
                'branch' =>'required',
                'department' =>'required',
                'designation' =>'required',
            ]);
             if($validator->passes()){
                 Session::put('officialDetails',$request->all()); 
                 return response()->json(['response'=>'success','action'=>'kycDetails']);
            }
            else{
                  return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
        }

        public function addKycDetails(Request $request){
          
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                // Generate a unique name for the file
                $fileName = $file->getClientOriginalName();
                $filePath = $file->storeAs('users', $fileName, 'public');
                Session::put('kycDetails', $filePath);
                // Optionally, you can store the file path in the session or database  
            }

            return response()->json(['response' => 'success', 'action' => 'submitDetails']);
            
        }

        public function getDesignations($department)
        {
            $department = DB::table('lms_designations')->where('designationsParentId', $department)->pluck('designationsName', 'designationsId');
            return response()->json($department);
        }

        public function SaveUserDetails(){
             
             $personalDetails = Session::get('personalDetails');
             $officialDetails = Session::get('officialDetails');
             $kycDetails = Session::get('kycDetails');

             $arrayData = [];
                if ($personalDetails) {
                    foreach ($personalDetails as $key => $value) {
                        $arrayData[$key] = $value;
                    }
                } 
                if ($officialDetails) {
                    foreach ($officialDetails as $key => $value) {
                        $arrayData[$key] = $value;
                    }
                } 
                if (!empty($kycDetails)) {
                    $arrayData['kycDocument'] = $kycDetails;
                    $arrayData['kycVerifiedStatus'] = 'Pending';
                }else{
                    // $arrayData['kycDocument'] = null;
                    $arrayData['kycVerifiedStatus'] = 'Document Not Received';
                }

                $arrayData['dateOfBirth'] = date('Y-m-d', strtotime($arrayData['dateOfBirth']));
                $arrayData['dateOfJoining'] = date('Y-m-d', strtotime($arrayData['dateOfJoining']));
                $arrayData['branch'] = implode(',',$arrayData['branch']);
                
                if($arrayData['rolesProvided']=='1'){

                }else{
                   $arrayData['rolesProvided'] = '0';
                   $arrayData['status'] = '0'; 
                }
                
                $arrayData['addedBy'] =  Session::get('userID');
                $arrayData['addedOn'] =  dt();
                unset($arrayData['_token']);
                unset($arrayData['oldProfile']);
                $userIDUpdate = $arrayData['userID'];
                
                
                
 
                // query to check existing data//
                $checkExists = DB::table('lms_users_details')->where('userID',$arrayData['userID'])->orderBy('id','desc')->first();
                if($checkExists){
                    unset($arrayData['userID']);
                    unset($arrayData['addedOn']);
                    $arrayData['updatedOn'] =  dt();
                    actLogs('Users','user update',$arrayData);
                    
                    DB::table('users')->where('userID',$userIDUpdate)->update(['displayName'=>$arrayData['fullName']]); 
                    $query = DB::table('lms_users_details')->where('userID',$userIDUpdate)->update($arrayData); 
                }else{
                    actLogs('Users','user added',$arrayData);
                    $query = DB::table('lms_users_details')->insert($arrayData);
                }
               
                if($query){
                    Session::forget('personalDetails');
                    Session::forget('officialDetails');
                    Session::forget('kycDetails');
                    return response()->json(['response'=>'success','message'=>'User updated successfully']);
                 }else{
                    return response()->json(['response'=>'error','message'=>'User updated failed']);
                 }
  
        }        


        public function usersList(Request $request){
            $data['usersData'] =  DB::table('lms_users_details')->orderBy('id','desc')->get();
            $data['page_info'] = pageInfo('Users List',$request->segment(1));
            return view('users.usersList')->with($data); 
        }

        public function usersStatusUpdate(Request $request){ 
            $query =DB::table('lms_users_details')->where('userID',$request->userID)->update(['status'=>$request->status]); 
            $query =DB::table('users')->where('userID',$request->userID)->update(['status'=>$request->status]); 
            if($query){
                actLogs('Users','user status update',$request->all());
                return response()->json(['response'=>'success','message'=>'Users status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Users status updation failed']);
            }
        }

        public function updateUsers(Request $request, $userID){
             $userID = base64_decode($userID);
             $data['userIDData'] = $userID;
             $data['users'] = DB::table('users')->where('role', '!=', 'Super Admin')->orderBy('id', 'desc')->get();
             $data['branchesData'] = DB::table('lms_cities')->where('status', '1')->orderBy('addedOn', 'desc')->get();
             $data['departmentsData'] = DB::table('lms_departments')->where('status','1')->orderBy('id', 'desc')->get();
             $data['designationsData'] = DB::table('lms_designations')->where('status','1')->orderBy('id', 'desc')->get();
             $data['usersData'] = DB::table('lms_users_details')->where('userID', $userID)->first();
             $data['page_info'] = pageInfo('Update User',$request->segment(1));
             return view('users.addUsers')->with($data); 

        }

        public function usersDelete(Request $request)
        {
            $query =  DB::table('users')->where('userID',$request->userID)->delete();
            $query2 =  DB::table('lms_users_details')->where('userID',$request->userID)->delete();
            $query3 =  DB::table('lms_users_roles')->where('userID',$request->userID)->delete();
            if($query2){
                actLogs('Users','user delete',$request->all());
                return response()->json(['response'=>'success','message'=>'Users deleted successfully']);
            }else{
                return response()->json(['response'=>'failed','message'=>'No data found']);
            }
        }
    /*functions for users end*/

    /*functions for users roles start*/

        public function usersRoles(Request $request, $userID){
            $userID = base64_decode($userID);
            $data['userIDData'] = $userID;
            $data['usersData'] = DB::table('lms_users_details')->where('userID', $userID)->first();
            $data['designationData'] = DB::table('lms_designations')->where('status','1')->orderBy('id', 'desc')->get();
            $data['modulesData'] = DB::table('lms_modules')->where(['status'=>'1','modulesParentId'=>null])->orderBy('id', 'desc')->get();
            $data['page_info'] = pageInfo('Add Users Roles',$request->segment(1));
            $data['users'] = DB::table('users')->where('status',1)->orderBy('id', 'desc')->get();
            $data['usersRolesData'] = DB::table('lms_users_roles')
                                    ->join('lms_users_details', 'lms_users_roles.userID', '=', 'lms_users_details.userID')
                                    ->join('users', 'lms_users_roles.userID', '=', 'users.userID')
                                    ->where('users.userID', $userID)
                                    ->select('lms_users_roles.moduleId', 'lms_users_roles.add', 'lms_users_roles.view', 'lms_users_roles.edit', 'lms_users_roles.delete', 'lms_users_roles.export', 'users.userName', 'users.role','lms_users_details.modules','lms_users_details.rolesProvided')
                                    ->get();
            return view('users.addUsersRoles')->with($data); 
        }

        public function fetchModules(Request $request){
                    // Get the ID of the selected module
                    $moduleId = $request->input('modules');

                    // Query the database to get the name of the selected module
                    $moduleName = DB::table('lms_modules')
                                    ->where('modulesId', $moduleId)
                                    ->value('modulesName','modulesId');

                    // Query the database for sub-modules related to the provided module ID
                    $subModules = DB::table('lms_modules')
                                    ->select('modulesId', 'modulesName')
                                    ->where('modulesParentId', $moduleId)
                                    ->orderBy('id', 'desc')
                                    ->get();

                    // Build HTML for the selected module and its sub-modules
                    $html = '';
                    foreach ($subModules as $subModule) {
                        $html .= '<tr data-module-id="' . $moduleId . '">
                                    <td class="d-flex">
                                        <div class="form-check">
                                            <strong style="margin-left:5px;">' . $subModule->modulesName . '</strong>
                                            <input type="checkbox" class="form-check-input modules-checkbox" name="module_id[]" value="' . $subModule->modulesId . '">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input add-checkbox" name="add_' . $subModule->modulesId . '">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input view-checkbox" name="view_' . $subModule->modulesId . '">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input edit-checkbox" name="edit_' . $subModule->modulesId . '">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input delete-checkbox" name="delete_' . $subModule->modulesId . '">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input export-checkbox" name="export_' . $subModule->modulesId . '">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </td>
                                </tr>';
                    }

            // Return the HTML response
            return response()->json(['html' => $html]);
        }

        public function fetchModulesEditData(Request $request) {
            $selectedModuleIds = $request->input('modules');
            $html = ''; // Initialize $html variable

            // Loop through each selected module
            foreach ($selectedModuleIds as $moduleId) {
                // Query the database to get the name of the selected module
                $moduleName = DB::table('lms_modules')
                                ->where('modulesId', $moduleId)
                                ->value('modulesName');

                // Query the database for sub-modules related to the provided module ID
                $subModules = DB::table('lms_modules')
                                ->select('modulesId', 'modulesName')
                                ->where('modulesParentId', $moduleId)
                                ->orderBy('id', 'desc')
                                ->get();

                // Build HTML for the selected module
                $html .= '<tr data-module-id="' . $moduleId . '">
                            <th colspan="6" class="text-center">' . $moduleName . '</th>
                        </tr>';

                // Loop through sub-modules
                foreach ($subModules as $subModule) {
                    // Query the database for permissions for this submodule
                    $permissions = DB::table('lms_users_roles')
                                    ->where('userID', $request->input('userID'))
                                    ->where('moduleId', $subModule->modulesId)
                                    ->first();

                    // Determine checkbox status based on permissions
                    $moduleChecked = $permissions && $permissions->moduleId == $subModule->modulesId ? 'checked' : '';
                    $addChecked = $permissions && $permissions->add == 1 ? 'checked' : '';
                    $viewChecked = $permissions && $permissions->view == 1 ? 'checked' : '';
                    $editChecked = $permissions && $permissions->edit == 1 ? 'checked' : '';
                    $deleteChecked = $permissions && $permissions->delete == 1 ? 'checked' : '';
                    $exportChecked = $permissions && $permissions->export == 1 ? 'checked' : '';

                    // Build HTML for the sub-module
                    $html .= '<tr data-module-id="' . $moduleId . '">
                                <td class="d-flex">
                                    <div class="form-check">
                                        <strong style="margin-left:5px;">' . $subModule->modulesName . '</strong>
                                        <input type="checkbox" class="form-check-input modules-checkbox" name="module_id[]" value="' . $subModule->modulesId . '" ' . $moduleChecked . '>
                                        <label class="form-check-label"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input add-checkbox" name="add_' . $subModule->modulesId . '" ' . $addChecked . '>
                                        <label class="form-check-label"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input view-checkbox" name="view_' . $subModule->modulesId . '" ' . $viewChecked . '>
                                        <label class="form-check-label"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input edit-checkbox" name="edit_' . $subModule->modulesId . '" ' . $editChecked . '>
                                        <label class="form-check-label"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input delete-checkbox" name="delete_' . $subModule->modulesId . '" ' . $deleteChecked . '>
                                        <label class="form-check-label"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input export-checkbox" name="export_' . $subModule->modulesId . '" ' . $exportChecked . '>
                                        <label class="form-check-label"></label>
                                    </div>
                                </td>
                            </tr>';
                }
            }

            // Return the HTML response
            return response()->json(['html' => $html]);
        }
           public function addUsersRoles(Request $request){


                    if($request->input('rolesProvided')=='1'){
                        $rules = [
                            'userID' => 'required|string|max:255',
                            'fullName' => 'required|string|max:255',
                            'department' => 'required|string|max:255',
                            'designation' => 'required|string|max:255',
                            'username' => 'required|string|max:255',
                            'roles' => 'required',
                            'modules' => 'required',
                            'reportingManagers' => 'required',
                            // Add validation rules for other fields as needed
                        ];
                    }else{
                        // Define validation rules
                        $rules = [
                            'userID' => 'required|string|max:255',
                            'fullName' => 'required|string|max:255',
                            'department' => 'required|string|max:255',
                            'designation' => 'required|string|max:255',
                            'username' => 'required|string|max:255|unique:users',
                            'password' => 'required|min:6',
                            'roles' => 'required',
                            'modules' => 'required',
                            'reportingManagers' => 'required',
                            // Add validation rules for other fields as needed
                        ];
                    }
                    // Define custom error messages
                    $messages = [
                        'username.unique' => 'The username has already been taken.',
                        'roles.required' => 'Please select at least one role.',
                        'roles.*.exists' => 'Invalid role selected.',
                        'modules.required' => 'Please select at least one module.',
                        // Add custom error messages for other fields as needed
                    ];

                 

            // Create validator instance
            $validator = Validator::make($request->all(), $rules, $messages);

            if($validator->passes()){
                $moduleIds = $request->input('module_id');
                $addPermissions = $request->except('userID', 'fullName', 'department', 'designation', 'username', 'password', 'roles', 'modules', 'rolesProvided', '_token');
                $data = [];
                 DB::table('lms_users_roles')->where(['userID'=>$request->input('userID')])->delete();
                // Iterate over the received data and insert into the database
                foreach ($moduleIds as $moduleId) {
                    $permissionData = [
                        'userID' => $request->input('userID'),
                        'moduleId' => $moduleId,
                        'add' => isset($addPermissions['add_' . $moduleId]) ? 1 : 0,
                        'view' => isset($addPermissions['view_' . $moduleId]) ? 1 : 0,
                        'edit' => isset($addPermissions['edit_' . $moduleId]) ? 1 : 0,
                        'delete' => isset($addPermissions['delete_' . $moduleId]) ? 1 : 0,
                        'export' => isset($addPermissions['export_' . $moduleId]) ? 1 : 0,
                        'addedOn' => date('Y-m-d h:i:s'),
                    ];

                     actLogs('Users','user roles added',$permissionData);
                     DB::table('lms_users_roles')->insert($permissionData);
                    //array_push($data,$permissionData);
                } 

                $userCreate = $request->input('createUserCheck') ?? null;
                DB::table('lms_users_details')->where('userID',$request->input('userID'))->update(['rolesProvided'=>'1','status'=>1,'createUserCheck'=>$userCreate,'modules'=>implode(',',$request->input('modules')),'reportingManagers'=>implode(',',$request->input('reportingManagers'))]);

                $usersData['userID'] = $request->input('userID');
                $usersData['displayName'] = $request->input('fullName');
                $usersData['userName'] = $request->input('username');
                if(!empty($request->input('password'))){
                    $usersData['password'] = Hash::make($request->input('password'));
                }
                $usersData['role'] = getUserNameById('lms_designations','designationsId',$request->input('roles'),'designationsName') ;
                $usersData['addedBy'] = Session::get('userID');
                           
                $checkUserExists =  DB::table('users')->where('userID',$request->input('userID'))->first();
                if($checkUserExists){
                    $usersData['updated_at'] = dt();
                    actLogs('Users','user roles update',$usersData);
                    DB::table('users')->where('userID',$request->input('userID'))->update($usersData);
                }else{
                    $usersData['created_at'] = dt();
                    $usersData['status'] = 1;
                    actLogs('Users','user roles insert',$usersData);
                    DB::table('users')->where('userID',$request->input('userID'))->insert($usersData);
                }

                return response()->json(['response'=>'success','message' => 'Users roles added successfully']);  
            }else{
                return response()->json(['response'=>'failed','errors' => $validator->errors()]);  
                 
            }
        }
     

     public function usersViewDetails(Request $request){
        $userID = $request->input('userID');
   
        $usersData = DB::table('users')
                     ->where(['users.status'=>1,'users.userID'=>$userID])
                     ->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
                     ->select('lms_users_details.*','users.username')
                     ->orderBy('id','desc')
                     ->first();

        $usersRoles = DB::table('lms_users_roles')
                    ->where('userID',$userID)
                    ->get();        

        $output = '<div class="row">
                        <div class="col-xl-4 col-lg-5">
                            <div class="card text-center">
                                <div class="card-body">';
                                
                                    $imageSrc = $usersData->profile ? Storage::url($usersData->profile) : "public/users/avatar-nouser.jpg";
                                    $output .= '<img src="' . $imageSrc . '" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">';
                                    $output .= '<h4 class="mb-0 mt-2">'.$usersData->fullName.' ('.$usersData->userID.')</h4>
                                    <p class="text-muted mt-1 font-14">'.$usersData->gender.'</p>';
                                    if($usersData->status=='1'):
                                    $output.= '<button type="button" class="btn btn-outline-success btn-sm mb-2">Active</button>';
                                    else:
                                    $output.= '<button type="button" class="btn btn-outline-danger btn-sm mb-2">Deactive</button>';
                                    endif;

                                    $output.= '<div class="text-start mt-3">
                                        <h4 class="font-13 text-uppercase">Personal Details :</h4>
                                        <p class="text-muted mb-2 font-13"><strong>Full Name :</strong> <span class="ms-2">'.$usersData->fullName.'</span></p>

                                        <p class="text-muted mb-2 font-13"><strong>Mobile :</strong><span class="ms-2">'.$usersData->mobile.'</span></p>

                                        <p class="text-muted mb-2 font-13"><strong>Email :</strong> <span class="ms-2 ">'.$usersData->email.'</span></p>

                                        <p class="text-muted mb-1 font-13"><strong>Gender :</strong> <span class="ms-2">'.$usersData->gender.'</span></p>
                                        <p class="text-muted mb-1 font-13"><strong>Date Of Birth :</strong> <span class="ms-2">'.$usersData->dateOfBirth.'</span></p>
                                    </div>
                                </div> 
                            </div>  
                            
                        </div>  

                        <div class="col-xl-8 col-lg-7">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                        <li class="nav-item">
                                            <a href="#aboutme" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0 active">
                                                Official Details
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#timeline" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0 ">
                                                 KYC Details
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane  show active" id="aboutme">
                                      
                                            <h4 class="font-13 text-uppercase">Official Details :</h4>
                                           <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">Email:</th>
                                                            <td>'.$usersData->officialEmail.'</td>
                                                            <th scope="row">Mobile:</th>
                                                            <td>'.$usersData->officialMobile.'</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Joining Date:</th>
                                                            <td>'.df($usersData->dateOfJoining).'</td>
                                                            <th scope="row">Branch:</th>
                                                            <td>'.getUserNameById('lms_cities','cityID',$usersData->branch,'cityName').'</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Department:</th>
                                                            <td>'.getUserNameById('lms_departments','departmentsId',$usersData->department,'departmentsName').'</td>
                                                            <th scope="row">Designation:</th>
                                                            <td>'.getUserNameById('lms_designations','designationsId',$usersData->designation,'designationsName').'</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Reporting Manager:</th>
                                                            <td>'.$usersData->reportingManagers.'</td>
                                                            <th scope="row"></th>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>


                                        </div>
                                        <div class="tab-pane" id="timeline">
 
                                             
                                            <div class="border border-light rounded p-2 mb-3">
                                                <div class="d-flex row">';
                                                  if(!empty($usersData->kycDocument)){
                                  $output.=' 
                                            <div class="col-md-6">
                                                <a target="_blank" href="'.Storage::url($usersData->kycDocument).'"><i class="mdi mdi-eye btn btn-primary"></i></a>
                                                </div>
                                                 <div class="col-md-6">
                                                <a target="_blank" href="'.Storage::url($usersData->kycDocument).'" download><i class="mdi mdi-download btn btn-primary"></i></a>
                                           </div>
                                          ';
                                  }
                                            $output.='</div>
                                        </div>
                                    </div>  
                                </div>  
                            </div>  
                        </div>  
                    </div>';
       return response()->json(['response'=>'success','data'=>$output]);  
     }
    /*functions for users roles end*/
 
     public function fetchReportingManagers(Request $request){
        $departmentID = $request->departmentID;
        $reportingManagers = DB::table('users')
            ->select('users.userID', 'users.displayName', 'lms_users_details.department')
            ->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
            ->where('lms_users_details.department', $departmentID)
            ->where('users.userID','!=')
            ->get();
        return response()->json($reportingManagers);
     }
}
