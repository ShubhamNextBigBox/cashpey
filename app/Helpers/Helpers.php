<?php
use Illuminate\Support\Facades\Log;  


/*  Helper for Print Array Data*/
if (!function_exists('p')) {
    function p($data)
    {
         echo "<pre>";
         print_r($data);
         echo "</pre>";
         die;
    }
}
/*  Helper Ends*/

/*  Helper for current date time */
if (!function_exists('dt')) {
    function dt()
    {
        return date('Y-m-d H:i:s');
    }
}
/*  Helper Ends*/


/*  Helper for current date time */
if (! function_exists('slugToText')) {
    function slugToText($slug)
    {
        $text = str_replace('-', ' ', $slug);
        return ucwords($text);
    }
}
/*  Helper Ends*/

/*  Helper for number format */
if (!function_exists('nf')) {
    function nf($data)
    {
        return $data;
    }
}
/*  Helper Ends*/


/*  Helper to get current user logged in userID starts*/
if (!function_exists('getUserID')) {
    function getUserID()
    {
        return Session::get('userID');
    }
}
/*  Helper Ends*/


/*  Helper for badge status color*/
if (!function_exists('getStatusClass')) {
    function getStatusClass($status) {
        switch($status) {
            case 'Disbursed':
                return 'badge-success'; // Bootstrap class for a successful status
            case 'Part Payment':
                return 'badge-warning'; // Bootstrap class for a warning status
            case 'Closed':
                return 'badge-secondary'; // Bootstrap class for a secondary status
            case 'Settlement':
                return 'badge-info'; // Bootstrap class for informational status
            case 'PAYDAY PRECLOSE':
                return 'badge-danger'; // Bootstrap class for a critical status
            default:
                return 'badge-outline-primary'; // Default badge color
        }
    }
}
/*  Helper ends */
 
/*  Helper for random number generate */
function randomNo($min, $max) {
    // Digits from 0 to 9
    $digits = range(0, 9);
    
    if (count($digits) < 6) {
        throw new Exception('Not enough digits to generate a unique number');
    }

    // Initialize an empty string for the final result
    $uniqueDigits = '';

    // Loop until we have 6 unique digits
    while (strlen($uniqueDigits) < 6) {
        // Randomly pick a digit
        $randomDigit = $digits[array_rand($digits)];

        // Check if the digit is already in the string, if not, add it
        if (strpos($uniqueDigits, (string)$randomDigit) === false) {
            $uniqueDigits .= $randomDigit;
            // Remove the used digit from the array
            $digits = array_diff($digits, [$randomDigit]);
        }
    }

    return $uniqueDigits;
}
/* Helper ends */

 



/* Helper for time ago show */
if (!function_exists('timeAgo')) {
    function timeAgo($timestamp) {
        // Parse the timestamp using DateTime
        try {
            $date = new DateTime($timestamp);
        } catch (Exception $e) {
            return 'Invalid date';
        }

        $now = new DateTime();
        $interval = $now->diff($date);

        if ($interval->y > 0) {
            return $interval->y . ' years ago';
        } else if ($interval->m > 0) {
            return $interval->m . ' months ago';
        } else if ($interval->d > 0) {
            return $interval->d . ' days ago';
        } else if ($interval->h > 0) {
            return $interval->h . ' hours ago';
        } else if ($interval->i > 0) {
            return $interval->i . ' minutes ago';
        } else {
            return 'Just now';
        }
    }
}
/*  Helper Ends*/




/* Helper for optional module status */
if (!function_exists('optionalModules')) {
    function optionalModules($moduleName) {
            
     $query =  DB::table('lms_optional_modules')->where(['module'=>$moduleName,'status'=>1])->count();     
       if($query){
        return true;
       }else{
        return false;
       } 

    }
}
/*  Helper Ends*/


