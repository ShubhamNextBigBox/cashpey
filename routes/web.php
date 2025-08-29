<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileLoanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\DisbursalController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\RedFlagController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\EsignVideoKycController;
use App\Http\Controllers\ApprovalMatrixController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EnachController;
use App\Http\Controllers\SoaController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\BankStatementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/


Route::middleware(['userAuth'])->get('/link', function () {
    Artisan::call('storage:link');
});



Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    
    return response()->json(['message' => 'Cache and config cleared successfully!']);
});
 
 

 Route::get('/',[AuthController::class,'index'])->middleware('guest');
 Route::match(['get','post'],'login',[AuthController::class,'index'])->middleware('guest');

 Route::get('logout',[AuthController::class,'logout']);

 Route::get('punch',[AuthController::class,'punch']);
 Route::get('custom-404',function(){
     return view('error.404');
 })->name('custom-404');
 
Route::post('punch-in',[AuthController::class,'punchInOut'])->name('punchInOut')->middleware('userAuth');
  

/*Middleware for all routes for user logged in or not*/
Route::middleware(['userAuth','isSuperAdmin'])->group(function () {

/*Group Routes for Settings starts*/
	Route::prefix('settings')->group(function () {

		   Route::get('master',[SettingsController::class,'settings']);
		  
		  /*Routes for modules starts*/
		    Route::get('modules',[SettingsController::class,'modules']);
		    Route::post('modules-add',[SettingsController::class,'modulesAdd'])->name('modulesAdd');
		    Route::get('modules-edit',[SettingsController::class,'modulesEdit'])->name('modulesEdit');
		    Route::post('modules-update',[SettingsController::class,'modulesUpdate'])->name('modulesUpdate');
		    Route::post('modules-status',[SettingsController::class,'modulesStatusUpdate'])->name('modulesStatusUpdate');
		    Route::get('modules-delete',[SettingsController::class,'modulesDelete'])->name('modulesDelete');		 
	    /*Routes for modules ends*/
	    
	    
	      /*Routes for modules starts*/
		    Route::get('draggable-menu',[SettingsController::class,'draggableMenu']);
		    Route::post('updateMenuOrder', [SettingsController::class, 'updateMenuOrder'])->name('updateMenuOrder');
            Route::post('updateSubMenuOrder', [SettingsController::class, 'updateSubMenuOrder'])->name('updateSubMenuOrder');
	    /*Routes for modules ends*/

	    /*Routes for departments starts*/
		    Route::get('departments',[SettingsController::class,'departments']);
		    Route::post('departments-add',[SettingsController::class,'departmentsAdd'])->name('departmentsAdd');
		    Route::get('departments-edit',[SettingsController::class,'departmentsEdit'])->name('departmentsEdit');
		    Route::post('departments-update',[SettingsController::class,'departmentsUpdate'])->name('departmentsUpdate');
		    Route::post('departments-status',[SettingsController::class,'departmentsStatusUpdate'])->name('departmentsStatusUpdate');
		    Route::get('departments-delete',[SettingsController::class,'departmentsDelete'])->name('departmentsDelete');
	    /*Routes for departments ends*/

	     /*Routes for leads status starts*/
		    Route::get('leads-status',[SettingsController::class,'leadsStatus']);
		    Route::post('leads-add',[SettingsController::class,'leadsStatusAdd'])->name('leadsStatusAdd');
		    Route::get('leads-edit',[SettingsController::class,'leadsStatusEdit'])->name('leadsStatusEdit');
		    Route::post('leads-update',[SettingsController::class,'leadsStatusUpdate'])->name('leadsStatusUpdate');
		    Route::post('leads-status',[SettingsController::class,'leadsStatusStatusUpdate'])->name('leadsStatusStatusUpdate');
		    Route::get('leads-delete',[SettingsController::class,'leadsStatusDelete'])->name('leadsStatusDelete');
	    /*Routes for leads status ends*/

	    /*Routes for roi starts*/
		    Route::get('roi',[SettingsController::class,'roi']);
		    Route::post('roi-add',[SettingsController::class,'roiAdd'])->name('roiAdd');
		    Route::get('roi-edit',[SettingsController::class,'roiEdit'])->name('roiEdit');
		    Route::post('roi-update',[SettingsController::class,'roiUpdate'])->name('roiUpdate');
		    Route::post('roi-status',[SettingsController::class,'roiStatusUpdate'])->name('roiStatusUpdate');
		    Route::get('roi-delete',[SettingsController::class,'roiDelete'])->name('roiDelete');
	    /*Routes for roi ends*/
 
	    /*Routes for designations starts*/
		    Route::get('designations',[SettingsController::class,'designations']);
		    Route::post('designations-add',[SettingsController::class,'designationsAdd'])->name('designationsAdd');
		    Route::get('designations-edit',[SettingsController::class,'designationsEdit'])->name('designationsEdit');
		    Route::post('designations-update',[SettingsController::class,'designationsUpdate'])->name('designationsUpdate');
		    Route::post('designations-status',[SettingsController::class,'designationsStatusUpdate'])->name('designationsStatusUpdate');
		    Route::get('designations-delete',[SettingsController::class,'designationsDelete'])->name('designationsDelete');
 	    /*Routes for designations ends*/

		  /*Routes for branches starts*/
		    Route::get('branches',[SettingsController::class,'index']);
		    Route::post('branch-add',[SettingsController::class,'branchAdd'])->name('branchAdd');
		    Route::get('branch-edit',[SettingsController::class,'branchEdit'])->name('branchEdit');
		    Route::post('branch-update',[SettingsController::class,'branchUpdate'])->name('branchUpdate');
		    Route::post('branch-status',[SettingsController::class,'branchStatusUpdate'])->name('branchStatusUpdate');
		    Route::get('branch-delete',[SettingsController::class,'branchDelete'])->name('branchDelete');
		    Route::get('branch-fetch',[SettingsController::class,'getAllBranches'])->name('getAllBranches');
	    /*Routes for branches ends*/

	    /*Routes for company starts*/
		    Route::get('organisation',[SettingsController::class,'organisation']);
		    Route::post('organisation-add',[SettingsController::class,'organisationAdd']);
	    /*Routes for company ends*/

	    /*Routes for branch target starts*/
		    Route::get('branch-target',[SettingsController::class,'branchTarget']);
		    Route::post('branch-target-add',[SettingsController::class,'branchTargetAdd'])->name('branchTargetAdd');
		    Route::get('branch-target-edit',[SettingsController::class,'branchTargetEdit'])->name('branchTargetEdit');
		    Route::post('branch-target-update',[SettingsController::class,'branchTargetUpdate'])->name('branchTargetUpdate');
		    Route::get('branch-target-delete',[SettingsController::class,'branchTargetDelete'])->name('branchTargetDelete');
	    /*Routes for branch target ends*/

	    /*Routes for sanction target starts*/
		    Route::get('sanction-target',[SettingsController::class,'sanctionTarget']);
		    Route::post('sanction-target-add',[SettingsController::class,'sanctionTargetAdd'])->name('sanctionTargetAdd');
		    Route::get('sanction-target-edit',[SettingsController::class,'sanctionTargetEdit'])->name('sanctionTargetEdit');
		    Route::post('sanction-target-update',[SettingsController::class,'sanctionTargetUpdate'])->name('sanctionTargetUpdate');
		    Route::get('sanction-target-delete',[SettingsController::class,'sanctionTargetDelete'])->name('sanctionTargetDelete');
	    /*Routes for sanction target ends*/

	    /*Routes for states starts*/
		    Route::get('states',[SettingsController::class,'states']);
		    Route::post('states-add',[SettingsController::class,'statesAdd'])->name('statesAdd');
		    Route::get('states-edit',[SettingsController::class,'statesEdit'])->name('statesEdit');
		    Route::post('states-update',[SettingsController::class,'statesUpdate'])->name('statesUpdate');
		    Route::post('states-status',[SettingsController::class,'statesStatusUpdate'])->name('statesStatusUpdate');
		    Route::get('states-delete',[SettingsController::class,'statesDelete'])->name('statesDelete');
	    /*Routes for states ends*/

	    /*Routes for approval matrix starts*/
		    Route::get('approval-matrix-list',[SettingsController::class,'approvalMatrixList']);
		    Route::get('get-users-by-designation/{designations}',[SettingsController::class,'getUsersByDesignation'])->name('getUsersByDesignation');
		    Route::post('approval-matrix-add',[SettingsController::class,'approvalMatrixAdd'])->name('approvalMatrixAdd');
		    Route::post('approval-matrix-update',[SettingsController::class,'approvalMatrixUpdate'])->name('approvalMatrixUpdate');
		    Route::get('approval-matrix-edit',[SettingsController::class,'approvalMatrixEdit'])->name('approvalMatrixEdit');
		    Route::post('approval-matrix-status',[SettingsController::class,'approvalMatrixStatusUpdate'])->name('approvalMatrixStatusUpdate');
		  
		    Route::get('approval-matrix-delete',[SettingsController::class,'approvalMatrixDelete'])->name('approvalMatrixDelete');
	    /*Routes for approval matrix ends*/

	    /*Routes for cities starts*/
		    Route::get('cities',[SettingsController::class,'cities']);
		    Route::post('cities-add',[SettingsController::class,'citiesAdd'])->name('citiesAdd');
		    Route::get('cities-edit',[SettingsController::class,'citiesEdit'])->name('citiesEdit');
		    Route::post('cities-update',[SettingsController::class,'citiesUpdate'])->name('citiesUpdate');
		    Route::post('cities-status',[SettingsController::class,'citiesStatusUpdate'])->name('citiesStatusUpdate');
		    Route::get('cities-delete',[SettingsController::class,'citiesDelete'])->name('citiesDelete');
	    /*Routes for cities ends*/
	    
	    /*Routes for optional modules starts*/
		    Route::get('optional-modules',[SettingsController::class,'optionalModules']);
		    Route::post('optional-modules-status-update',[SettingsController::class,'optionalModulesStatusUpdate'])->name('optionalModulesStatusUpdate');
	    /*Routes for optional modules ends*/

	});
});

