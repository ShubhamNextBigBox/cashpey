<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index(Request $req){
 

        if($req->isMethod('post')){   
                $validatedData = $req->validate([
                'userName' => 'required|min:3',
                'password' => 'required|min:5',
                ]);
                $user = $req->only('userName','password');
                $user['status'] = 1;
                if(Auth::attempt($user)){
                    if (Auth::check()) {                        
                        $user = Auth::user();
                        Session::put('name',$user->displayName);
                        Session::put('userID',$user->userID);
                        Session::put('role',$user->role);
                        $data = [
                            'userID' => $user->userID,
                            'loginTime' => dt(),
                            'ip' => $req->ip(),
                            'loginDate' => dt(),
                            'addedOn' => dt(),
                            ];
                        DB::table('lms_login_logs')->insert($data); 
                        
                        return redirect('dashboard')->with('LoginSuccess','You have successfully logged in.');

                    //   $checkExists =  DB::table('lms_attendance')->where(['userID'=>Session::get('userID'),'attendanceDate'=>date('Y-m-d')])->first(); 
                       
                    //   if($checkExists){
                    //     return redirect('dashboard')->with('LoginSuccess','You have successfully logged in.');
                    //   }else{
                    //      return redirect('punch');
                    //   }  
                         
                    }
                }else{
                    return redirect()->back()->with('LoginError',"The credentials you've entered is invalid.");
                }

            
         }
         if($req->isMethod('get')){   
             return view('auth.login');
        }  
    }


    public function punch(){
        return view('auth.punch');
    }

    public function punchInOut(Request $request){
        
              $apiKey = 'AIzaSyD3LqaRVIfmV8K9ye579ENlY_98vj3S52I';

           
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, 'https://maps.google.com/maps/api/geocode/json?latlng=' . trim($request->latitude) . ',' . trim($request->longitude) . '&key=' . $apiKey . '');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              $response = curl_exec($ch);
              curl_close($ch); 
        
              $data = json_decode($response, true);

              $address = $data['results'][0]['formatted_address'];
              $city = $data['results'][0]['address_components'][4]['long_name'];
              $punchType = $request->punchType;

              $checkExists =  DB::table('lms_attendance')->where(['userID'=>Session::get('userID'),'attendanceDate'=>date('Y-m-d')])->first(); 
               
                if ($punchType == 'punchIN') {
                        $dataAttendance = array(
                            'userID' => Session::get('userID'),
                            'attendanceDate' => date('Y-m-d'),
                            'signIN' => date('h:i a'),
                            'signInAddress' => $address,
                            'city' => $city,
                            'updatedOn' => date('Y-m-d h:i:s a'),
                            'addedOn' => date('Y-m-d h:i:s a')
                        );
                        $dataLogAttendance = array(
                            'userID' =>Session::get('userID'),
                            'attendanceDate' =>date('Y-m-d'),
                            'time' => date('h:i a'),
                            'address' => $address,
                            'city' => $city,
                            'punchType' => $request->punchType,
                            'updatedOn' => date('Y-m-d h:i:s a'),
                            'addedOn' => date('Y-m-d h:i:s a')
                        );

                      if($checkExists){ 
                        DB::table('lms_attendance_log')->insert($dataLogAttendance);
                      }else{
                        Session::put('punchInTime',date('h:i:s')); 
                        DB::table('lms_attendance')->insert($dataAttendance);
                        DB::table('lms_attendance_log')->insert($dataLogAttendance);
                      }
                      return response()->json(['response' => 'success']);
 
                
                } elseif ($punchType == 'punchOut') {
                    $dataAttendance = array(
                        'signOut' => date('h:i a'),
                        'signOutAddress' => $address,
                        'city' => $city,
                        'updatedOn' => date('Y-m-d h:i:s a'),
                    );
                    $dataLogAttendance = array(
                        'userID' =>Session::get('userID'),
                        'attendanceDate' =>date('Y-m-d'),
                        'time' => date('h:i a'),
                        'address' => $address,
                        'city' => $city,
                        'punchType' => $request->punchType,
                        'updatedOn' => date('Y-m-d h:i:s a'),
                        'addedOn' => date('Y-m-d h:i:s a')
                    );
                    DB::table('lms_attendance')->where(['userID'=>Session::get('userID'),'attendanceDate'=>date('Y-m-d')])->update($dataAttendance); 
                    DB::table('lms_attendance_log')->insert($dataLogAttendance);

                    return response()->json(['response' => 'success']);

                }    
    }   



   public function logout() {
    if (Session::has('userID')) {
        // Update the logout time in login logs
        DB::table('lms_login_logs')
            ->where('userID', Session::get('userID'))
            ->orderBy('id', 'desc')
            ->take(1)
            ->update(['logoutTime' => dt(), 'updatedOn' => dt()]);

        // Update sign-out time in attendance
        DB::table('lms_attendance')
            ->where(['userID' => Session::get('userID'), 'attendanceDate' => date('Y-m-d')])
            ->update(['signOut' => date('h:i a')]);

        // Clear the session
        Session::flush();

        // Redirect with success message
        return redirect('/')->with('success', 'You have successfully logged out.');
    }

    // Optionally, redirect to a different page or show an error message if not logged in
    return redirect('/')->with('error', 'You are not logged in.');
}

}
