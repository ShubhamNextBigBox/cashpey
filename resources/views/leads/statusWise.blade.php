@extends('layouts.master')
@section('page-title',$page_info['page_title'])
@section('main-section')
<div class="content-page">
<div class="content">
<!-- Start Content-->
<div class="container-fluid">
<!-- start page title -->
<div class="row">
   <div class="col-12">
      <div class="page-title-box">
         <div class="page-title-right">
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="javascript: void(0);">{{$page_info['page_title']}}</a></li>
               <li class="breadcrumb-item active">{{$page_info['page_name']}}</li>
            </ol>
         </div>
         <h4 class="page-title">{{$page_info['page_name']}}</h4>
      </div>
   </div>
</div>
<!-- end page title -->
<div class="row">
   <div class="col-12">
      <ul class="nav nav-tabs mb-1">
         <li class="nav-item">
            <a href="leads/list/fresh" class="nav-link @if ($activeTab == 'fresh') active @endif">
            <i class="mdi mdi-home-variant d-md-none d-block"></i>
            <span class="d-none d-md-block">Fresh</span>
            </a>
         </li>
         <li class="nav-item">
            <a href="leads/list/reloan-fresh" class="nav-link @if ($activeTab == 'reloan-fresh') active @endif">
            <i class="mdi mdi-account-circle d-md-none d-block"></i>
            <span class="d-none d-md-block">Reloan Fresh</span>
            </a>
         </li>
         <li class="nav-item">
            <a href="leads/list/callback" class="nav-link @if ($activeTab == 'callback') active @endif">
            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
            <span class="d-none d-md-block">Callback</span>
            </a>
         </li>
         <li class="nav-item">
            <a href="leads/list/no-answer" class="nav-link @if ($activeTab == 'no-answer') active @endif">
            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
            <span class="d-none d-md-block">No Answer</span>
            </a>
         </li>
         <li class="nav-item">
            <a href="leads/list/interested" class="nav-link @if ($activeTab == 'interested') active @endif">
            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
            <span class="d-none d-md-block">Interested</span>
            </a>
         </li>
         <li class="nav-item">
            <a href="leads/list/not-eligible" class="nav-link @if ($activeTab == 'not-eligible') active @endif">
            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
            <span class="d-none d-md-block">Not Eligible</span>
            </a>
         </li>
         <li class="nav-item">
            <a href="leads/list/incomplete-documents" class="nav-link @if ($activeTab == 'incomplete-documents') active @endif">
            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
            <span class="d-none d-md-block">Incomplete Docs</span>
            </a>
         </li>
         <li class="nav-item">
            <a href="leads/list/less-salary" class="nav-link @if ($activeTab == 'less-salary') active @endif">
            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
            <span class="d-none d-md-block">Less Salary</span>
            </a>
         </li>
         <li class="nav-item">
            <a href="leads/list/document-received" class="nav-link @if ($activeTab == 'document-received') active @endif">
            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
            <span class="d-none d-md-block">Document Received</span>
            </a>
         </li>
         <li class="nav-item">
            <a href="leads/list/rejected" class="nav-link @if ($activeTab == 'rejected') active @endif">
            <i class="mdi mdi-settings-outline d-md-none d-block"></i>
            <span class="d-none d-md-block">Rejected</span>
            </a>
         </li>
      </ul>
      
      <div class="card">
         <div class="tab-content">
            <div class="card-header new_code_show">
               <div class="row">
                  <div class="col-md-12">
                     <form action="{{ route('leads.filter', ['leadType' => $activeTab]) }}" method="get">
                        <div class="row new_col_new">
                          
                           <div class="col-md-3">
                              <!-- Adjusted column width -->
                              <div class="input-group">
                                 <span class="input-group-text">
                                 <i class="uil uil-filter"></i>
                                 </span>
                                 <select class="form-select exportSelect" name="filter">
                                 <option value="sortBySearch"  {{ $filter === 'sortBySearch' ? 'selected' : '' }}>Search</option>
                                 <option value="sortByToday"  {{ $filter === 'sortByToday' ? 'selected' : '' }}>Today</option>
                                 <option value="sortByWeek"  {{ $filter === 'sortByWeek' ? 'selected' : '' }}>Last 7 Days</option>
                                 <option value="sortByThisMonth"  {{ $filter === 'sortByThisMonth' ? 'selected' : '' }}>Current Month</option>
                                 <option value="sortByLastMonth"  {{ $filter === 'sortByLastMonth' ? 'selected' : '' }}>Previous Month</option>
                                 <option value="sortByDate" {{ $filter === 'sortByDate' ? 'selected' : '' }}>Date Range</option>
                                 @if(isSuperAdmin())
                                 <option value="exportAll" {{ $filter === 'exportAll' ? 'selected' : '' }}>Export All Data</option>
                                 @endif
                                 @if(rolesAccess('Status Wise', 'export') || isSuperAdmin() || isAdmin())
                                 <option value="exportByDate" {{ $filter === 'exportByDate' ? 'selected' : '' }}>Export by Date Range</option>
                                 @endif
                                 </select>
                              </div>
                           </div>
                           
                           
                           <div class="col-md-3 searchBoxContainer">
                              <div class="input-group">
                                 <input type="text" name="search" class="form-control searchInput" placeholder="Name, email, mobile, pancard...">
                                 <button class="btn btn-primary" type="submit" id="searchButton">
                                 <i class="uil uil-search"></i>
                                 </button>
                              </div>
                           </div>
                           
                           
                           <div class="col-md-3 dateRangeContainer"  style="display: none;">
                              <div class="input-group">
                                 <input type="text" class="form-control date singledaterange" name="searchRange" data-toggle="date-picker" data-cancel-class="btn-warning">
                                 <button class="btn btn-primary customSearchButton cus_new" type="submit">
                                 <i class="uil uil-search"></i>
                                 </button>
                              </div>
                           </div>
                           
                           
                           @if(isSuperAdmin())
                           <div class="col-md-1 exportAllContainer" style="display: none;">
                              <button class="btn btn-primary" type="submit">
                              <i class="uil uil-export"></i> Export
                              </button>
                           </div>
                           @endif
                           @if(rolesAccess('Status Wise', 'export') || isSuperAdmin() || isAdmin())
                           <div class="col-md-4 exportByDateContainer">
                              <div class="input-group">
                                 <input type="text" class="form-control date exportDateRange" name="exportRange" id="exportRange" 
                                    data-toggle="date-picker" data-cancel-class="btn-warning">
                                 <button class="btn btn-primary exportByDateRangeButton" type="submit">
                                 <i class="uil uil-export"></i> Export
                                 </button>
                              </div>
                           </div>
                           
                           
                           @endif
                           @if(!empty($filter))
                           <div class="col-md-1 text-center">
                              <a href="leads/list/{{$activeTab}}" class="btn btn-outline-primary" data-toggle="tooltip" data-original-title="success">
                              <i class="uil uil-refresh"></i>
                              </a>
                           </div>
                            
                           @endif
                           @if ($activeTab == 'fresh')
                           @if(isSuperAdmin())
                           <div class="col-md-1 text-center deleteButton"  style="display:none;">
                              <button type="button" class="btn btn-danger">
                              <i class="uil uil-trash"></i>
                              </button>
                           </div>
                           
                           
                           @endif
                           @if(role()=='Sr. Credit Manager' || isSuperAdmin() || isAdmin())
                           <div class="col-md-1 text-center bulkAssignButton"  style="display:none;">
                              <button type="button" class="btn text-bg-primary">
                              <i class="uil uil-exchange-alt"></i>
                              </button>
                           </div>
                           
                           
                           @endif
                           @if(isSuperAdmin() || isAdmin())
                          {{--  <div class="col-md-1 text-center">
                              <button type="button" id="importLeadButton" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-target="#success-header-modal" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Import Leads">
                              <i class="mdi mdi-publish"></i>
                              </button>
                           </div>
                            --}}
                           
                           @endif
                           @if(rolesAccess('Status Wise', 'add') || isSuperAdmin() || isAdmin())
                           <div class="col-md-1 text-center">
                              <button type="button" id="addFreshLeadBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#right-modal">
                              <i class="uil uil-plus"></i> Add
                              </button>
                           </div>
                           
                           
                           @endif
                           @endif
                        </div>
                        
                        
                     </form>
                  </div>
               </div>
            </div>
            <div class="tab-pane @if ($activeTab == 'fresh') show active @endif" id="fresh-leads">
               <div class="card-body"  data-simplebar data-simplebar-lg >
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           @if(isSuperAdmin() || isAdmin())
                           <th>
                              <input type="checkbox" class="form-check-input" id="checkAll">
                           </th>
                           @endif
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="15" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           @if(isSuperAdmin() || isAdmin())
                           <td>
                              <input type="checkbox" class="leadCheckbox form-check-input" value="{{$arr->leadID}}">
                           </td>
                           @endif
                           <td>{{$serial++}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                              @endif
                              @if(rolesAccess('Status Wise', 'edit') || isSuperAdmin() || isAdmin())     
                              <a href="#" class="text-info open-modal" data-lead-id="{{$arr->leadID}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="info-tooltip" data-bs-title="Lead Edit" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-square-edit-outline'></i>
                              </a>
                              @endif
                           </td>
                           @endif
                           <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                           <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->addedOn)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th></th>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
            <div class="tab-pane @if ($activeTab == 'reloan-fresh') show active @endif" id="reloan-fresh">
               <div class="card-body" style="overflow-x:auto;">
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="15" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           <td>{{$serial++}}</td>
                            @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                              @endif
                              @if(rolesAccess('Status Wise', 'edit') || isSuperAdmin() || isAdmin())     
                              <a href="#" class="text-info open-modal" data-lead-id="{{$arr->leadID}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="info-tooltip" data-bs-title="Lead Edit" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-square-edit-outline'></i>
                              </a>
                              @endif
                           </td>
                           @endif
                          <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                           <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->addedOn)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
            <div class="tab-pane @if ($activeTab == 'callback') show active @endif" id="callback">
               <div class="card-body" style="overflow-x:auto;">
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="15" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           <td>{{$serial++}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                           </td>
                           @endif
                           <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                           <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->timelineDate)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
            <div class="tab-pane @if ($activeTab == 'no-answer') show active @endif" id="no-answer">
               <div class="card-body" style="overflow-x:auto;">
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="15" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           <td>{{$serial++}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                           </td>
                           @endif
                           <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                           <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->timelineDate)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
            <div class="tab-pane @if ($activeTab == 'interested') show active @endif" id="interested">
               <div class="card-body" style="overflow-x:auto;">
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="15" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           <td>{{$serial++}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                           </td>
                           @endif
                           <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                            <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->timelineDate)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
            <div class="tab-pane @if ($activeTab == 'not-eligible') show active @endif" id="not-eligible">
               <div class="card-body" style="overflow-x:auto;">
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="15" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           <td>{{$serial++}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                           </td>
                           @endif
                          <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                            <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->timelineDate)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
            <div class="tab-pane @if ($activeTab == 'incomplete-documents') show active @endif" id="incomplete-documents">
               <div class="card-body" style="overflow-x:auto;">
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="15" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           <td>{{$serial++}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                           </td>
                           @endif
                          <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                            <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->timelineDate)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
            <div class="tab-pane @if ($activeTab == 'less-salary') show active @endif" id="less-salary">
               <div class="card-body" style="overflow-x:auto;">
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="15" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           <td>{{$serial++}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                           </td>
                           @endif
                           <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                           <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->timelineDate)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
            <div class="tab-pane @if ($activeTab == 'document-received') show active @endif" id="document-received">
               <div class="card-body" style="overflow-x:auto;">
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="15" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           <td>{{$serial++}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                           </td>
                           @endif
                           <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                            <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->timelineDate)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
             <div class="tab-pane @if ($activeTab == 'rejected') show active @endif" id="rejected">
               <div class="card-body" style="overflow-x:auto;">
                  <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                        <tr style="font-size:14px;">
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </thead>
                     <tbody>
                        @php
                        $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                        @endphp
                        @if ($leads->isEmpty())
                        <tr>
                           <td colspan="14" class="text-center">No data found</td>
                        </tr>
                        @else
                        @foreach($leads as $key => $arr)
                        <tr style="font-size:15px;">
                           <td>{{$serial++}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td>
                              <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                              <i class='mdi mdi-eye'></i>
                              </a>
                           </td>
                           @endif
                           <td>{{$arr->leadID}}</a></td>
                           <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : '--')}}</td>
                           <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : '--')}}</td>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                           @endif
                           <td>{{$arr->email}}</td>
                           <td>{{$arr->mobile}}</td>
                           <td>{{$arr->pancard}}</td>
                           <td>{{nf($arr->monthlyIncome)}}</td>
                           <!--<td>{{$arr->city}}</td>-->
                           <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                           <td>{{$arr->customerType}}</td>
                           <td>{{$arr->utmSource}}</td>
                           <!-- DFT(date-format) is a custom helper -->
                           <td>{{dft($arr->timelineDate)}}</td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                     <tfoot>
                        <tr>
                           <th>#</th>
                           @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                           <th>Action</th>
                           @endif
                           <th>Lead ID</th>
                           <th>Assigned RM</th>
                           <th>Assigned CM</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Mobile</th>
                           <th>Pancard</th>
                           <th>Monthly Income</th>
                           <th>City</th>
                           <th>Employement Type</th>
                           <th>Utm Source</th>
                           <th>Date</th>
                        </tr>
                     </tfoot>
                  </table>
                  <div class="row">
                     {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                  </div>
               </div>
            </div>
         </div>
         <!-- end card-->
      </div>
      
      
      <!-- end col -->
   </div>
   <!-- end row -->
</div>
<!-- container -->
<div id="right-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-dialog-scrollable modal-right">
      <div class="modal-content">
         <div class="modal-header border-0 text-center">
            <h4 class="modal-title" style="color:#4743fa; margin: 0 auto;" id="primary-header-modalLabel">Lead Information Form</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form class="ps-1 pe-1" id="leadAddEditModalForm" action="#" autocomplete="off">
               @csrf
               <div class="row">
                   <div class="col-md-6">
                     <div class="mb-2">
                        <label for="state" class="form-label">Assigned CM</label>
                        <select class="form-control" name="cmID" id="cmID" @if(role() != 'CRM Support' && !isSuperAdmin() && !isAdmin() && role()!= 'Credit Manager' && role()!='Sr. Credit Manager') disabled @endif>
                           <option value="">Select Credit Manager</option>
                           @foreach($cmUsers as $arr)
                            <option value="{{$arr->userID}}">{{$arr->displayName}}</option>
                           @endforeach
                        </select>
                        <span class="cmIDErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-2">
                        <label for="rmID" class="form-label">Assigned RM</label>
                        <select class="form-control rmID" name="rmID" id="rmID" @if(role() != 'CRM Support' && !isSuperAdmin() && !isAdmin() && role()!= 'Credit Manager' && role()!='Sr. Credit Manager') disabled @endif>
                        @php $selectedUserID = Session::get('userID'); @endphp
                        <option value="" @if(!$selectedUserID) selected @endif>Select Relationship Manager</option>
                        @foreach($rmUsers as $arr)
                        <option value="{{ $arr->userID }}" 
                        @if($selectedUserID == $arr->userID) selected @endif>
                        {{ $arr->displayName }}
                        </option>
                        @endforeach
                        </select>
                        <span class="rmIDErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-1">
                        <label for="username" class="form-label">Pancard Number</label>
                       <input class="form-control" type="text" name="pancard" id="pancard" placeholder="Pancard Number" 
                        @if(role() != 'CRM Support' && !isSuperAdmin()) readonly @endif>
                        <span class="pancardErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                       <div class="mb-1">
                          <label for="adharNumber" class="form-label">Adhar Number</label>
                          <input class="form-control" type="text" name="adharNumber" id="adharNumber" placeholder="Adhar Number" 
                             @if(role() != 'CRM Support' && !isSuperAdmin()) readonly @endif>
                          <span class="adharNumberErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label for="nameOnPancard" class="form-label">Name on Pancard</label>
                          <input class="form-control" type="text" name="nameOnPancard" id="nameOnPancard" placeholder="Name on Pancard" 
                             @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                          <span class="nameOnPancardErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label for="email" class="form-label">Email</label>
                          <input class="form-control" type="email" name="email" id="email" placeholder="Email" 
                             @if(role() != 'CRM Support' && !isSuperAdmin() && !isAdmin()) disabled @endif>
                          <span class="emailErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label for="mobile" class="form-label">Mobile</label>
                          <input class="form-control" type="text" name="mobile" id="mobile" placeholder="Mobile" 
                              @if(role() != 'CRM Support' && !isSuperAdmin() && !isAdmin()) disabled @endif>
                          <span class="mobileErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label for="loanAmount" class="form-label">Loan Amount</label>
                          <input class="form-control" type="number" name="loanAmount" id="loanAmount" placeholder="Loan Amount" min="0" 
                             @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                          <span class="loanAmountErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label for="monthlySalary" class="form-label">Monthly Salary</label>
                          <input class="form-control" type="number" name="monthlySalary" id="monthlySalary" placeholder="Monthly Salary" min="0" 
                             @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                          <span class="monthlySalaryErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <div class="position-relative" id="datepicker4">
                             <label class="form-label">Date Of Birth</label>
                             <input type="text" class="form-control mb-1" data-provide="datepicker" data-date-autoclose="true" 
                                data-date-container="#datepicker4" placeholder="Date of Birth" name="dob" id="dob" 
                                @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                          </div>
                          <span class="dobErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label class="form-label">Gender</label><br>
                          <div class="form-check form-check-inline">
                             <input class="form-check-input" type="radio" name="gender" id="male" value="Male"
                                @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                             <label class="form-check-label" for="male">Male</label>
                          </div>
                          <div class="form-check form-check-inline">
                             <input class="form-check-input" type="radio" name="gender" id="female" value="Female"
                                @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                             <label class="form-check-label" for="female">Female</label>
                          </div>
                          <span class="genderErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label for="pincode" class="form-label">Pincode</label>
                          <input class="form-control" type="number" name="pincode" id="pincode" placeholder="Pincode" min="0" 
                             @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                          <span class="pincodeErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label for="state" class="form-label">State</label>
                          <select class="form-select" name="state" id="state"
                             @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                             <option value="">Select State</option>
                             @foreach($states as $arr)
                                <option value="{{$arr->stateID}}">{{$arr->stateName}}</option>
                             @endforeach
                          </select>
                          <span class="stateErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label for="cities" class="form-label">City</label>
                          <select class="form-select" name="city" id="cities"
                             @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                             <option value="">Select City</option>
                             <!-- Add more options for other cities as needed -->
                          </select>
                          <span class="cityErr"></span>
                       </div>
                    </div>
                    
                    <div class="col-md-6">
                       <div class="mb-1">
                          <label for="purpose" class="form-label">Purpose</label>
                          <select class="form-select" name="purpose" id="purpose"
                             @if(role() != 'CRM Support' && !isSuperAdmin()) disabled @endif>
                             <option value="">Select Purpose</option>
                             <option value="Household fund shortage">Household fund shortage</option>
                             <option value="Travel fund shortage">Travel fund shortage</option>
                             <option value="Meeting immediate commitment">Meeting immediate commitment</option>
                             <option value="Immediate purchase">Immediate purchase</option>
                             <option value="Loan to clear bills">Loan to clear bills</option>
                             <option value="Loan repayment">Loan repayment</option>
                             <option value="Loan for paying school fees">Loan for paying school fees</option>
                             <option value="Medical emegency">Medical emegency</option>
                             <option value="Buying gadgets">Buying gadgets</option>
                             <option value="Weddings expenses">Weddings expenses</option>
                             <option value="Home interiors">Home interiors</option>
                             <option value="Down-payment shortfall">Down-payment shortfall</option>
                             <option value="Personal">Personal</option>
                             <option value="wedding">Wedding</option>
                             <option value="Medical">Medical</option>
                             <option value="Travel">Travel</option>
                             <option value="Loan Payment">Loan Payment</option>
                             <option value="Bill Payment">Bill Payment</option>
                             <option value="Other">Other</option>
                          </select>
                          <span class="purposeErr"></span>
                       </div>
                    </div>
                  
               </div>
               <div class="mb-2 text-center">
                  <input type="hidden" name="leadID" id="leadID" value="">
                  <input type="hidden" name="contactID" id="contactID" value="">
                  <input type="submit" class="btn btn-primary form-control" value="Save">
               </div>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
<div id="success-header-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="success-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-success">
            <h4 class="modal-title text-white" id="success-header-modalLabel">Import Leads</h4>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form class="ps-1 pe-1" id="leadImportModalForm" method="POST" enctype="multipart/form-data">
               @csrf
               <div class="row">
                  <div class="col-md-12 text-center">
                     <a href="{{Storage::url('sampleFiles/Lead_Import_Sample_Sheet.xlsx')}}" class="btn btn-success btn-sm mb-2" download="">Download Sample</a>
                  </div>
                  <div class="col-md-12 mb-2 dropzone">
                     <input name="importLeadFile" type="file" id="file" style="visibility: hidden;"/>
                     <a href="javascript:void(0)" id="uploadIcon">
                        <div class="dz-message needsclick">
                           <i class="h1 text-muted ri-upload-cloud-2-line"></i>
                           <h5>Click to import excel file.</h5>
                        </div>
                     </a>
                  </div>
               </div>
         </div>
         <div class="modal-footer">
         <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
         <button type="button" class="btn btn-success" id="importButton">Import</button>
         </div>
         </form>
      </div>
   </div>
</div>
<div id="info-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div class="modal-body p-4">
            <div class="text-center">
               <i class="ri-alert-line h1 text-warning"></i>
               <h4 class="mt-2">Are You Sure Want To Delete ?</h4>
               <p class="mt-3"></p>
               <button type="button" class="btn btn-success my-2" id="confirmYes">Yes</button>
               <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
            </div>
         </div>
      </div>
   </div>
</div>
<div id="success-header-modal-assign" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="success-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-success">
            <h4 class="modal-title text-white" id="success-header-modalLabel">Leads Transfer</h4>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form class="ps-1 pe-1" id="leadTransferModalForm" method="POST">
               @csrf
               <div class="row">
                  <div class="col-md-6">
                     <div class="mb-2">
                        <label for="state" class="form-label">Assigned RM</label>
                        <select class="form-control rmID" name="rmTransferID">
                           <option value="">Select Relationship Manager</option>
                           @foreach($rmUsers as $arr)
                           <option value="{{$arr->userID}}">{{$arr->displayName}}</option>
                           @endforeach
                        </select>
                        <span class="rmTransferIDErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-2">
                        <label for="state" class="form-label">Assigned CM</label>
                        <select class="form-control cmID" name="cmTransferID">
                           <option value="">Select Credit Manager</option>
                           @foreach($cmUsers as $arr)
                           <option value="{{$arr->userID}}">{{$arr->displayName}}</option>
                           @endforeach
                        </select>
                        <span class="cmTransferIDErr"></span>
                     </div>
                  </div>
               </div>
         </div>
         <div class="modal-footer">
         <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
         <button type="button" class="btn btn-success" id="assignBtnForm">Assign</button>
         </div>
         </form>
      </div>
   </div>
</div>
@endsection

@section('custom-js')

<script type="text/javascript">

    $('#uploadIcon').click(function() {
        $('#file').click();
    });

    // Handle the import button click event
    $('#importButton').on('click', function(e) {
        e.preventDefault(); // Prevent the default form submission
        var formData = new FormData($('#leadImportModalForm')[0]);

        $.ajax({
            url: '{{route('importLeads')}}', // URL to send the request to
            type: 'POST',
            data: formData,
            contentType: false, // Let the browser set the content type
            processData: false, // Do not process data
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
            },
            success: function(data) {
                if (data.response == 'success') {
                    $('#success-header-modal').modal('hide');
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                    setTimeout(function() { window.location.reload(); }, 1000);
                }
            },
        });
    });

    $(document).ready(function() {
        // Manually activate the tab based on URL hash
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr("href"); // activated tab
            window.location.hash = target; // add tab id to browser URL
        });

        // Check if there's a hash in the URL and activate the corresponding tab
        if (window.location.hash) {
            $('a[data-bs-toggle="tab"][href="' + window.location.hash + '"]').tab('show');
        }
    });

    $('#checkAll').change(function() {
        $('.leadCheckbox').prop('checked', $(this).prop('checked'));
        toggleDeleteButton();
    });

    // Individual checkbox change
    $('.leadCheckbox').change(function() {
        toggleDeleteButton();
    });

    function toggleDeleteButton() {
        var anyChecked = false;
        $('.leadCheckbox').each(function() {
            if ($(this).prop('checked')) {
                anyChecked = true;
                return false; // Exit each loop early
            }
        });

        if (anyChecked) {
            $('.deleteButton').show();
            $('.bulkAssignButton').show();
        } else {
            $('.deleteButton').hide();
            $('.bulkAssignButton').hide();
        }
    }

    // Handle delete button click
    $('.deleteButton').click(function() {
        var leadIDs = [];
        $('.leadCheckbox:checked').each(function() {
            leadIDs.push($(this).val());
        });

        if (leadIDs.length === 0) {
            alert('Please select at least one lead to delete.');
            return;
        }

        $('#info-alert-modal').modal('show');

        $('#confirmYes').click(function() {
            $.ajax({
                url: "{{ route('leadsDelete') }}",
                type: 'POST',
                data: {
                    leadIDs: leadIDs,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.response == 'success') {
                        $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                        setTimeout(function() { window.location.reload(); }, 1000);
                    }
                },
            });

            // Hide modal after deletion
            $('#info-alert-modal').modal('hide');
        });
    });

    $('.bulkAssignButton').click(function() {
        var leadIDs = [];
        $('.leadCheckbox:checked').each(function() {
            leadIDs.push($(this).val());
        });

        if (leadIDs.length === 0) {
            $.NotificationApp.send("Oh snap!", 'Please select at least 1 lead', "bottom-right", "rgba(0,0,0,0.2)", "error");
            return;
        }

        $('#success-header-modal-assign').modal('show');

        $('#assignBtnForm').off('click').on('click', function() {
            var rmTransferID = $('.rmID').val();
            var cmTransferID = $('.cmID').val();

            $.ajax({
                url: "{{ route('leadsTransfer') }}",
                type: 'POST',
                data: {
                    rmTransferID: rmTransferID,
                    cmTransferID: cmTransferID,
                    leadIDs: JSON.stringify(leadIDs),
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.response === 'success') {
                        $('#success-header-modal-assign').modal('hide');
                        $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                        setTimeout(function() { window.location.reload(); }, 1000);
                    } else {
                        printErrorMsg(data.error);
                    }
                },
            });
        });
    });

    $('#addFreshLeadBtn').click(function() {
        $('#leadAddEditModalForm')[0].reset();
    });

    $('#importLeadButton').click(function() {
        $('#success-header-modal').modal('show');
    });

    // Date picker initialization or additional actions as needed
    $(document).ready(function() {
        // Function to update containers based on selected option
        function updateContainers(selectedOption) {
            // Hide all containers and disable inputs
            $('.searchBoxContainer, .dateRangeContainer, .customSortContainer, .exportAllContainer, .exportByDateContainer').hide();
            $('.searchInput, .singledaterange, .customSearchInput, .customDateRange, .exportDateRange').prop('disabled', true);

            // Show the corresponding container and enable relevant inputs based on selected option
            if (selectedOption === 'sortBySearch') {
                $('.searchBoxContainer').show();
                $('.searchInput').prop('disabled', false);
            } else if (selectedOption === 'sortByDate') {
                $('.dateRangeContainer').show();
                $('.singledaterange').prop('disabled', false);
            } else if (selectedOption === 'customSort') {
                $('.customSortContainer').show();
                $('.customSearchInput, .customDateRange').prop('disabled', false);
            } else if (selectedOption === 'exportAll') {
                $('.exportAllContainer').show();
                // No specific input to enable/disable
            } else if (selectedOption === 'exportByDate') {
                $('.exportByDateContainer').show();
                $('.exportDateRange').prop('disabled', false);
            } else if (selectedOption === 'sortByToday' || selectedOption === 'sortByWeek' || selectedOption === 'sortByThisMonth' || selectedOption === 'sortByLastMonth') {
                // Only show the search button in searchBoxContainer for these options
                $('.searchBoxContainer').show();
                $('#searchButton').show();
            } else {
                // Default behavior if no valid option selected or if selectedOption is blank
                selectedOption = 'sortBySearch'; // Default to 'sortBySearch'
                $('.searchBoxContainer').show();
                $('.searchInput').prop('disabled', false);
            }

            // Set the selected option in the dropdown
            $('.exportSelect').val(selectedOption);
        }

        // Initialize with default option 'sortBySearch'
        @php
            $selectedOption = !empty($filter) ? $filter : 'sortBySearch';
        @endphp
        var selectedOption = '{{ $selectedOption }}';
        updateContainers(selectedOption);

        // Handle change event on the select dropdown
        $('.exportSelect').change(function() {
            var selectedOption = $(this).val();
            updateContainers(selectedOption);
        });
    });

    $(document).ready(function() {
        $('#state').change(function() {
            var stateID = $(this).val();
            if (stateID) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('fetchCities') }}",
                    dataType: 'json',
                    data: { stateID: stateID },
                    success: function(res) {
                        if (res && res.length > 0) { // Check if response is not empty
                            $("#cities").empty();
                            $.each(res, function(key, value) {
                                $("#cities").append('<option value="' + value.cityID + '">' + value.cityName + '</option>'); // Fix the capitalization for cityName
                            });
                        } else {
                            $("#cities").empty().append('<option value="">No Cities Found</option>'); // Provide feedback for empty response
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Log any errors to console for debugging
                    }
                });
            } else {
                $("#cities").empty().append('<option value="">Select State First</option>'); // Provide feedback for no state selected
            }
        });
    });

    $(document).ready(function() {
        // Click event handler for the link
        $('.open-modal').click(function(event) {
            event.preventDefault(); // Prevent default link behavior

            var leadID = $(this).data('lead-id'); // Get the leadID from data attribute

            // Make AJAX request to fetch data using leadID
            $.ajax({
                url: "{{ route('freshLeadEdit') }}", // Replace with your route URL
                type: 'GET',
                data: { leadID: leadID },
                success: function(response) {
                    // Populate modal with fetched data
                    $('#nameOnPancard').val(response.name);
                    $('#email').val(response.email);
                    $('#mobile').val(response.mobile);
                    $('#pancard').val(response.pancard);
                    $('#adharNumber').val(response.aadharNo);
                    $('#loanAmount').val(response.loanRequired);
                    $('#monthlySalary').val(response.monthlyIncome);
                    var parts = response.dob.split('-'); // Split by '-'
                    var formattedDate = parts[1] + '/' + parts[2] + '/' + parts[0]; // Format as "m/d/y"

                    $('#dob').val(formattedDate);
                    $('input[name="gender"][value="' + response.gender + '"]').prop('checked', true);
                    $('input[name="employmentType"][value="' + response.customerType + '"]').prop('checked', true);
                    $('#state').val(response.state).trigger('change');
                    $('#cities').val(response.city).trigger('change');
                    $('#pincode').val(response.pincode);
                    $('#purpose').val(response.purpose);
                    $('#rmID').val(response.rmID);
                    $('#cmID').val(response.cmID);
                   // fetchRelationshipManagers(response.cmID,response.rmID);
                    $('#leadID').val(response.leadID);
                    $('#contactID').val(response.contactID);

                    // Open the modal
                    $('#right-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(error);
                }
            });
        });
    });

    $(document).ready(function() {
        // Get the selected RM ID on page load
        // var cmID = $('#cmID').val();
        // fetchRelationshipManagers(cmID);

        // Add change event listener
        $(document).on('change', '#cmID, .cmID', function() {
            var cmID = $(this).val();
            fetchRelationshipManagers(cmID);
        });
    });

    $(document).on('submit', '#leadAddEditModalForm', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var leadID = $('#leadID').val();

        $.ajax({
            url: "{{ route('leadAdd') }}",
            type: "POST",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: formData,
            success: function(data) {
                if (data.response == 'success') {
                    $('.errClr').html('').hide();
                    $('#leadAddEditModalForm').trigger('reset');
                    $('#right-modal').modal('hide');
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                    setTimeout(function() { window.location.reload(); }, 1000);
                } else {
                    printErrorMsg(data.error);
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    });

    function fetchRelationshipManagers(cmID,rmID = '') {
        // $('#rmID').empty().append('<option value="">Select Relationship Manager</option>'); // Clear the cmID dropdown
        // $('.rmID').empty().append('<option value="">Select Relationship Manager</option>'); // Clear the cmID dropdown
       
        if (cmID) {
            $.ajax({
                url: '{{ route('fetchRelationshipManagers') }}', // Define your route here
                type: 'GET',
                data: { cmID: cmID }, // Pass the rmID
                success: function(data) {
                    if (data) {
                    // Check if newAssignedRM is null or empty, and if so, use oldAssignedRM
                    var rmValue = (data.newAssignedRM && data.newAssignedRM.trim() !== '') ? data.newAssignedRM : data.oldAssignedRM;
                    
                    // Set the value of the dropdown
                    $('.rmID').val(rmValue);
                 }  else {
                        // $('#rmID').append('<option value="">No Relationship Managers Found</option>');
                        // $('.rmID').append('<option value="">No Relationship Managers Found</option>');
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        }
    }

    function printErrorMsg(msg) {
        $('.errClr').remove();
        $.each(msg, function(key, value) {
            $('.' + key + 'Err').html('<div class="text-danger errClr mt-1" style="font-size:12px;"><i class="ri-close-circle-line me-1 align-left font-12"></i><strong>' + value + '</strong></div>');
        });
    }

</script>

@endsection