/*Group Routes for Settings ends*/

/*Middleware for all routes for user logged in or not starts*/
/*Routes for BankStatemet starts*/
Route::middleware(['userAuth'])->group(function () {
	Route::prefix('bsa')->group(function () {
		Route::get('/', [BankStatementController::class, 'index'])->name('bsa.index');
		Route::post('statement', [BankStatementController::class, 'submitStatement'])->name('bsa.statement');
		Route::get('get-results', [BankStatementController::class, 'getResults'])->name('bsa.get-results');
	});
});



/*Group Routes for Users starts*/
Route::middleware(['userAuth'])->group(function () { // middleware to check if the user is authenticated
    Route::prefix('users')->group(function () {
        /*Routes for users starts*/
        Route::get('master', [UsersController::class, 'index'])->middleware('checkUserCreate');
        Route::get('add-users', [UsersController::class, 'addUsers'])->middleware('checkUserCreate');
        Route::get('update-users/{empId}', [UsersController::class, 'updateUsers'])->middleware('checkUserCreate');
        Route::get('get-designations/{department}', [UsersController::class, 'getDesignations'])->name('getDesignations');
        Route::post('personal-details', [UsersController::class, 'addPersonalDetails'])->name('step1PersonalDetails')->middleware('checkUserCreate');
        Route::post('official-details', [UsersController::class, 'addOfficialDetails'])->name('step2OfficialDetails')->middleware('checkUserCreate');
        Route::post('kyc-details', [UsersController::class, 'addKycDetails'])->name('step3KycDetails')->middleware('checkUserCreate');
        Route::get('save-details', [UsersController::class, 'SaveUserDetails'])->name('step4SaveDetails')->middleware('checkUserCreate');
        Route::get('users-list', [UsersController::class, 'usersList'])->name('usersList');
        Route::post('users-status', [UsersController::class, 'usersStatusUpdate'])->name('usersStatusUpdate')->middleware('checkUserCreate');
        Route::get('users-delete', [UsersController::class, 'usersDelete'])->name('usersDelete')->middleware('checkUserCreate');
        Route::post('users-view', [UsersController::class, 'usersViewDetails'])->name('usersViewDetails');
        /*Routes for users ends*/

        /*Routes for users roles starts*/
        Route::get('users-roles/{empId}', [UsersController::class, 'usersRoles'])->middleware('checkUserCreate');
        Route::post('add-users-roles', [UsersController::class, 'addUsersRoles'])->name('addUsersRoles')->middleware('checkUserCreate');
        Route::get('fetch-modules', [UsersController::class, 'fetchModules'])->name('fetchModules')->middleware('checkUserCreate');
        Route::get('fetch-modules-edit', [UsersController::class, 'fetchModulesEditData'])->name('fetchModulesEditData')->middleware('checkUserCreate');
        Route::get('fetch-reporting-managers', [UsersController::class, 'fetchReportingManagers'])->name('fetchReportingManagers')->middleware('checkUserCreate');
        /*Routes for users roles ends*/
    });
});
/*Group Routes for Users ends*/


