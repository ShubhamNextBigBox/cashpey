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
<div class="card">
   <div class="card-body">
    <div id="menu-container">
        @foreach ($combinedMenus as $menu)
            <div class="menu-item" data-id="{{ $menu['id'] }}" data-parent-id="NULL" id="menu-{{ $menu['id'] }}">
                <h5 class="menu-title">
                    <i class="uil uil-apps"></i> <!-- Optional: You can use any icon here -->
                    {{ $menu['name'] }}
                </h5>
                <!-- Submenus (if any) -->
                @if (!empty($menu['subMenus']))
                    <div class="submenu-container">
                        <span class="submenu-title">Sub-Module:</span>
                        <div class="submenu-items" id="submenu-{{ $menu['id'] }}">
                            @foreach ($menu['subMenus'] as $key => $subMenu)
                                <div class="submenu-item" data-id="{{ $subMenu['id'] }}" data-parent-id="{{ $menu['id'] }}">
                                    <span class="submenu-index">{{ ++$key }}.</span> 
                                    <span class="submenu-name">{{ $subMenu['name'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

</div>
@endsection

@section('custom-js')
  <script>
   $(document).ready(function() {
    // Enable sorting for parent menu items
    $("#menu-container").sortable({
        items: ".menu-item",
        placeholder: "ui-state-highlight",  // Add a placeholder when dragging
        update: function(event, ui) {
            // Logic for when the order of parent menu items changes
            var newOrder = $(this).sortable("toArray", { attribute: "data-id" });
            updateMenuOrder(newOrder);
        }
    });

    // Enable sorting for submenu items within each parent
    $(".submenu-items").sortable({
        items: ".submenu-item",
        placeholder: "ui-state-highlight",  // Add a placeholder when dragging
        update: function(event, ui) {
            var newOrder = $(this).sortable("toArray", { attribute: "data-id" });
            updateSubMenuOrder(newOrder);
        }
    });

    // Function to update menu order via AJAX
    function updateMenuOrder(newOrder) {
        $.ajax({
            url: '{{route('updateMenuOrder')}}',  // Adjust the URL for your backend route
            method: 'POST',
            data: {
                menuOrder: newOrder,
                _token: "{{ csrf_token() }}"  // Add CSRF token for security
            },
            success: function(response) {
                $.NotificationApp.send("Well Done!",'Module position updated', "bottom-right", "rgba(0,0,0,0.2)", "success");
                setTimeout(function() { window.location.reload(); }, 1000);
            },
            error: function(xhr, status, error) {
                console.error('Error updating menu order:', error);
            }
        });
    }

    // Function to update submenu order via AJAX
    function updateSubMenuOrder(newOrder) {
        $.ajax({
            url: '{{route('updateSubMenuOrder')}}',  // Adjust the URL for your backend route
            method: 'POST',
            data: {
                subMenuOrder: newOrder,
                _token: "{{ csrf_token() }}"  // Add CSRF token for security
            },
            success: function(response) {
                $.NotificationApp.send("Well Done!",'Sub-Module position updated', "bottom-right", "rgba(0,0,0,0.2)", "success");
                setTimeout(function() { window.location.reload(); }, 1000);
            },
            error: function(xhr, status, error) {
                console.error('Error updating submenu order:', error);
            }
        });
    }

});
</script>

@endsection