/* Helper for Notifications Support Tickets*/
    if(!function_exists('getTicketNotification')) {
        function getTicketNotification() { ?>
         <div class="px-2" style="max-height: 300px;" data-simplebar>
            <h5 class="text-muted font-13 fw-normal mt-2">Today</h5>
            <!-- item-->

           <?php
             $status  = ['Open','Pending','Hold'];    
             $notiData = DB::table('lms_tickets')->whereIn('status', $status)->orderBy('id','desc')->get(); 
            if(count($notiData)> 0): 
                foreach($notiData as $arr): ?>
                <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="notify-icon bg-primary">
                                    <i class="mdi mdi-comment-account-outline"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 text-truncate ms-2">
                                <h5 class="noti-item-title fw-semibold font-14"><?=getUserNameById('users','userID', $arr->generatedBy, 'displayName')?><small class="fw-normal text-muted ms-1"><?=timeAgo($arr->addedOn)?></small></h5>
                                <small class="noti-item-subtitle text-muted"><?=$arr->subject?></small>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; 
                else: ?>
                   <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 text-truncate ms-2">
                                <small class="noti-item-subtitle text-muted">No tickets found</small>
                            </div>
                        </div>
                    </div>
                </a> 
        <?php endif;?>
          
        </div>
   <?php     }
    }
 
/* Helper ends */


/* Helper for Notifications */
    if(!function_exists('getNotification')) {
        function getNotification() { ?>
         <div class="px-2" style="max-height: 300px;" data-simplebar>
            <h5 class="text-muted font-13 fw-normal mt-2">Today</h5>
            <!-- item-->

           <?php  $todayNotiData = DB::table('lms_activity_logs')->whereDate('performedOn',date('Y-m-d'))->orderBy('id','desc')->get(); 
            if(count($todayNotiData)> 0): 
                foreach($todayNotiData as $arr): ?>
                <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="notify-icon bg-primary">
                                    <i class="mdi mdi-comment-account-outline"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 text-truncate ms-2">
                                <h5 class="noti-item-title fw-semibold font-14"><?=getUserNameById('users','userID', $arr->userID, 'displayName')?><small class="fw-normal text-muted ms-1"><?=timeAgo($arr->performedOn)?></small></h5>
                                <small class="noti-item-subtitle text-muted"><?=$arr->description?></small>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; 
                else: ?>
                   <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 text-truncate ms-2">
                                <small class="noti-item-subtitle text-muted">No activity found</small>
                            </div>
                        </div>
                    </div>
                </a> 
        <?php endif;?>
            
            <!-- item-->
            <?php  $yesterdayNotiData = DB::table('lms_activity_logs')->whereDate('performedOn',date('Y-m-d', strtotime('-1 day')))->orderBy('id','desc')->get(); 
                if(count($yesterdayNotiData)> 0): ?>
                    <h5 class="text-muted font-13 fw-normal mt-0">Yesterday</h5>
                   <?php foreach($yesterdayNotiData as $arr): ?>
                    <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-2">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="notify-icon bg-primary">
                                        <i class="mdi mdi-comment-account-outline"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 text-truncate ms-2">
                                    <h5 class="noti-item-title fw-semibold font-14"><?=getUserNameById('users','userID', $arr->userID, 'displayName')?><small class="fw-normal text-muted ms-1"><?=timeAgo($arr->performedOn)?></small></h5>
                                    <small class="noti-item-subtitle text-muted"><?=$arr->description?></small>
                                </div>
                            </div>
                        </div>
                    </a>
            <?php endforeach; endif; ?>
        </div>
   <?php     }
    }
 
/* Helper ends */


/*  Helper for Activity logs*/
if (!function_exists('approvalMatrix')) {
    function approvalMatrix()
    {
        $userID = Session::get('userID');
        $data = DB::table('lms_approval_matrix')->where('status',1)->whereJsonContains('users', $userID)->first();
        return $data;
    }
}
/*  Helper Ends*/