/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Disbursal starts*/
		Route::prefix('activity')->group(function () {
			/*Routes for display all activity logs*/
	     	Route::get('logs', [ActivityController::class, 'activityLogs']);
	     	Route::get('lead-transfer-logs', [ActivityController::class, 'activityLeadsTransferLogs']);
			/*Routes for display all activity logs*/

		});
	});


/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Disbursal starts*/
		Route::prefix('attendance')->group(function () {
			/*Routes for display all activity logs*/
	     	Route::get('list', [AttendanceController::class, 'attendanceList']);
	     	Route::get('show-attendance-log', [AttendanceController::class, 'showAttendanceLog'])->name('showAttendanceLog');
	     	Route::post('attendance-add', [AttendanceController::class, 'attendanceAdd'])->name('attendanceAdd');
	     	Route::post('attendance-edit', [AttendanceController::class, 'attendanceEdit'])->name('attendanceEdit');
			/*Routes for display all activity logs*/

		});
	});



/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Disbursal starts*/
		Route::prefix('soa')->group(function () {
			/*Routes for display all activity logs*/
	          Route::get('standard-soa/{leadID}', [SoaController::class, 'standardSoa']);
	     	/*Routes for display all activity logs*/
		});
	});



/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Disbursal starts*/
		Route::prefix('support')->group(function () {
			/*Routes for display all activity logs*/
	     	Route::get('tickets-category', [SupportController::class, 'ticketsCategory']);
	     	Route::get('tickets/{supportType?}', [SupportController::class, 'tickets']);
	     	Route::post('generate-ticket', [SupportController::class, 'generateTicket'])->name('generateTicket');
	     	Route::get('view-ticket/{ticketID}', [SupportController::class, 'viewTicket'])->name('viewTicket');
	     	Route::post('reply-ticket', [SupportController::class, 'replyTicket'])->name('replyTicket');
	     	Route::post('assign-user-ticket', [SupportController::class, 'assignUserTicket'])->name('assignUserTicket');
			/*Routes for display all activity logs*/

		});
	});	

