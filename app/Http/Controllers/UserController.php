<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use DB;
use Mail;
class UserController extends Controller
{
	Public function testCall()
    {
		return response()->json('Salam');
	}
	//Method to register the User
   Public function userRegister(Request $request)
    {
        $FULLNAME = $request->get("FULLNAME");
        $USERNAME = $request->get("USERNAME");
        $EMAIL = $request->get("EMAIL");
        $MOBILENO = $request->get("MOBILENO");
        $PASSWRD = $request->get("PASSWRD");
        $VERIFIED = $request->get("VERIFIED");
        $REGDATE = time();

        $check = DB::table('member')->where('EMAIL', '=', $EMAIL)->sharedLock()->get();
        if ($check->isEmpty())
        {
        DB::table('member')->insert(
            ['FULLNAME' => $FULLNAME, 
            'USERNAME' => $USERNAME,
            'EMAIL' => $EMAIL,
            'MOBILENO' => $MOBILENO,
            'PASSWRD' => password_hash($PASSWRD,PASSWORD_DEFAULT),
            'VERIFIED' => 0,
            'REGDATE' => $REGDATE]
        );}
        else {return 0;}
    }
		//Method to send verification Email when user request password change.
        public function basicRequestemail(Request $request) {
            $EMAIL=$request->get("EMAIL");
            $CODE=$request->get("CODE");
      
     $Get_Data = DB::table('member')->where('EMAIL', '=', $EMAIL)->sharedLock()->get();
     if (!$Get_Data->isEmpty())
      {Mail::send([], [], function ($message) use ($CODE,$EMAIL) {
      $message->to($EMAIL)
         ->from('WardrobeRentalApp@gmail.com','Wardrobe Rental')
        ->subject('Wardrobe Rental App (Forget Password Service)')
        ->setBody("<h2>Forget Password</h2><p> <strong>Code</strong> : $CODE  <br><br>  <strong>Please use that code</strong></p>", 'text/html'); // for HTML
		});
		return "true";
		}
		else {return "false";}
       }

		// Gets the credentials and process the login request
       public function userLogin(Request $request)
       {

        $EMAIL = $request->get("EMAIL");
        $PASSWD = $request->get("PASSWORD");
        $Get_Data = DB::table('member')->where('EMAIL', '=', $EMAIL)->sharedLock()->get();
        if (!$Get_Data->isEmpty())
        {
            $tpass = $Get_Data[0]->PASSWRD;
            if (password_verify($PASSWD, $tpass)) 
            {
                return "true";
            }
            else {return "false";}

        }
        else {
            return "null";
        }

       }
		// Updates the user password
       public function updatePassword(Request $request )
       {
        $EMAIL = $request->get("EMAIL");
        $PASSWD = $request->get("PASSWORD");
       $var = DB::table('member')
        ->where('EMAIL', $EMAIL)
        ->update(['PASSWRD' =>  password_hash($PASSWD,PASSWORD_DEFAULT)]);
        return $var;
       }

		// This method updates the user data
       Public function updateUser(Request $request)
       {
           $FULLNAME = $request->get("FULLNAME");
           $USERNAME = $request->get("USERNAME");
           $EMAIL = $request->get("EMAIL");
           $MOBILENO = $request->get("MOBILENO");
           $PASSWRD = $request->get("PASSWRD");
   
           $check = DB::table('member')->where('EMAIL', '=', $EMAIL)->sharedLock()->get();
           if (!$check->isEmpty())
           {
           DB::table('member')
           ->where('EMAIL', $EMAIL)
           ->update(
               ['FULLNAME' => $FULLNAME, 
               'USERNAME' => $USERNAME,
               'EMAIL' => $EMAIL,
               'MOBILENO' => $MOBILENO,
               'PASSWRD' => password_hash($PASSWRD,PASSWORD_DEFAULT),
               ]
           );}
           else {return 0;}
       }
		
		//This method to get the user data
       Public function getUserData($EMAIL)
       {
        $Data = DB::table('member')->where('EMAIL', '=', $EMAIL)->sharedLock()->get();
        if (!$Data->isEmpty())
           { return $Data; }
           else {return "false";}
       }
		