/*  Helper for Activity logs*/
if (!function_exists('actLogs')) {
    function actLogs($module,$description,$log)
    {

        $username = getUserNameById('users','userID',Session::get('userID'),'displayName');
        $data = [
            'userID' => Session::get('userID'),
            'username' => $username,
            'module' => ucwords($module),
            'description' => ucwords($description),
            'log' => json_encode($log),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'performedOn' => dt(),
            'addedOn' => date('Y-m-d'),
        ];

        DB::table('lms_activity_logs')->insert($data);

        // Log txt file create
        $logMessage = sprintf(
            "[%s] Module: %s, Description: %s, User ID: %s, Username: %s, IP: %s, Log: %s\n",
            date('Y-m-d H:i:s'),
            $module,
            $description,
            $data['userID'],
            $data['username'],
            $data['ip'],
            $data['log']
        );

        // Path to the log file
        $logFilePath = storage_path('logs/activity_log.txt');

        // Write to the log file, appending the new log entry
        file_put_contents($logFilePath, $logMessage, FILE_APPEND);


       // Log to a log file create
       Log::channel('activity')->info('Activity Log:', [
            'module' => $module,
            'description' => $description,
            'log' => $log,
            'userID' => Session::get('userID'),
            'username' => $username,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'performedOn' => dt(),
            'addedOn' => date('Y-m-d'),
        ]);
    }
}
/*  Helper Ends*/


/*  Helper for BreadCrumbs and Page Title*/
if (!function_exists('pageInfo')) {
    function pageInfo($page_name,$page_title)
    {
        $data=['page_name'=>ucwords($page_name),'page_title'=>ucwords($page_title)];
        return $data;
    }
}
/*  Helper Ends*/

/*  Helper for get Name By UserId*/
if (!function_exists('getUserNameById')) {
   function getUserNameById($table,$column,$idToGet,$value)
    {
        $data= DB::table($table)->where($column,$idToGet)->first();
        if($data){
          return ucwords($data->$value);
        }else{
          return '--';
        }
    }   

}
/*  Helper Ends*/


/*  Helper for get Profile Pic By UserId*/
if (!function_exists('profilePic')) {
   function profilePic()
    {
      $data =  DB::table('lms_users_details')->select('profile')->where('userID', Session::get('userID'))->first();
      if($data){
        return $data->profile;
      }else{
        return 'users/avatar-nouser.jpg';
      }
    }   

}
/*  Helper Ends*/



/*  Helper for get employee id*/

if (!function_exists('getEmpIDAuto')) {
    function getEmpIDAuto()
    {
        $data = DB::table('lms_company_informations')->select('empIdPrefixes')->orderBy('id', 'desc')->limit(1)->first();

        if (!empty($data)) {
            $latestEmpId = DB::table('lms_users_details')
                ->select('userID')
                ->orderBy('id', 'desc')
                ->first();

            if (!empty($latestEmpId)) {
                $empId = $latestEmpId->userID;

                // Extract the string part (prefix) using regular expression
                preg_match('/^[A-Za-z]+/', $empId, $matches);
                $stringPart = $matches[0];

                // Calculate the numeric part and its length
                $stringPartCount = strlen($stringPart);
                $numericPart = (int)substr($latestEmpId->userID, $stringPartCount); // Extract numeric part
                $nextNumericPart = $numericPart + 1;
                $nextEmpId = $data->empIdPrefixes.$nextNumericPart;
                return $nextEmpId;
                // Determine the number of digits in the current numeric part
//                 $currentNumericLength = strlen((string)$numericPart);
//                 $newNumericPart = str_pad($nextNumericPart, $currentNumericLength, '0', STR_PAD_LEFT); // Pad with zeros

//                 // Generate the next employee ID
// echo                $nextEmpId = $stringPart . $newNumericPart; 
                
                
             //   echo $nextEmpId;
                //return ucwords($nextEmpId);
            } else {
                return ucwords($data->empIdPrefixes . '1'); // Start with '1' if no user ID exists
            }
        } else {
            return ucwords('Please Add Employee ID Prefix');
        }
    }
}

    


/*  Helper Ends*/
 
 
 /*  Helper to check role*/

if (!function_exists('role')) {
   function role()
    { 
        return Session::get('role');    
    }   
}

/*  Helper Ends*/

 
/*  Helper to check super admin*/

if (!function_exists('isSuperAdmin')) {
   function isSuperAdmin()
    {    
        $userID = Session::get('userID');
        if (Session::get('role') == 'Super Admin') { 
            return true;
        }else{
            return false;
        }
    }   
}

/*  Helper Ends*/


/*  Helper to check admin*/

if (!function_exists('isAdmin')) {
   function isAdmin()
    {    
        $userID = Session::get('userID');
        if (Session::get('role') == 'Admin') { 
            return true;
        }else{
            return false;
        }
    }   
}