/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Leads starts*/
		Route::prefix('leads')->group(function () {

			/*Routes for display/filter all Status wise Leads starts*/
	     		Route::get('list/{leadType?}', [LeadsController::class, 'statusWiseLeads'])->name('leads.list');
				Route::get('filter/{leadType}', [LeadsController::class, 'statusWiseLeads'])->name('leads.filter');
			/*Routes for display/filter all Status wise Leads Leads end*/

			/*Routes for enquiry Leads starts*/
			Route::get('enquiry', [LeadsController::class, 'enquiryList']);
			/*Routes for enquiry Leads end*/

			/*Routes for display/filter all Leads starts*/
		     	Route::get('all-leads', [LeadsController::class, 'allLeads']);
		    /*Routes for display/filter all Leads end*/


			/*Routes for Leads Delete starts*/
				Route::post('leadsDelete', [LeadsController::class, 'leadsDelete'])->name('leadsDelete');
		    /*Routes for Leads Delete starts*/

		    /*Routes for Approval matrix starts*/
				Route::get('approval-matrix-leads', [LeadsController::class, 'approvalMatrixLeads']);
		    /*Routes for Approval matrix starts*/


		    /*Routes for Fresh Leads starts*/
		    	Route::get('lead-view/{leadID}',[LeadsController::class,'leadView'])->name('leadView');
		    	Route::post('fresh-leads-add',[LeadsController::class,'addLeadManual'])->name('leadAdd');
		    	Route::get('fresh-leads-edit',[LeadsController::class,'freshLeadEdit'])->name('freshLeadEdit');
		    	Route::get('fetch-cities',[LeadsController::class,'fetchCities'])->name('fetchCities');
		    /*Routes for Fresh Leads ends*/

	      /*Routes for Pancard Details starts*/
	    	Route::post('verify-pancard',[LeadsController::class,'verifyPancard'])->name('verifyPancard');
	      /*Routes for Pancard Details ends*/

	       /*Routes for Bulk Leads Import starts*/
	    	Route::post('import-leads',[LeadsController::class,'importLeads'])->name('importLeads');
	      /*Routes for Bulk Leads Import ends*/

	       /*Routes for Leads Transfer starts*/
	    	Route::post('leads-transfer',[LeadsController::class,'leadsTransfer'])->name('leadsTransfer');
	      /*Routes for Leads Transfer ends*/

	      

	       
	       /*Routes for lead assignments starts*/
	    	Route::get('rm-list',[LeadsController::class,'rmList']);
	    	Route::post('rm-assignment-status-update',[LeadsController::class,'leadAssignmentRMstatusUpdate'])->name('leadAssignmentRMstatusUpdate');
	    	Route::get('cm-list',[LeadsController::class,'cmList']);
	    	Route::post('cm-assignment-status-update',[LeadsController::class,'leadAssignmentCMstatusUpdate'])->name('leadAssignmentCMstatusUpdate');
	    	
	        Route::post('assign-rm',[LeadsController::class,'assignRM'])->name('assignRM');
	        Route::post('assign-cm',[LeadsController::class,'assignCM'])->name('assignCM');
	    	
	      /*Routes for lead assignments ends*/


	      /*Routes for Fetch credit managers from RMID starts*/
		    	Route::get('fetch-relationship-managers', [LeadsController::class, 'fetchRelationshipManagers'])->name('fetchRelationshipManagers');
		   /*Routes for Fetch credit managers from RMID ends*/

		      
		});
	});