		//This method adds the advertisment to the database.
       Public function addAdvertisement(Request $request)
       {
        //Code to put image in the storage 
        $imgBase64 = $request->get("imgbase64");
         $data = base64_decode($imgBase64);
         $image_name = "/app/public/uploads/imgs/". 'post_' . time() . '.png';
         $path = storage_path() . $image_name;
         file_put_contents($path, $data);

         //Database work to put data in table
        $M_ID = $request->get("M_ID");
        $TYPE = $request->get("TYPE");
        $SIZE = $request->get("SIZE");
        $LENGTH = $request->get("LENGTH");
        $SHOULDERS = $request->get("SHOULDERS");
        $WAIST = $request->get("WAIST");
        $NECK = $request->get("NECK");
        $ARMS = $request->get("ARMS");
        $AVAILABILITY = $request->get("AVAILABILITY");
        $TIMESTAMP = time();
        $BUY_DATE = $request->get("BUY_DATE");
        $BUY_PRICE = $request->get("SHOULDERS");
        $EXP_PRICE = $request->get("EXP_PRICE");
        $RENT_PRICE = $request->get("RENT_PRICE");
        $RENT_TILL = $request->get("RENT_TILL");

        DB::table('dressinfo')->insert(
            ['M_ID' => $M_ID, 
            'TYPE' => $TYPE,
            'SIZE' => $SIZE,
            'LENGTH' => $LENGTH,
            'SHOULDERS' => $SHOULDERS,
            'WAIST' => $WAIST,
            'NECK' => $NECK,
            'ARMS' => $ARMS,
            'AVAILABILITY' => $AVAILABILITY,
            'TIMESTAMP' => $TIMESTAMP,
            'TILL' => $RENT_TILL,
            'IMGNAME' => 'http://192.168.8.101/Wardrobe/storage'.$image_name,
            ]);
            $var = DB::table('dressinfo')->where('D_ID', DB::raw("(select max(`D_ID`) from dressinfo)"))->get();
            DB::table('pricing')->insert(
                ['D_ID' => $var[0]->D_ID, 
                'BUY_DATE' => $BUY_DATE,
                'BUY_PRICE' => $BUY_PRICE,
                'EXP_PRICE' => $EXP_PRICE,
                'RENT_PRICE' => $RENT_PRICE,
                ]);
       }
		// This method to get the dress info.
       public function getDressInfo()
       {
        $Data = Response::json( DB::select( DB::raw("SELECT d.D_ID, d.M_ID, d.TYPE, d.SIZE, d.LENGTH, d.SHOULDERS, d.WAIST, d.NECK, d.ARMS, d.AVAILABILITY, DATE_FORMAT(FROM_UNIXTIME(d.TIMESTAMP),'%d/%c/%Y') as TIMESTAMP, d.IMGNAME,DATE_FORMAT(FROM_UNIXTIME(d.TILL),'%d/%c/%Y') as TILL,
        DATE_FORMAT(FROM_UNIXTIME(p.BUY_DATE),'%d/%c/%Y') as BUY_DATE, p.BUY_PRICE, p.EXP_PRICE, p.RENT_PRICE FROM `dressinfo` as d JOIN `pricing` as p WHERE d.D_ID = p.D_ID ")));
        return $Data;
       }
		
		//This method is to get the complete detail of dress.
		Public function getDressData($ID)
       {    $Data = DB::table('dressinfo')
            ->join('pricing', 'dressinfo.D_ID', '=', 'pricing.D_ID')
            ->select('dressinfo.*', 'pricing.*')
			->where('dressinfo.D_ID', '=', $ID)
            ->get();
		    if (!$Data->isEmpty())
            { return $Data; }
            else {return "false";}
       }
	   
	   //This will get the Member ID for verification of same user advertisment.
	   public function getAdVer($EMAIL)
	   {
		    $Data = Response::json( DB::select( DB::raw("SELECT `M_ID` FROM `member` WHERE `EMAIL` = '$EMAIL'")));
			return $Data;
	   }
		
		public function addRequest(Request $request)
		{
			   $M_ID = $request->get("M_ID");
               $D_ID = $request->get("D_ID");
			   $DATE_FROM = $request->get("DATE_FROM");
               $DATE_TO = $request->get("DATE_TO");
			   $STATUS = $request->get("STATUS");
               $STREET1 = $request->get("STREET1");
			   $STREET2 = $request->get("STREET2");
               $STATE = $request->get("STATE");
			   $CITY = $request->get("CITY");
               $POSTALCODE = $request->get("POSTALCODE");
			    $Check_Dup_Ent = DB::table('rent')->where('M_ID', '=', $M_ID)->get();
				
				if ($Check_Dup_Ent->isEmpty()){
				 DB::table('rent')->insert(
                 ['M_ID' => $M_ID, 
                 'D_ID' => $D_ID,
                 'DATE_FROM' => $DATE_FROM,
                 'DATE_TO' => $DATE_TO,
                 'STATUS' => $STATUS,
                 ]);
				
				}
			
			else
				{
			   DB::table('rent')
			  ->where([['M_ID', '=', $M_ID],['D_ID', '=', $D_ID]])
			  ->update(
                 [
                 'DATE_FROM' => $DATE_FROM,
                 'DATE_TO' => $DATE_TO,
                 'STATUS' => $STATUS,
                 ]);
			    }
				
				//Address Tab Work
				$Check_Dup_Add = DB::table('address')->where('M_ID', '=', $M_ID)->get();
				if ($Check_Dup_Add->isEmpty()){
					
				 DB::table('address')->insert(
                 ['M_ID' => $M_ID, 
                 'STREET1' => $STREET1,
                 'STREET2' => $STREET2,
                 'STATE' => $STATE,
                 'CITY' => $CITY,
                 'POSTALCODE' => $POSTALCODE,
                 ]);
				}
				else
				{
			   DB::table('address')
			  ->where('M_ID', '=', $M_ID)
			  ->update(
                 [ 
                 'STREET1' => $STREET1,
                 'STREET2' => $STREET2,
                 'STATE' => $STATE,
                 'CITY' => $CITY,
                 'POSTALCODE' => $POSTALCODE,
                 ]);
					
			
				}
				
       }
               
       public function uploadPic(Request $request)
       {
       $imgBase64 = $request->get("imgbase64");
       
       $data = base64_decode($imgBase64);
      // dd($data);
       //now you will save the image to your upload folder, here you can media library of choice or use storage function,
      //but here i will be using normal public_path.
      $image_name = "/app/public/uploads/imgs/". 'post_' . time() . '.png';
      //you can save it with any nae of choice or any extension of choice. or you may wish to leave it as the default 
      //extension depends on how you want to save the image.
      $path = storage_path() . $image_name;
      file_put_contents($path, $data);
        

       }
			   
			
			
		

}