/*  Helper Ends*/



 
// for dynamic menu and submenu
 if (!function_exists('getMenuSubmenuItems')) {
    function getMenuSubmenuItems() {
        $userID = Session::get('userID');
        
        if (Session::get('role') == 'Super Admin') {
            // Fetch all parent modules for Super Admin, ordered by 'modulesId' (ascending)
                $parentModules = DB::table('lms_modules')
                ->select('modulesId', 'modulesName', 'modulesUrl')
                ->whereNull('modulesParentId')
                ->orderBy('position', 'asc')  // Order parent modules by 'modulesId' (ascending)
                ->get();

            foreach ($parentModules as $menu) { ?>
                <li class="side-nav-item">
                    <?php
                    // Fetch child modules for the current parent, ordered by 'position' (ascending)
                    $childModules = DB::table('lms_modules')
                        ->select('modulesId', 'modulesName', 'modulesUrl')
                        ->where('modulesParentId', $menu->modulesId)
                        ->where('status', 1)
                        ->orderBy('position', 'asc')  // Order child modules by 'position' (ascending)
                        ->get();
                    ?>
                    <?php if ($childModules->isNotEmpty()) { ?>
                        <a data-bs-toggle="collapse" href="#<?=$menu->modulesId?>" aria-expanded="false" aria-controls="sidebarEmail" class="side-nav-link">
                            <i class="uil-angle-double-right"></i>
                            <span><?=$menu->modulesName?></span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="<?=$menu->modulesId?>">
                            <ul class="side-nav-second-level">
                                <?php foreach ($childModules as $subMenu) {?>
                                    <li>
                                        <a href="<?=$subMenu->modulesUrl?>"><?=$subMenu->modulesName?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } else { ?>
                        <a href="<?=$menu->modulesUrl?>" class="side-nav-link">
                            <i class="uil-angle-double-right"></i>
                            <span><?=$menu->modulesName?></span>
                        </a>
                    <?php } ?>
                </li>
            <?php } ?>

            <li class="side-nav-title">Master</li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarTasks" aria-expanded="false" aria-controls="sidebarTasks" class="side-nav-link collapsed">
                    <i class="uil uil-clock-nine"></i>
                    <span> Activity Logs </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarTasks" style="">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="activity/logs">All Logs</a>
                        </li>
                        <li>
                            <a href="activity/lead-transfer-logs">Lead Transfer</a>
                        </li>
                    </ul>
                </div>
            </li>
            <a href="settings/master" class="side-nav-link">
                <i class="uil-cog"></i>
                <span>Settings</span>
            </a>
            <a href="users/master" class="side-nav-link">
                <i class="uil-users-alt"></i>
                <span>Users</span>
            </a>
      <?php  } else {
            // Fetch parent modules assigned to the user, ordered by 'modulesId' (ascending)
            $modulesAssignedToUser = DB::table('lms_users_details')
                ->select('createUserCheck','modules')
                ->where('userID', $userID)
                ->first();
 
            $modulesArray = explode(',', $modulesAssignedToUser->modules);

            // Fetch parent modules for the user, ordered by 'modulesId' (ascending)
            $parentModules = DB::table('lms_modules')
                ->select('modulesId', 'modulesName')
                ->whereIn('modulesId', $modulesArray)
                ->whereNull('modulesParentId')
                ->where('status','1')
                ->orderBy('position', 'asc')  // Order parent modules by 'modulesId' (ascending)
                ->get();
         
            foreach ($parentModules as $parentModule) { ?>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#<?=$parentModule->modulesId?>" aria-expanded="false" aria-controls="sidebarEmail" class="side-nav-link">
                        <i class="uil-angle-double-right"></i>
                        <span><?=$parentModule->modulesName?></span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="<?=$parentModule->modulesId?>">
                        <ul class="side-nav-second-level">
                            <?php
                            // Fetch child modules for each parent module assigned to the user, ordered by 'position' (ascending)
                           $childModules = DB::table('lms_modules')
                            ->select('lms_modules.modulesId', 'lms_modules.modulesName', 'lms_modules.modulesUrl')
                            ->join('lms_users_roles', 'lms_modules.modulesId', '=', 'lms_users_roles.moduleId')
                            ->where('lms_users_roles.userID', $userID)
                            ->where('lms_modules.modulesParentId', $parentModule->modulesId)
                            ->where('status','1')
                            ->orderBy('position', 'asc')  // Order child modules by 'position' (ascending)
                            ->get();

                            foreach ($childModules as $subMenu) {?>
                                <li>
                                    <a href="<?=$subMenu->modulesUrl?>"><?=$subMenu->modulesName?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </li><?php } ?>
                
                <?php if(isAdmin()){?>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarUsers" aria-expanded="false" aria-controls="sidebarEmail" class="side-nav-link">
                     <i class="uil-users-alt"></i>
                      <span> Users </span>
                       <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarUsers">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="users/users-list"><i class="uil-angle-double-right"></i> Users List</a>
                            </li>
                        </ul>
                    </div>
                </li> 
                <li class="side-nav-title">Master</li>
                    <a href="settings/master" class="side-nav-link">
                    <i class="uil-cog"></i>
                    <span>Settings</span>
                    </a>
                </li> 
            
                <?php } ?>
            
                <?php if($modulesAssignedToUser->createUserCheck=='1'){ ?>
                    <li class="side-nav-title">Master</li>
                    <a href="settings/master" class="side-nav-link">
                    <i class="uil-cog"></i>
                    <span>Settings</span>
                    </a>
                    <a href="users/master" class="side-nav-link">
                        <i class="uil-users-alt"></i>
                        <span>Users</span>
                    </a>
                </li> 
            
             <?php  } 
       }
        
    }
}

//helper ends


if (!function_exists('rolesAccess')) {

    function rolesAccess($childModuleName, $condition) {
        static $cachedAccess = null; // same request ke liye cache

        if (isSuperAdmin()) {
            return true; // super admin ke liye hamesha allowed
        }

        if ($cachedAccess === null) {
            $userID = getUserID();

            // ek hi query me sab modules ke access nikal lo
            $roles = DB::table('lms_users_roles')
                ->join('lms_modules', 'lms_modules.modulesId', '=', 'lms_users_roles.moduleId')
                ->where('lms_users_roles.userID', $userID)
                ->where('lms_modules.status', '1')
                ->select(
                    'lms_modules.modulesId',
                    'lms_modules.modulesName',
                    'lms_users_roles.add',
                    'lms_users_roles.view',
                    'lms_users_roles.edit',
                    'lms_users_roles.delete',
                    'lms_users_roles.export'
                )
                ->get();

            // easy lookup ke liye associative array bana lo
            $cachedAccess = [];
            foreach ($roles as $role) {
                $cachedAccess[strtolower($role->modulesName)] = [
                    'add'    => (bool) $role->add,
                    'view'   => (bool) $role->view,
                    'edit'   => (bool) $role->edit,
                    'delete' => (bool) $role->delete,
                    'export' => (bool) $role->export,
                ];
            }
        }

        // module name normalize
        $childModuleName = strtolower($childModuleName);
        $condition = strtolower($condition);

        // check access
        return $cachedAccess[$childModuleName][$condition] ?? false;
    }
}

// helper for roles management ends

 


// helper for date format start

if(!function_exists('df')){
    function df($date){
       return date('d-m-Y', strtotime($date));
    }
}

// helper for date ends


// helper for loan no
if (!function_exists('enachNoGenerate')) {
    function enachNoGenerate($leadID, $loanNo) {
        $collectionData = DB::table('lms_collection')
            ->select('enachID')
            ->where(['leadID' => $leadID, 'loanNo' => $loanNo])
            ->orderBy('id', 'desc')
            ->first();

        if ($collectionData && !empty($collectionData->enachID)) {
            $enachIdOld = $collectionData->enachID;
            return $enachNo =   ++$enachIdOld;
        }else{
             // Remove the first 4 characters from loanNo
            $loanNo = substr($loanNo, 4);
            $sNo = '01';
            return $loanNo.$sNo;
        }
    }
}



// helper for loan no
if(!function_exists('loanNoGenerate')){
    function loanNoGenerate($leadID){
             
        $branchCity = DB::table('lms_approval')->select('branch')->where(['leadID'=>$leadID])->orderBy('id', 'desc')->first();
        
        if($branchCity){
        $cityBranchCode = DB::table('lms_cities')
            ->where(['cityID' => $branchCity->branch, 'status' => 1])
            ->first();
        
        // Now fetch the state from lms_states based on the stateID from $cityBranchCode
        $stateBranchCode = DB::table('lms_states')
            ->where('stateID', $cityBranchCode->stateID)
            ->first();
            
        
        $loanNoPrefix = cmp()->loanNoPrefixes;
        $branchCodeState = $stateBranchCode->branchCode;
        $branchCodeCity = $cityBranchCode->branchCode;
        $currentYear = date('y');
        $currentMonth = date('m');
        
        $combinedBranchCode = $branchCodeState.$branchCodeCity;
        
       
        $loanDataCheckForBranchCode = DB::table('lms_approval')
            ->select('branchCode', 'digits')
            ->where(['branchCode' => $combinedBranchCode, 'status' => 'Approved'])
            ->whereYear('createdDate', date('Y')) // Filter by current year
            ->whereMonth('createdDate', date('m')) // Filter by current month
            ->orderBy('id', 'desc')
            ->first();
        
        if($loanDataCheckForBranchCode){
            $oldDigits = $loanDataCheckForBranchCode->digits;
            $incrementdigits = str_pad(++$oldDigits, 4, '0', STR_PAD_LEFT);
            if((int)$incrementdigits > 9999) {
                $incrementdigits = '0001'; // Reset to 0001 if it exceeds 9999
            }
        }else{
          $incrementdigits = '0001';
        }
         $data ['loanNo'] = $loanNoPrefix.$currentYear.$currentMonth.$branchCodeState.$branchCodeCity.$incrementdigits;
         $data ['digits'] = $incrementdigits;
         return $data;
        } else{
            return "00000000";
        }
    }
}
// helper for loan no ends

// helper for branchCode starts
if(!function_exists('branchCodeFetch')){
    function branchCodeFetch($leadID){
             
        $branchCity = DB::table('lms_approval')->select('branch')->where(['leadID'=>$leadID])->orderBy('id', 'desc')->first();
        
        if($branchCity):
        $cityBranchCode = DB::table('lms_cities')
            ->where(['cityID' => $branchCity->branch, 'status' => 1])
            ->first();
        
        // Now fetch the state from lms_states based on the stateID from $cityBranchCode
        $stateBranchCode = DB::table('lms_states')
            ->where('stateID', $cityBranchCode->stateID)
            ->first();
        $branchCodeState = $stateBranchCode->branchCode;
        $branchCodeCity = $cityBranchCode->branchCode;
        return  $branchCodeState.$branchCodeCity;
        else:
            return "0000";
        endif;
    }
}
// helper for branchCode starts ends



// helper for branchCode starts
if(!function_exists('stampDuty')){
    function stampDuty($branch){
        // Query to find the branch's state ID
        $branchState = DB::table('lms_cities')->select('stateID')->where(['cityID' => $branch, 'status' => 1])->orderBy('stateID', 'desc')->first();

        // Check if the branchState was found
        if ($branchState !== null) {
            // Query to find the stamp duty for the state
            $stampDuty = DB::table('lms_states')
                            ->select('stampDuty')
                            ->where(['stateID' => $branchState->stateID, 'status' => 1])
                            ->first();

            // Check if stampDuty was found
            if ($stampDuty !== null) {
                return $stampDuty->stampDuty;
            } else {
                return "100";  // Return 0 if no stamp duty found
            }
        } else {
            return "100";  // Return 0 if no branchState found
        }
    }
}

// helper for branchCode starts ends


// helper for date format with time start

if(!function_exists('dft')){
    function dft($date){
       return date('d-m-Y H:i:s',strtotime($date));
    }
}

// helper for date ends

/*  Helper for attendance button*/
if (!function_exists('attendanceBtn')) {
    function attendanceBtn()
    {
        $checkExists = DB::table('lms_attendance_log')->where(['userID' => Session::get('userID'), 'attendanceDate' => date('Y-m-d')])->orderBy('id', 'desc')->first();

        if (!$checkExists) {
          //  echo '<a href="javascript:void(0)" class="font-24 text-success" data-punchType="punchIN" id="punchIN" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="success-tooltip" data-bs-title="Punch In"><em class="icon ni ni-clock"></em><i class="ri-fingerprint-line font-24"></i></a>';
        } else {
            if ($checkExists->punchType === 'punchOut') {
               // echo '<a href="javascript:void(0)" class="font-24 text-success" data-punchType="punchIN" id="punchIN" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="success-tooltip" data-bs-title="Punch In"><em class="icon ni ni-clock"></em><i class="ri-fingerprint-line font-24"></i></a>';
            } elseif ($checkExists->punchType === 'punchIN') {
                $signInTime = strtotime($checkExists->time);
                $currentTime = time();
                $timeDifference = $currentTime - $signInTime;

                // Pass time difference to JavaScript
           //     echo '<span id="runningTime" class="clock">' . sprintf("%02d:%02d:%02d", floor($timeDifference / 3600), floor(($timeDifference % 3600) / 60), $timeDifference % 60) . '</span>';
          //      echo '<a href="javascript:void(0)" class="font-24 text-danger" data-punchType="punchOut" id="punchOut" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="danger-tooltip" data-bs-title="Punch Out"><em class="icon ni ni-clock"></em> <i class="ri-fingerprint-line font-24"></i></a>';

                // Add JavaScript to update the clock
                // echo '<script>
                //     const runningTimeElement = document.getElementById("runningTime");
                //     let timeDifference = ' . $timeDifference . ';
                    
                //     setInterval(() => {
                //         timeDifference++;
                //         const hours = String(Math.floor(timeDifference / 3600)).padStart(2, "0");
                //         const minutes = String(Math.floor((timeDifference % 3600) / 60)).padStart(2, "0");
                //         const seconds = String(timeDifference % 60).padStart(2, "0");
                //         runningTimeElement.textContent = hours + ":" + minutes + ":" + seconds;
                //     }, 1000);
                // </script>';
            }
        }
    }
}

/*  Helper Ends*/


// helper for convert Number To Words starts
if (!function_exists('convertNumberToWords')) {
    function convertNumberToWords($number) {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ' ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
            100 => 'Hundred',
            1000 => 'Thousand',
            1000000 => 'Million',
            1000000000 => 'Billion',
            1000000000000 => 'Trillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 0) {
            return $negative . convertNumberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = intval($number / 10) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = intval($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . convertNumberToWords($remainder);
                }
                break;
            case $number < 1000000:
                $thousands = intval($number / 1000);
                $remainder = $number % 1000;
                $string = convertNumberToWords($thousands) . ' ' . $dictionary[1000];
                if ($remainder) {
                    $string .= $separator . convertNumberToWords($remainder);
                }
                break;
            case $number < 1000000000:
                $millions = intval($number / 1000000);
                $remainder = $number % 1000000;
                $string = convertNumberToWords($millions) . ' ' . $dictionary[1000000];
                if ($remainder) {
                    $string .= $separator . convertNumberToWords($remainder);
                }
                break;
            case $number < 1000000000000:
                $billions = intval($number / 1000000000);
                $remainder = $number % 1000000000;
                $string = convertNumberToWords($billions) . ' ' . $dictionary[1000000000];
                if ($remainder) {
                    $string .= $separator . convertNumberToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
        
        $string .= ' only';
        return $string;
    }
}
/*  Helper Ends*/


// helper for repaydate extends days

if(!function_exists('cmp')){
    function repayDays($days){
        return $days;
    }
}

// helper for repaydate extends days ends

// helper for curreny show

if(!function_exists('currency')){
    function currency($data){
        return $data;
    }
}

// helper for curreny show
 

// helper for company details

if(!function_exists('cmp')){
    function cmp(){
        return DB::table('lms_company_informations')->limit(1)->first();
    }
}

// helper for company details


// helper for clock time
if(!function_exists('clock')){
    function clock(){
       echo '<div id="MyClockDisplay" class="clock" onload="showTime()"></div>';
    }
}
// helper for clock time details

 

// for get data all branches
if (!function_exists('getAllBranches')) {
    function getAllBranches($data)
    {
         foreach($data as $key => $arr):
            $output ='<tr>
                        <td>'.++$key.'</td>
                        <td>'.$arr->branchName.'</td>
                        <td>'.$arr->addedBy.'</td>
                        <td>'.date('d, M, Y',strtotime($arr->addedOn)).'</td>';
                        if($arr->status=='1'):
                           $output.='<td>
                                <div>
                                    <input type="checkbox" id="switch'.$arr->branchId.'" checked data-switch="success" data-branchId="'.$arr->branchId.'" value="0" class="status-switch">
                                    <label for="switch'.$arr->branchId.'" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                </div>
                            </td>';
                        else:
                        $output.='<td>
                            <div>
                                <input type="checkbox" id="switch'.$arr->branchId.'" data-switch="success" data-branchId="'.$arr->branchId.'" value="1" class="status-switch">
                                <label for="switch'.$arr->branchId.'" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                            </div>
                        </td>';
                        endif;
                        $output.='<td>
                            <a href="" class="text-info branchEditBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="info-tooltip" data-bs-title="Edit Branch" style="font-size: 18px;" data-branchId="'.$arr->branchId.'">
                                <i class="mdi mdi-pencil"></i>
                            </a> 
                            <a href="" class="text-danger branchDeleteBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="danger-tooltip" data-bs-title="Delete Branch" style="font-size: 18px;margin-left: 5px;" data-branchId="'.$arr->branchId.'">
                                <i class="mdi mdi-delete"></i>
                            </a>
                    </td>
                </tr>';
               echo $output;  
        endforeach;
        //return $data;
    }
}

 
if (!function_exists('uniqueIDGenerator')) {
    
    /**
     * Generate a unique ID for a specified table and column
     *
     * @param string $table Name of the table to check uniqueness
     * @param string $column Name of the column to check uniqueness
     * @param int $length Length of the generated unique ID (default is 6)
     * @return string The unique ID
     */
    function uniqueIDGenerator($table, $column, $length = 6) {
        // Digits from 1 to 9 (avoiding 0)
        $digits = range(1, 9);
        
        // Ensure enough digits are available to generate the ID
        if (count($digits) < $length) {
            throw new Exception('Not enough digits to generate a unique ID');
        }

        // Initialize an empty string for the final unique ID
        $uniqueID = '';

        // Loop until we have the required number of unique digits
        while (strlen($uniqueID) < $length) {
            // Randomly pick a digit
            $randomDigit = $digits[array_rand($digits)];

            // Check if the digit is already used in the ID
            if (strpos($uniqueID, (string)$randomDigit) === false) {
                $uniqueID .= $randomDigit;
                // Remove the used digit from the array
                $digits = array_diff($digits, [$randomDigit]);
            }
        }

        // Check if the generated ID exists in the database
        $idExists = DB::table($table)
                      ->where($column, $uniqueID)
                      ->exists();

        // If the ID exists, recursively regenerate it
        if ($idExists) {
            return uniqueIDGenerator($table, $column, $length);
        }

        // Return the unique ID
        return $uniqueID;
    }
}


if (!function_exists('sendMobileNotification')) {
    function sendMobileNotification($mobileNumber, $message, $templateId)
    {
        $url = 'https://msgn.mtalkz.com/api';

        $postData = [
            'apikey' => '94AI4YTvTXb18i3U',
            'senderid' => 'CSHPEY',
            'number' => $mobileNumber,
            'message' => $message,
            'templateid' => $templateId,
            'format' => 'json'
        ];

        try {
            $response = Http::post($url, $postData);
            $result = $response->json();

            if (isset($result['status']) && $result['status'] === 'OK') {
                return [
                    'success' => true,
                    'message' => 'Document Request Message sent successfully.'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send SMS.',
                'data' => $result
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send SMS.',
                'error' => $e->getMessage()
            ];
        }
    }
}

if (!function_exists('getToken')) {

   function getToken()
   {
      $email = 'abidi@cashpey.com';
      $secret_key = 'ReN5MH!WSfvxTX5tFPo4&5q18';

      $response = Http::post('https://app.nextbigbox.co.in/api/verifyToken', [
         'email' => $email,
         'secret_key' => $secret_key,
      ]);

      if ($response->ok()) {
         return $response->json('authorisation.token');
      }

      abort(401, 'Unable to authenticate with the API.');
   }
}

 