/*Group Routes for Users ends*/



/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Sanction starts*/
		Route::prefix('sanction')->group(function () {
				/*Routes for display/filter all Sanction starts*/
			   	Route::get('approved', [SanctionController::class, 'approvedLeads']);
			   	Route::get('customer-approved', [SanctionController::class, 'customerApprovedLeads']);
		     	Route::get('sanction-rejected', [SanctionController::class, 'sanctionRejectedLeads']);
		     	Route::get('direct-rejected', [SanctionController::class, 'directRejectedLeads']);
		     	Route::get('approval-pending-list', [SanctionController::class, 'approvalPendingLeads']);
		     	Route::get('approval-pending-edit', [SanctionController::class, 'pendingApprovalEdit'])->name('pendingApprovalEdit');
		     	Route::post('approval-pending-update', [SanctionController::class, 'pendingApprovalUpdate'])->name('pendingApprovalUpdate');
		     	Route::get('enach', [SanctionController::class, 'enachList'])->name('enachList');
				/*Routes for display/filter all Sanction end*/

		});
	});
/*Group Routes for Users ends*/


/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Disbursal starts*/
		Route::prefix('disbursal')->group(function () {
				/*Routes for display/filter all Disbursal starts*/
		     	Route::get('pending-for-disburse', [DisbursalController::class, 'disbursalSheetSendLeads']);
		     	Route::get('disbursed', [DisbursalController::class, 'disbursedLeads']);
				/*Routes for display/filter all Disbursal end*/

		});
	});
/*Group Routes for Users ends*/


/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Disbursal starts*/
		Route::prefix('marketing')->group(function () {
			/*Routes for display all activity logs*/
	     	Route::get('cm-report-fresh-repeated', [MarketingController::class, 'cmFreshRepeated']);
	     	Route::get('branch-report-fresh-repeated', [MarketingController::class, 'branchFreshRepeated']);
	     	Route::get('pincode-report-fresh-repeated', [MarketingController::class, 'pincodeFreshRepeated']);
	     	Route::get('employment-wise', [MarketingController::class, 'employmentWise']);
	     	Route::get('salary-wise', [MarketingController::class, 'salaryWise']);
	     	Route::get('lead-status-wise', [MarketingController::class, 'leadStatusWise']);
	     	Route::get('utm-source-wise', [MarketingController::class, 'utmSourceWise']);
	     	Route::get('utm-fresh-repeated-wise', [MarketingController::class, 'utmFreshRepeatedWise']);
			/*Routes for display all activity logs*/
		});
	});
	

/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Red Flag Customers*/
		Route::prefix('redflag')->group(function () {
		     	Route::get('list', [RedFlagController::class, 'redFlagList']);
		     	Route::post('redflag-approval', [RedFlagController::class, 'redFlagApprovalStatusUpdate'])->name('redFlagLoanApprovalStatusUpdate');
		     	 
		});
	});
/*Group Routes for Users ends*/



/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Disbursal starts*/
		Route::prefix('collection')->group(function () {
				/*Routes for display/filter all Disbursal starts*/
		     	Route::get('part-payment', [CollectionController::class, 'partPaymentLeads']);
		     	Route::get('closed', [CollectionController::class, 'closedLeads']);
		     	Route::get('settlement', [CollectionController::class, 'settlementLeads']);
		     	Route::get('settled-to-closed', [CollectionController::class, 'settleToClosedLeads']);
		        Route::get('emi-pending', [CollectionController::class, 'emiPending']);
		        Route::get('get-repayment-schedule-data', [CollectionController::class, 'getRepaymentScheduleData']);
				/*Routes for display/filter all Disbursal end*/
		});
	});
/*Group Routes for Users ends*/



/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Change Password starts*/
		Route::prefix('settings')->group(function () {
				/*Routes for Change Password starts*/
		     	Route::get('change-password', [SettingsController::class, 'changePassword']);
		     	Route::post('update-password', [SettingsController::class, 'updatePassword']);
				/*Routes for Change Password end*/
		});
	});
/*Group Routes for Change Password ends*/


/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for E-Sign and Video Kyc List*/
		Route::prefix('kyc')->group(function () {
		     	Route::get('e-sign', [EsignVideoKycController::class, 'esignList']);
		     	Route::get('video', [EsignVideoKycController::class, 'videoList']);
		        Route::post('esignKycResendUpdate', [EsignVideoKycController::class, 'esignKycResendUpdate'])->name('esignKycResendUpdate');
		        Route::post('videoKycResendUpdate', [EsignVideoKycController::class, 'videoKycResendUpdate'])->name('videoKycResendUpdate');
		});
	});
/*Group Routes for Users ends*/

/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Reporting starts*/
		Route::prefix('reporting')->group(function () {
				/*Routes for Reporting starts*/
				Route::get('list/{reportingType?}', [ReportingController::class, 'leadWiseReporting'])->name('reporting.list');
				Route::get('filter/{reportingType}', [ReportingController::class, 'leadWiseReporting'])->name('reporting.filter');
				/*Routes for Reporting end*/
		});
	});
/*Group Routes for Change Password ends*/

 
/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Profile starts*/
		Route::prefix('profile')->group(function () {
		    
				Route::get('edit-profile-address', [ProfileController::class, 'editProfileAddress'])->name('editProfileAddress');
				Route::get('edit-profile-company', [ProfileController::class, 'editProfileCompany'])->name('editProfileCompany');
				Route::get('edit-profile-reference', [ProfileController::class, 'editProfileReference'])->name('editProfileReference');
				Route::get('edit-profile-document', [ProfileController::class, 'editProfileDocument'])->name('editProfileDocument');
				Route::get('edit-profile-collection', [ProfileController::class, 'editProfileCollection'])->name('editProfileCollection');
				Route::get('edit-profile-info', [ProfileController::class, 'profileInfoEdit'])->name('profileInfoEdit');
            	Route::get('edit-profile-checklist', [ProfileController::class, 'editProfileChecklist'])->name('editProfileChecklist');
            	Route::post('update-profile-info', [ProfileController::class, 'profileInfoUpdate'])->name('profileInfoUpdate');
            	Route::post('update-doc-pd', [ProfileController::class, 'updateDocByPD'])->name('updateDocByPD');
            	Route::post('update-add-pd', [ProfileController::class, 'updateDocByPD'])->name('updateAddByPD');
            	
				Route::post('add-timeline', [ProfileController::class, 'addTimeline']);
				Route::post('add-address', [ProfileController::class, 'addAddress']);
				Route::post('add-company', [ProfileController::class, 'addCompany']);
				Route::post('add-reference', [ProfileController::class, 'addReference']);
				Route::post('add-pd-verification', [ProfileController::class, 'addPdVerification']);
				Route::post('add-documents', [ProfileController::class, 'addDocuments']);
				Route::post('add-aadhaar', [ProfileController::class, 'addDocuments']);
				Route::post('add-checklist', [ProfileController::class, 'addChecklist']);
				Route::post('add-sanction', [ProfileController::class, 'addSanction']);
				Route::post('reject-sanction', [ProfileController::class, 'rejectSanction']);
				Route::post('add-disbursed', [ProfileController::class, 'addDisbursed']);
				Route::post('add-customer-remarks', [ProfileController::class, 'addCustomerRemarks']);
				Route::post('add-collection', [ProfileController::class, 'addCollection']);
				
				Route::post('update-sanction', [ProfileController::class, 'updateSanction']);
				Route::post('update-sanction', [ProfileController::class, 'updateSanction']);
				Route::post('update-disbursed', [ProfileController::class, 'updateDisbursed']);
				Route::post('update-collection', [ProfileController::class, 'updateCollection']);
				
				Route::get('delete-address', [ProfileController::class, 'addressDelete'])->name('addressDelete');
				Route::get('delete-company', [ProfileController::class, 'companyDelete'])->name('companyDelete');
				Route::get('delete-reference', [ProfileController::class, 'referenceDelete'])->name('referenceDelete');
				Route::get('delete-documents', [ProfileController::class, 'documentsDelete'])->name('documentsDelete');
				Route::get('delete-checklist', [ProfileController::class, 'checklistDelete'])->name('checklistDelete');
			    
			    Route::get('get-pd-person', [ProfileController::class, 'getPdPerson'])->name('getPdPerson');
			    Route::post('videokyc-download', [ProfileController::class, 'videokycDownload']);
			    	
			 //   Route::get('{leadID}', [ProfileController::class, 'profile']);
				// Route::get('send-esign-request/{leadID}/{contactID}', [ProfileController::class, 'sendEsignRequest'])->name('sendEsignRequest');
				// Route::get('send-videokyc-request/{leadID}/{contactID}', [ProfileController::class, 'sendVideoKycRequest'])->name('sendVideoKycRequest');
				// Route::get('get-estamp/{leadID}', [ProfileController::class, 'initiateEstamp']);
				// Route::get('check-estamp/{leadID}', [ProfileController::class, 'fetchEstampDocument']);
				// Route::get('send-enach-request/{leadID}', [ProfileController::class, 'sendEnachRequest'])->name('sendEnachRequest');
				// Route::get('esign-document-verify/{leadID}', [ProfileController::class, 'esignDocVerify']);
				// Route::get('videokyc-verify/{leadID}', [ProfileController::class, 'videoKycVerify']);	
				// Route::get('esign-document-download/{leadID}', [ProfileController::class, 'esignDocDownload']);
				// Route::get('kyc-details/{leadID}', [ProfileController::class, 'kycView']);
				// Route::get('proxy/{mediaId}', [ProfileController::class, 'fetchMedia']);
				
				// Route::post('videokyc-download', [ProfileController::class, 'videokycDownload']);
				// Route::post('videokyc-cm-approval', [ProfileController::class, 'videokycCMApproval']);
					
				// Route::get('kyc-details/{leadID}', [ProfileController::class, 'kycView']);
				
				// Route::get('proxy/{mediaId}', [ProfileController::class, 'fetchMedia']);
				Route::get('nbb-video-kyc2', [ProfileController::class, 'fetchMediaNbb'])->name('nbbVideoKyc2');
				
				Route::get('{leadID}', [ProfileController::class, 'profile']);
                Route::get('send-esign-request/{leadID}/{contactID}', [ProfileController::class, 'sendEsignRequest'])->name('sendEsignRequest');
                Route::get('send-videokyc-request/{leadID}/{contactID}', [ProfileController::class, 'sendVideoKycRequest'])->name('sendVideoKycRequest');
                Route::get('get-estamp/{leadID}', [ProfileController::class, 'initiateEstamp']);
                Route::get('check-estamp/{leadID}', [ProfileController::class, 'fetchEstampDocument']);
                Route::get('send-enach-request/{leadID}', [ProfileController::class, 'sendEnachRequest'])->name('sendEnachRequest');
                Route::get('esign-document-verify/{leadID}', [ProfileController::class, 'esignDocVerify']);
                Route::get('videokyc-verify/{leadID}', [ProfileController::class, 'videoKycVerify']);
                Route::get('esign-document-download/{leadID}', [ProfileController::class, 'esignDocDownload']);
                Route::get('kyc-details/{leadID}', [ProfileController::class, 'kycView']);
                Route::get('proxy/{mediaId}', [ProfileController::class, 'fetchMedia']);
                Route::get('nbb-video-kyc', [ProfileController::class, 'fetchMediaNbb'])->name('nbbVideoKyc');
                
                Route::post('videokyc-download', [ProfileController::class, 'videokycDownload']);
                Route::post('videokyc-cm-approval', [ProfileController::class, 'videokycCMApproval']);

		        // profile routes for loan 
		       
		       
		});
	});
/*Group Routes for Profile ends*/
/*Middleware for all routes for user logged in or not ends*/

/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Profile starts*/
		Route::prefix('profile-cashpey')->group(function () {
		        Route::post('penny-verification', [ProfileLoanController::class, 'pennyVerification'])->name('pennyVerification');
		        Route::post('pending-for-disbursal', [ProfileLoanController::class, 'pendingToDisburse'])->name('pendingToDisburse');
				Route::post('approved-to-disburse', [ProfileLoanController::class, 'approvedToDisburse'])->name('approvedToDisburse');
				Route::post('add-repayment', [ProfileLoanController::class, 'addRepayment'])->name('addRepayment');
				Route::post('add-emi-interest', [ProfileLoanController::class, 'addEmiInterestAmount'])->name('addEmiInterest');
				Route::post('approved-to-final', [ProfileLoanController::class, 'approvedToFinal'])->name('approvedToFinal');
		        Route::get('loan/{leadID}', [ProfileLoanController::class, 'profileLoan']);
		});
	});
/*Group Routes for Profile ends*/
/*Middleware for all routes for user logged in or not ends*/


/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Profile starts*/
		Route::prefix('soa')->group(function () {
		        Route::get('generate-soa/{leadID}', [SoaController::class, 'generatePDf']);    
		});
	});
/*Group Routes for Profile ends*/
/*Middleware for all routes for user logged in or not ends*/


/*Routes for dashboard starts*/
	Route::get('dashboard',[DashboardController::class,'index'])->middleware('userAuth')->name('dashboard');
	Route::get('notifications-count',[DashboardController::class,'notificationsCount'])->middleware('userAuth')->name('notificationsCount');
/*Routes for dashboard ends*/
 
 
 
/*Middleware for all routes for user logged in or not starts*/
	Route::middleware(['userAuth'])->group(function (){
	/*Group Routes for Reporting starts*/
		Route::prefix('mail')->group(function () {
				/*Routes for Mails starts*/
			    	Route::get('send-status/{leadID}',[MailController::class,'sendStatus']);
			    	Route::get('send-status-document/{leadID}',[MailController::class,'sendStatusDocument']); 
			    	Route::get('send-welcome-letter/{leadID}', [MailController::class, 'welcomeLetter'])->name('welcomeLetter');
			    	Route::post('sanctionApproval',[MailController::class,'sanctionApproval']);
			    	Route::post('sanctionRejection',[MailController::class,'sanctionRejection']);
			    	Route::post('add-communication-mail', [MailController::class, 'addCommunicationMail']);
			    	Route::get('test', [MailController::class, 'mailTest']);

				/*Routes for Mails end*/
		});
	});
/*Group Routes for Change Password ends*/

Route::get('customer-approval/{leadID}', [ProfileLoanController::class, 'acceptByCustomer'])->name('acceptByCustomer');
Route::get('enach-registration/{leadID}', [EnachController::class, 'enachRegister'])->name('enachRegister');
Route::post('enach-success', [EnachController::class, 'enachSuccess']);

  