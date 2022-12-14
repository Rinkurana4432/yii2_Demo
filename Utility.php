<?php
namespace app\components;
use Yii;
use yii\base\Component;
use yii\web\Controller;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\web\Session;
use yii\db\mssql\PDO;
use yii\base\Security;
class Utility extends Component {

  static function getupperstring($string) 
  {
      return $string = ucwords(strtolower($string));
  }

  public function get_menus_new($param_leftmenuid, $param_topmenuid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_menus_new`(:param_leftmenuid, :param_topmenuid, :param_employee_code, :param_role_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_leftmenuid', $param_leftmenuid);
    $command->bindValue(':param_topmenuid', $param_topmenuid);
    $command->bindValue(':param_employee_code', Yii::$app->user->identity->e_id);
    $command->bindValue(':param_role_id', Yii::$app->user->identity->role);
    if(!empty($param_leftmenuid) ){
    $result=$command->queryOne();
    }else{
    $result=$command->queryAll();
    }

    $connection->close();
    return $result;       
  }


  public function get_co_response($CURR_DATE_YEAR){

    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT erg.*,e.employee_code,e.fname,e.lname FROM `erss_response_log` erg
    LEFT JOIN employee e ON (e.employee_code=erg.agentName) 
    WHERE erg.CURR_DATE_YEAR='".$CURR_DATE_YEAR."' AND e.employee_code IS NOT NULL;
    ";

    //die($sql);
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result; 
  }
    
  public function get_co_mini_response($CURR_DATE_YEAR){

    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT erg.*,e.employee_code,e.fname,e.lname FROM `erss_response_log` erg
    LEFT JOIN employee e ON (e.employee_code=erg.agentName)
    WHERE erg.CURR_DATE_YEAR='".$CURR_DATE_YEAR."' AND e.employee_code IS NOT NULL;
    ";    
    //die($sql);
    $command = $connection->createCommand($sql);
    $result=$command->queryOne();
    $connection->close();
    return $result; 
  }

  public function view_co_feeback($emp_code,$month_val){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT rco.*,e.fname,e.lname FROM `reward_cofeedback` rco 
    LEFT JOIN employee e ON (e.employee_code=rco.employee_code)
    WHERE rco.`employee_code` LIKE '".$emp_code."' AND rco.`Month`='".$month_val."'

    ";
    $command = $connection->createCommand($sql);
    $result=$command->queryOne();
    $connection->close();
    return $result; 
  }
  
  public function checkprevious_co_feeback($month_val,$emp_code){

    $connection=   Yii::$app->db;
    $connection->open();
    $sql="SELECT count(*) as ttl FROM `reward_cofeedback` WHERE `employee_code`='".$emp_code."' AND Month='".$month_val."'";
    $command = $connection->createCommand($sql);
    $result=$command->queryOne();
    $connection->close();
    return $result['ttl']; 
  
  }

  public function add_co_feeback($arr,$month_val,$created_by,$emp_code){
    $connection=   Yii::$app->db;
    $connection->open();

    $sum = (int)$arr['resonse_time_wt']+$arr['actionable_call']+$arr['exceptional_achiv']+$arr['decipline']+$arr['accuracy_of_info']+$arr['rating_in_feedback'];

    $sql ="
    INSERT INTO `reward_cofeedback` (`employee_code`,`resonse_time_wt`, `actionable_call`, `exceptional_achiv`, `decipline`, `accuracy_of_info`, `rating_in_feedback`, `Month`,`sum_total`,`created_by`) 
    VALUES ('".$emp_code."',".$arr['resonse_time_wt'].",".$arr['actionable_call'].",".$arr['exceptional_achiv'].",".$arr['decipline'].",".$arr['accuracy_of_info'].",".$arr['rating_in_feedback'].",'".$month_val."',".$sum.",'".$created_by."');
    ";
    //die($sql);
    $command = $connection->createCommand($sql); 
    $ret = $command->execute();
    $connection->close();
    return $ret;
  }

 public function update_co_feeback($arr,$month_val,$created_by,$emp_code){
    $connection=   Yii::$app->db;
    $connection->open();
    $sum = (int)$arr['resonse_time_wt']+$arr['actionable_call']+$arr['exceptional_achiv']+$arr['decipline']+$arr['accuracy_of_info']+$arr['rating_in_feedback'];    
    $sql ="
    UPDATE `reward_cofeedback` SET
    
    `resonse_time_wt`=".$arr['resonse_time_wt'].",
    `actionable_call`=".$arr['actionable_call'].",
    `exceptional_achiv`=".$arr['exceptional_achiv'].",
    `decipline`=".$arr['decipline'].",
    `accuracy_of_info`=".$arr['accuracy_of_info'].",
    `rating_in_feedback`=".$arr['rating_in_feedback'].",
    `sum_total`=".$sum.",   
    `created_by`='".$created_by."'
     WHERE `employee_code`='".$emp_code."' AND  `Month`='".$month_val."';
    ";
    
    $command = $connection->createCommand($sql); 
    $ret = $command->execute();
    $connection->close();
    return $ret;
  }

  public function get_co_response_data($employee_code,$month_val){

    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="
    SELECT erl.*,e.fname,e.lname FROM `erss_response_log` erl
    LEFT JOIN employee e ON (erl.agentName=e.employee_code)
    WHERE `CURR_DATE_YEAR` LIKE '".$month_val."' AND `agentName` = '".$employee_code."'
    ";

  
    $command = $connection->createCommand($sql);
    $result=$command->queryOne();
    $connection->close();
    return $result; 
  }


  public function get_co_best_performer($month_val){

    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="
            SELECT * FROM `reward_cofeedback` WHERE `Month`='".$month_val."' ORDER By sum_total DESC LIMIT 0,3
    ";

  
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result; 
  }


	
	public function getTopMenus(){
		$all = Yii::$app->utility->get_menus_new(NULL, NULL);
               // echo "<pre>";print_r($all);die;
		$menulist = "";
		$i=0;
		$parent1=[];
		$parent=[];
		
		foreach($all as $a){
			$parent1[]=$a['parent'];
			$parent[$i]['parent']=$a['parent'];
			$parent[$i]['menu_url']=$all[0]['menu_url'];
			$i++;
		}
		$uniqueParent = array();
		$i=0;
		$parent1 = array_unique($parent1);
		
		$top = "";
		foreach($parent1 as $p){
			$top .= "$p,";
		}
		$top = rtrim($top,",");
        $topmenus = Yii::$app->utility->get_menus_new(NULL, $top);
		return $topmenus;
		// 
	}

	
	
    public function get_left_menu($param_menuid){
		$connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_left_menu`(:param_menuid, :param_role_id,:param_employee_code)";
		$command = $connection->createCommand($sql); 
        $command->bindValue(':param_menuid', $param_menuid);
        $command->bindValue(':param_role_id', Yii::$app->user->identity->role);
        $command->bindValue(':param_employee_code', Yii::$app->user->identity->e_id);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }


        public function update_service_details($allData){

       // echo "<pre>"; print_r($allData); die('yyyyyyyyyy=====');
 $employee_code = $allData->employee_code;
       $dept_id = $allData->dept_id;
       $designation_id = $allData->designation_id;
       $authority1 = $allData->authority1;
       $authority2 = $allData->authority2;
       $updated_by = Yii::$app->user->identity->e_id;
      $joining_date=$allData->joining_date;

             $probation_from = $allData->probation_from;
             $probation_to = $allData->probation_to;
             $serc_joningdate = $allData->serc_joningdate;
             $serc_departuredate = $allData->serc_departuredate;
             $confirmation_date = $allData->confirmation_date;

       $connection=   Yii::$app->db;
       $connection->open();
       $sql =" CALL `update_service_details_by_emp`(:param_employee_code,:param_dept_id,:param_designation_id,:param_authority1,:param_authority2,:param_updated_by,:param_joining_date,:param_probation_from,:param_probation_to,:param_serc_joningdate,:param_serc_departuredate,:param_confirmation_date, @Result)";
       $command = $connection->createCommand($sql);
       $command->bindValue(':param_employee_code', $employee_code);
       $command->bindValue(':param_dept_id', $dept_id);
       $command->bindValue(':param_designation_id', $designation_id);
       $command->bindValue(':param_authority1', $authority1);
       $command->bindValue(':param_authority2', $authority2);
       $command->bindValue(':param_updated_by', $updated_by);
        $command->bindValue(':param_joining_date', $joining_date);
        $command->bindValue(':param_probation_from', $probation_from);
        $command->bindValue(':param_probation_to', $probation_to);
        $command->bindValue(':param_serc_joningdate', $serc_joningdate);
        $command->bindValue(':param_serc_departuredate', $serc_departuredate);
        $command->bindValue(':param_confirmation_date', $confirmation_date);
       
       $result = $command->execute();
       $connection->close();
       return $result;          
    }

    public function web_app_get_employee_details(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `web_app_get_employee_details`()";
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }


    public function web_act_get_employee_details(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM `act_hp_employee` ORDER BY `sr_no`  DESC";
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }



    public function get_employee_leaves($param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_get_employee_leaves`(:param_employee_code)";
		$command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }

    public function hr_assign_unassign_role($param_employee_code,$param_role_id,$param_assign)
    {
        $connection=   Yii::$app->db;
        $connection->open();
     
        $sql ="CALL `hr_assign_unassign_roles`(:param_employee_code, :param_role_id, :param_assign, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        $command->bindValue(':param_role_id', $param_role_id);
        $command->bindValue(':param_assign', $param_assign);
 
        $result = $command->execute();

        $connection->close();     
        
        return $result;       
    }
   
   
    public function get_employee_roles_list($param_employee_code)
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_get_employee_roles`(:param_employee_code, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
    
    public function validate_url($param_menuid){
        if (\Yii::$app->user->isGuest) {
            return false;
        }
		
        if(isset(Yii::$app->user->identity) AND !empty(Yii::$app->user->identity)){
            if(empty(Yii::$app->user->identity->e_id)){
                return false;
            }
        }
		
        $empcode=Yii::$app->user->identity->e_id;
        $role=Yii::$app->user->identity->role;
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="CALL `validate_url`(:param_menuid, :param_employee_code, :param_role_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_menuid', $param_menuid); 
        $command->bindValue(':param_employee_code', $empcode); 
        $command->bindValue(':param_role_id', $role); 
        $result=$command->queryOne();
        $result1 = false;
//        echo "<pre>";print_r($result);die;
        if(!empty($result)){
            $checkUrl = Yii::$app->utility->get_master_menu($param_menuid, NULL);
            
            /*
             * Check Menu with Role ID
             * If controller same but action is not assigned to current role then check with role id
             */
            $chkrole = Yii::$app->utility->get_menu_mapping($checkUrl['menuid'], Yii::$app->user->identity->role);
            if(empty($chkrole)){
                return false;
            }

            $url = explode('/', $checkUrl['menu_url']);
            $module=Yii::$app->controller->module->id;
            $controller=Yii::$app->controller->id;
            if($module == 'eMulazimApp'){
                if($controller == $url[0]){
                    $result1 = true;
                }
            }else{
                $m = $url[0];
                $c = @$url[1];
                if($module == 'dashboard' AND $controller == 'default'){
                    $result1 = true;
                }elseif($module == $m AND $controller == $c){
                    $result1 = true;
                }else{
                    $result1 = false;
                }
            }
        }
        return $result1;
    }
    
    public function login_auth($param_username, $param_password, $param_role){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="CALL `login_auth`(:param_username, :param_password, :param_role)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_username', $param_username); 
        $command->bindValue(':param_password', $param_password); 
        $command->bindValue(':param_role', $param_role); 
        $result=$command->queryOne();
       // echo "<pre>";print_r($result); die;
//        if(isset($result['is_exist']) AND $result['is_exist'] == 0){
//            
//            $connection->close();
//            return "";
//        }
        
        if(!empty($result)){
            $cookies = Yii::$app->response->cookies;
            $aa = $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$result['e_id']."eMULazIMCDAC".  strtotime(date('Y-m-d'));
            $aa = md5($aa);
            $cookies->add(new \yii\web\Cookie([
                'name' => 'eMulazimCookie',
                'value' => $aa,
            ]));
            $result['role'] = $param_role;
            $result['accessToken'] = $aa;
        }

        $connection->close();
        return $result;
    }
    
    /*
     * Get Top Menu
     */
//    public function get_submenu($parent){
//        $connection=   Yii::$app->db;
//        $connection->open();
//        $sql =" CALL `get_submenu`(:param_parent)";
//        $command = $connection->createCommand($sql); 
//        $command->bindValue(':param_parent', $parent);
//        $result= $command->queryAll();
//        $connection->close();
//        return $result;
//    }
    
    /*
    * Encrypt String
    */
    public function encryptString($encrypt){
        $string = base64_encode($encrypt);
        //echo "=--->".Encrypt_Key;
        $key = Encrypt_Key;
        if(isset(Yii::$app->user->identity) AND !empty(Yii::$app->user->identity)){
            if(!empty(Yii::$app->user->identity->role)){
            $key = $key.Yii::$app->user->identity->role;
            }
        }
        $encrypted = base64_encode(Yii::$app->security->encryptByKey($string, $key));
        $encrypted = rawurlencode($encrypted);
        return $encrypted;
    }

    /*
    * Decrypt String
    * @ Send encrypted String for decrypt
    */

    public function decryptString($string){
        $string = rawurldecode($string);
        $key = Encrypt_Key;
        if(isset(Yii::$app->user->identity) AND !empty(Yii::$app->user->identity)){
            if(!empty(Yii::$app->user->identity->role)){
            $key = $key.Yii::$app->user->identity->role;
            }
        }
      
        $decrypted = Yii::$app->security->decryptByKey(base64_decode($string), $key);
        $decrypted = base64_decode($decrypted);
        return $decrypted;
    }
    //---------------
    public function get_unit_details($u_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM `emp_dept_units` WHERE id='".$u_id."'";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return @$result[0]; 
    }
    //---------------
	
    /*Registration*/
       public function add_employee($param_employee_id,$param_email,$param_password,$param_role_id,$param_dept_id,$param_fname,$param_lname,$param_gender,$param_dob,$param_phone,$param_emergency_contact,$param_address,$param_city,$param_state,$param_zip,$param_contact,$param_p_address,$param_p_city,$param_p_state,$param_p_country,$param_p_zip,$param_contact2,$param_joining_date,$param_desg_id,$param_employment_type,$param_marital_status,$param_authority1,$param_authority2,$param_effected_from,$param_financial_year,$param_grade_pay_scale,$param_emplevel,$param_basic_cons_pay,$param_vpf_percentage,$param_updated_by,$param_date_of_change,$param_blood_group,$param_emp_image,$param_emp_signature,$param_emp_address_proof,$param_pan_number,$param_religion,$param_caste,$param_passport_detail,$param_category,$param_beltno,$param_rank,$param_substantive_rnk,$param_unit,$param_place_of_posting,$param_erss_job_profile,$param_erv_deployed,$param_offclocation,$param_citizenship,$param_from_date,$param_to_date,$confirmation_date,$partam_office_ord_no,$param_posting_remarks,$param_license_no,$param_license_expired_date ){
        

       
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_employee`(
            :param_employee_code,
            :param_username,
            :param_password,
            :param_role_id,
            :param_dept_id,
            :param_fname,
            :param_lname,
            :param_gender,
            :param_dob,
            :param_phone,
            :param_emergency_phone,
            :param_address,
            :param_city,
            :param_state,
            :param_zip,
            :param_contact,
            :param_p_address,
            :param_p_city,
            :param_p_state,
            :param_p_country,
            :param_p_zip,
            :param_p_contact,
            :param_joining_date,            
            :param_designation_id,            
            :param_employment_type,
            :param_marital_status,
            :param_authority1,
            :param_authority2,
            :param_effected_from,
            :param_financial_year,
            :param_grade_pay_scale,
            :param_level,
            :param_basic_cons_pay,
            :param_vpf_percentage,
            :param_updated_by,
            :param_date_of_change,
            :param_blood_group,
            :param_emp_image,
            :param_emp_signature,
            :param_emp_address_proof,
            :param_pan_number,
            :param_religion,
            :param_caste,
            :param_passport_detail,
            :param_category,
            :param_belt_no,
            :param_rank,
            :param_substantive_rnk,
            :param_unit,
            :param_place_of_posting,
            :param_erss_job_profile,
            :param_erv_deployed,
            :param_offclocation,
            :param_citizenship, 
             :param_from_date,
           :param_to_date,
           :confirmation_date, 
           :partam_office_ord_no, 
           :param_posting_remarks , 
           :param_license_no, 
           :param_license_expired_date,                              
            @Result)";
        $command = $connection->createCommand($sql);

        $command->bindValue(':param_employee_code', $param_employee_id);
        $command->bindValue(':param_username', $param_email);
        $command->bindValue(':param_password', $param_password);
        $command->bindValue(':param_role_id', '3');
        $command->bindValue(':param_dept_id', $param_dept_id);
        $command->bindValue(':param_fname', $param_fname);
        $command->bindValue(':param_lname', $param_lname);
        $command->bindValue(':param_gender', $param_gender);
        $command->bindValue(':param_dob', $param_dob);
        $command->bindValue(':param_phone', $param_phone);
        $command->bindValue(':param_emergency_phone',$param_emergency_contact);
        $command->bindValue(':param_address',$param_address);
        $command->bindValue(':param_city',$param_city);
        $command->bindValue(':param_state',$param_state);
        $command->bindValue(':param_zip',$param_zip);
        $command->bindValue(':param_contact',$param_contact);
        $command->bindValue(':param_p_address',$param_p_address);
        $command->bindValue(':param_p_city', $param_p_city);
        $command->bindValue(':param_p_state', $param_p_state);
        $command->bindValue(':param_p_country', $param_p_country);
        $command->bindValue(':param_p_zip', $param_p_zip);
        $command->bindValue(':param_p_contact', $param_contact2);
        $command->bindValue(':param_joining_date', $param_joining_date);    
        $command->bindValue(':param_designation_id', $param_desg_id);
        $command->bindValue(':param_employment_type', $param_employment_type);
        $command->bindValue(':param_marital_status', $param_marital_status);
        $command->bindValue(':param_authority1', $param_authority1);
        $command->bindValue(':param_authority2', $param_authority2);
        $command->bindValue(':param_effected_from', $param_effected_from);
        $command->bindValue(':param_financial_year', $param_financial_year);
        $command->bindValue(':param_grade_pay_scale', $param_grade_pay_scale);
        $command->bindValue(':param_level', $param_emplevel);
        $command->bindValue(':param_basic_cons_pay', $param_basic_cons_pay);
        $command->bindValue(':param_vpf_percentage', 0);
        $command->bindValue(':param_updated_by', Yii::$app->user->identity->e_id);
        $command->bindValue(':param_date_of_change', date('Y-m-d H:i:s'));
        $command->bindValue(':param_blood_group', $param_blood_group);
        $command->bindValue(':param_emp_image', $param_emp_image);
        $command->bindValue(':param_emp_signature', $param_emp_signature);
        $command->bindValue(':param_emp_address_proof', $param_emp_address_proof);
        $command->bindValue(':param_pan_number', $param_pan_number);
        $command->bindValue(':param_religion', $param_religion);
        $command->bindValue(':param_caste', $param_caste);
        $command->bindValue(':param_passport_detail', $param_passport_detail);
        $command->bindValue(':param_category', $param_category); 
        $command->bindValue(':param_belt_no', $param_beltno);
        $command->bindValue(':param_rank', $param_rank);
        $command->bindValue(':param_substantive_rnk', $param_substantive_rnk); 
        $command->bindValue(':param_unit', $param_unit);
        $command->bindValue(':param_place_of_posting', $param_place_of_posting);
        $command->bindValue(':param_erss_job_profile', $param_erss_job_profile);
        $command->bindValue(':param_erv_deployed', $param_erv_deployed);
        $command->bindValue(':param_offclocation', $param_offclocation);
        $command->bindValue(':param_citizenship', $param_citizenship);
        $command->bindValue(':param_from_date', $param_from_date);
        $command->bindValue(':param_to_date', $param_to_date);
        $command->bindValue(':confirmation_date', $confirmation_date);
         $command->bindValue(':partam_office_ord_no', $partam_office_ord_no);
          $command->bindValue(':param_posting_remarks', $param_posting_remarks);
       $command->bindValue(':param_license_no', $param_license_no);
          $command->bindValue(':param_license_expired_date', $param_license_expired_date);
        
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
     /*Registration*/
      public function add_visitor($param_employee_code, $param_username, $param_password,  $param_fname, $param_lname, $param_gender, $param_dob, $param_phone, $param_emergency_phone, $param_address, $param_city, $param_state, $param_zip, $param_contact, $param_p_address, $param_p_city, $param_p_state, $param_p_country, $param_p_zip, $param_p_contact,   $param_emp_image, $param_emp_signature,$param_emp_address_proof,$id_type,$id_number){
        

        // echo '------'.Yii::$app->user->identity->e_id; die();

        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_visitor`(:param_employee_code, :param_username, :param_password, :param_role_id,  :param_fname, :param_lname, :param_gender, :param_dob, :param_phone, :param_emergency_phone, :param_address, :param_city, :param_state,:param_zip,:param_contact, :param_p_address,:param_p_city,:param_p_state,:param_p_country,:param_p_zip, :param_p_contact, :param_employment_type, :param_emp_image,:param_emp_signature,:param_emp_address_proof, :param_id_type,:param_id_number,  @Result)";
        $command = $connection->createCommand($sql);

        $command->bindValue(':param_employee_code', $param_employee_code);
        $command->bindValue(':param_username', $param_username);
        $command->bindValue(':param_password', $param_password);
        $command->bindValue(':param_role_id', '23');      
        $command->bindValue(':param_fname', $param_fname);
        $command->bindValue(':param_lname', $param_lname);
        $command->bindValue(':param_gender', $param_gender);
        $command->bindValue(':param_dob', $param_dob);
        $command->bindValue(':param_phone', $param_phone);
        $command->bindValue(':param_emergency_phone', $param_emergency_phone);
        $command->bindValue(':param_address', $param_address);
        $command->bindValue(':param_city', $param_city); 
        $command->bindValue(':param_state', $param_state); 
        $command->bindValue(':param_zip', $param_zip); 
        $command->bindValue(':param_contact', $param_contact); 
        $command->bindValue(':param_p_address', $param_p_address);
        $command->bindValue(':param_p_city', $param_p_city);
        $command->bindValue(':param_p_state', $param_p_state);
        $command->bindValue(':param_p_country', $param_p_country);
        $command->bindValue(':param_p_zip', $param_p_zip);
        $command->bindValue(':param_p_contact', $param_p_contact);     
        $command->bindValue(':param_employment_type', 'V');
        $command->bindValue(':param_emp_image', $param_emp_image);
        $command->bindValue(':param_emp_signature', $param_emp_signature);
        $command->bindValue(':param_id_type', $id_type);
        $command->bindValue(':param_id_number', $id_number);
        $command->bindValue(':param_emp_address_proof', $param_emp_address_proof);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    
   /* old code
   public function update_employee($param_employee_code,$param_fname, $param_lname, $param_gender, $param_dob, $param_phone, $param_emergency_phone, $param_address, $param_city, $param_state, $param_zip, $param_contact, $param_p_address, $param_p_city, $param_p_state, $param_p_country, $param_p_zip, $param_p_contact, $param_marital_status, $param_blood_group, $param_emp_signature, $param_is_active ){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_employee`(:param_employee_code, :param_fname, :param_lname, :param_gender, :param_dob, :param_phone, :param_emergency_phone, :param_address, :param_city, :param_state,:param_zip,:param_contact, :param_p_address,:param_p_city,:param_p_state,:param_p_country,:param_p_zip, :param_p_contact, :param_marital_status, :param_blood_group,:param_emp_image,:param_emp_signature, :param_is_active, @Result)";
        $command = $connection->createCommand($sql);
        $command->bindValue(':param_employee_code', $param_employee_code);
        $command->bindValue(':param_fname', $param_fname);
        $command->bindValue(':param_lname', $param_lname);
        $command->bindValue(':param_gender', $param_gender);
        $command->bindValue(':param_dob', $param_dob);
        $command->bindValue(':param_phone', $param_phone);
        $command->bindValue(':param_emergency_phone', $param_emergency_phone);
        $command->bindValue(':param_address', $param_address);
        $command->bindValue(':param_city', $param_city); 
        $command->bindValue(':param_state', $param_state); 
        $command->bindValue(':param_zip', $param_zip); 
        $command->bindValue(':param_contact', $param_contact); 
        $command->bindValue(':param_p_address', $param_p_address);
        $command->bindValue(':param_p_city', $param_p_city);
        $command->bindValue(':param_p_state', $param_p_state);
        $command->bindValue(':param_p_country', $param_p_country);
        $command->bindValue(':param_p_zip', $param_p_zip);
        $command->bindValue(':param_p_contact', $param_p_contact);
        $command->bindValue(':param_marital_status', $param_marital_status);
        $command->bindValue(':param_blood_group', $param_blood_group);
        $command->bindValue(':param_emp_signature', $param_emp_signature);
        $command->bindValue(':param_is_active', $param_is_active);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    } */

    /////////////////////////////////////////////////   
         public function update_employee($param_employee_code, $param_email_id, $param_fname, $param_lname, $param_name_hindi, $param_gender, $param_dob, $param_phone, $param_emergency_phone, $param_address, $param_city, $param_state, $param_zip, $param_contact, $param_p_address, $param_p_city, $param_p_state, $param_p_zip, $param_p_contact, $param_pan_number, $param_marital_status, $param_blood_group,$param_emp_image, $param_emp_signature,$param_emp_address_proof, $param_religion, $param_category, $param_caste, $param_passport_detail,$param_is_active,$param_beltno,$param_rank,$param_substantive_rnk,$param_unit,$param_employement_status,$param_place_of_posting,$param_erss_job_profile,$param_erv_deployed,$param_offclocation) { 

        // die('==>'.$param_employement_status);
        $connection=   Yii::$app->db;
        $connection->open();
         $sql =" CALL `Updt_employee`(:Param_employee_code, :Param_email_id, :Param_fname, :Param_lname, :Param_name_hindi, :Param_gender, :Param_dob, :Param_phone, :Param_emergency_phone, :Param_address, :Param_city, :Param_state, :Param_zip, :Param_contact, :Param_p_address, :Param_p_city, :Param_p_state, :Param_p_zip, :Param_p_contact, :Param_pan_number, :Param_marital_status, :Param_blood_group, :Param_emp_image, :Param_emp_signature,:Param_emp_address_proof, :Param_religion, :Param_category, :Param_caste, :Param_passport_detail,:param_is_active,:param_beltno,:param_rank,:param_substantive_rnk,:param_unit,:param_employement_status,:param_place_of_posting,:param_erss_job_profile,:param_erv_deployed,:param_offclocation,@Result)";

        $command = $connection->createCommand($sql);
        $command->bindValue(':Param_employee_code', $param_employee_code);
        $command->bindValue(':Param_email_id', $param_email_id);
        $command->bindValue(':Param_fname', $param_fname);
        $command->bindValue(':Param_lname', $param_lname);
        $command->bindValue(':Param_name_hindi', $param_name_hindi);
        $command->bindValue(':Param_gender', $param_gender);
        $command->bindValue(':Param_dob', $param_dob);
        $command->bindValue(':Param_phone', $param_phone);
        $command->bindValue(':Param_emergency_phone', $param_emergency_phone);
        $command->bindValue(':Param_address', $param_address);
        $command->bindValue(':Param_city', $param_city); 
        $command->bindValue(':Param_state', $param_state); 
        $command->bindValue(':Param_zip', $param_zip); 
        $command->bindValue(':Param_contact', $param_contact); 
        $command->bindValue(':Param_p_address', $param_p_address);
        $command->bindValue(':Param_p_city', $param_p_city);
        $command->bindValue(':Param_p_state', $param_p_state);
        $command->bindValue(':Param_p_zip', $param_p_zip);
        $command->bindValue(':Param_p_contact', $param_p_contact);
        $command->bindValue(':Param_pan_number', $param_pan_number);
        $command->bindValue(':Param_marital_status', $param_marital_status);
        $command->bindValue(':Param_blood_group', $param_blood_group);
        $command->bindValue(':Param_emp_image', $param_emp_image);
        $command->bindValue(':Param_emp_signature', $param_emp_signature);
        $command->bindValue(':Param_emp_address_proof', $param_emp_address_proof);
        $command->bindValue(':Param_religion', $param_religion);
        $command->bindValue(':Param_category', $param_category);
        $command->bindValue(':Param_caste', $param_caste);
        $command->bindValue(':Param_passport_detail', $param_passport_detail);
        $command->bindValue(':param_is_active', $param_is_active);
        $command->bindValue(':param_beltno', $param_beltno);
        $command->bindValue(':param_rank', $param_rank);
        $command->bindValue(':param_substantive_rnk', $param_substantive_rnk);
        $command->bindValue(':param_unit',$param_unit);
        $command->bindValue(':param_employement_status',$param_employement_status);
        $command->bindValue(':param_place_of_posting',$param_place_of_posting);
        $command->bindValue(':param_erss_job_profile',$param_erss_job_profile);
        $command->bindValue(':param_erv_deployed',$param_erv_deployed);
        $command->bindValue(':param_offclocation',$param_offclocation);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
///////////////////////////////////////////////////////////////////////////////////
    public function get_service_details($param_employee_code,$info_type)
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_service_details`(:param_employee_code, :info_type)";
	    $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        $command->bindValue(':info_type', $info_type);
        if($info_type == 'Current'){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        
        $connection->close();
        return $result; 
    }

    public function get_act_employees($param_employee_code=NULL){

       
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM `act_hp_employee` WHERE employee_code = '".$param_employee_code."';";		
        $command = $connection->createCommand($sql); 
        if(!empty($param_employee_code)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
    
    public function get_employees($param_employee_code=NULL){

       
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="CALL `get_employees`(:param_employee_code)";		
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        if(!empty($param_employee_code)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
    public function get_qualification($param_role_id, $param_employee_code,$param_eq_id, $param_status){
        
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_get_qualification`(:param_employee_code, :param_role_id, :param_eq_id, :param_status)";
	    $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        $command->bindValue(':param_role_id', $param_role_id);
        $command->bindValue(':param_eq_id', $param_eq_id);
        $command->bindValue(':param_status', $param_status);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
    
    
    public function get_family_details($param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_get_family_details`(:param_employee_code)";
	    $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        //        $command->bindValue(':param_status', $param_status);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }


    // get_training_details
    public function get_training_details($param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_get_employee_training_details`(:param_employee_code)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        //        $command->bindValue(':param_status', $param_status);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }

    public function get_emptraining_details($param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_employee_all_training_details`(:param_employee_code)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        //        $command->bindValue(':param_status', $param_status);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
    

    // get_language_details
    public function get_language_details($param_employee_code,$param_withoutLangID=NULL){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_get_language_details`(:param_employee_code,:param_withoutLangID)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        $command->bindValue(':param_withoutLangID', $param_withoutLangID);
        //        $command->bindValue(':param_status', $param_status);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }

    public function get_language(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_language`()";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }    

    public function get_experience_details($param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_get_employee_experience_details`(:param_employee_code)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        //        $command->bindValue(':param_status', $param_status);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
    

    public function get_master_roles($param_role_id=NULL){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_master_roles`(:param_role_id)";
        $command = $connection->createCommand($sql);        
        $command->bindValue(':param_role_id', $param_role_id);
        if(!empty($param_role_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        
        $connection->close();
        return $result;       
    }
     
    public function get_master_active_roles()
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_master_active_roles`(@Result)";
        $command = $connection->createCommand($sql);        
        
        $result=$command->queryAll();
        
        
        $connection->close();
        return $result;       
    }

    public function get_act_employees_by_params($paramsemp)
    {


    }

    public function get_employees_by_params($paramsemp)
    {
        
        $fname=NULL;
        $lname=NULL;
        $location=NULL;
        $designation=NULL;
        $str='';
        $connection=   Yii::$app->db;
        $connection->open();
        if(!empty($paramsemp['fname']))
        $fname = trim($paramsemp['fname']);
        if(!empty($paramsemp['lname']))
        $lname = trim($paramsemp['lname']);
        if(!empty($paramsemp['posting']))
        $location = trim($paramsemp['posting']);
        if(!empty($paramsemp['designation']))
        $designation = trim($paramsemp['designation']);       
       // print_r($_REQUEST);
       
        
        $str =array();
        if(!empty($fname))        
        $str[] = "`fname` LIKE '".$fname."'";
        if(!empty($lname))
        $str[] = "`lname` LIKE '".$lname."'";
        if(!empty($location))
        $str[]  = "`location`='".$location."'";
        if(!empty($designation))
        $str[] = "desg_name LIKE '".$designation."'";
        $text='';
        foreach($str as $k=>$s){

            
            if($k==0){
                $text = $s;
            }else{                
                $text = $text."  OR ".$s;            
            }
        }
       // echo $text;
       


        $sql = "
        SELECT ahe.*,b.*,e.* FROM act_hp_employee ahe
        LEFT JOIN rbac_employee b ON (b.employee_code = ahe.employee_code)
        LEFT JOIN hr_service_details e ON (e.employee_code=ahe.employee_code)
        WHERE 1=1      
        and (".$text.")   
        ORDER BY `sr_no` DESC
        ";  
        $command = $connection->createCommand($sql);      
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }

     
    
    
//    public function get_employee_leave_requests(){
//        $connection=   Yii::$app->db;
//        $connection->open();
//        $sql =" CALL `get_employee_leave_requests`()";
//	$command = $connection->createCommand($sql); 
//       // $command->bindValue(':param_e_id', $param_e_id);
//        $result=$command->queryAll();
//        $connection->close();
//        return $result;       
//    }
    
    // get_all_category
    public function get_all_category(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_all_category`()";
        
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }

    public function get_dept($param_dept_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_dept`(:param_dept_id)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_dept_id', $param_dept_id);
        if(!empty($param_dept_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }


    public function get_designation($param_desg_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_designation`(:param_desg_id)";
		
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_desg_id', $param_desg_id);
        if(!empty($param_desg_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }

    public function get_designation_by_dptID($param_dept_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_designation_by_dptID`(:param_dept_id)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_dept_id', $param_dept_id);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }


   public function add_appraisal($param_app_id, $param_app_title,$param_app_job_description,$param_app_document,$param_app_deleted, $param_app_uplodatedby,$param_app_status,$param_app_achievement,$param_app_year){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_appraisal`(:param_app_id, :param_app_title, :param_app_job_description, :param_app_deleted,:param_app_uplodatedby, :param_app_status, :param_app_document, :param_app_achievement, :param_app_year, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_app_id', $param_app_id); 
        $command->bindValue(':param_app_title', $param_app_title); 
        $command->bindValue(':param_app_job_description', $param_app_job_description); 
        $command->bindValue(':param_app_deleted', $param_app_deleted);
        $command->bindValue(':param_app_uplodatedby', $param_app_uplodatedby, PDO::PARAM_STR);
        $command->bindValue(':param_app_status', $param_app_status);
        $command->bindValue(':param_app_document', $param_app_document); 
         $command->bindValue(':param_app_achievement', $param_app_achievement); 
 $command->bindValue(':param_app_year', $param_app_year); 
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function update_apprasial_status($param_app_id, $param_app_role,$param_app_feedback,$param_app_rating,$param_approve_by){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `upd_apprasial_status`(:param_app_id,:param_app_role, :param_app_feedback, :param_app_rating, :param_approve_by,  @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_app_id', $param_app_id);        
         
           $command->bindValue(':param_app_role', $param_app_role);
           $command->bindValue(':param_app_feedback', $param_app_feedback);
           $command->bindValue(':param_app_rating', $param_app_rating);
            $command->bindValue(':param_approve_by', $param_approve_by);
       

        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function apprasial_request($param_app_id, $param_app_comment,$param_app_status,$param_app_role){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `apprasial_request_change`(:param_app_id, :param_app_comment, :param_app_status, :param_app_role,  @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_app_id', $param_app_id);        
        $command->bindValue(':param_app_status', $param_app_status);
        $command->bindValue(':param_app_role', $param_app_role);
        $command->bindValue(':param_app_comment', $param_app_comment);     
       

        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function Apprasial_revoke($param_app_id, $param_app_status,$param_app_role){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `Apprasial_revoke`(:param_app_id,  :param_app_status, :param_app_role,  @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_app_id', $param_app_id);        
           $command->bindValue(':param_app_status', $param_app_status);
           $command->bindValue(':param_app_role', $param_app_role);           
       

        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
     public function get_appraisal_statges($param_app_user, $param_app_year){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_appraisal_statges`(:param_app_user,  :param_app_year,   @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_app_user', $param_app_user);        
           $command->bindValue(':param_app_year', $param_app_year);                   
       

        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }

     public function get_appraisal_details($param_employee_fla,$param_employee_sla,$param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_appraisal_details`(:param_employee_fla,:param_employee_sla,:param_employee_code)";        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_fla', $param_employee_fla);
         $command->bindValue(':param_employee_sla', $param_employee_sla); 
          $command->bindValue(':param_employee_code', $param_employee_code); 
        if(!empty($param_employee_fla) and !empty($param_employee_sla) ){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }

    
    public function add_update_dept($param_dept_id, $param_dept_name,$param_dept_desc,$param_is_active){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_dept`(:param_dept_id, :param_dept_name, :param_dept_desc,:param_is_active, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_dept_id', $param_dept_id); 
        $command->bindValue(':param_dept_name', $param_dept_name); 
        $command->bindValue(':param_dept_desc', $param_dept_desc); 
        $command->bindValue(':param_is_active', $param_is_active); 
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function update_password($param_current_password, $param_new_password){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `update_password`(:param_username, :param_emp_code, :param_current_password,:param_new_password, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_username', Yii::$app->user->identity->email_id); 
        $command->bindValue(':param_emp_code', Yii::$app->user->identity->e_id);
        $command->bindValue(':param_current_password', $param_current_password);
        $command->bindValue(':param_new_password', $param_new_password);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    
    public function add_update_desg($param_desg_id, $param_desg_name,$param_desg_desc,$param_is_active){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_desg`(:param_desg_id, :param_desg_name, :param_desg_desc,:param_is_active, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_desg_id', $param_desg_id); 
        $command->bindValue(':param_desg_name', $param_desg_name); 
        $command->bindValue(':param_desg_desc', $param_desg_desc); 
        $command->bindValue(':param_is_active', $param_is_active); 
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    
    public function verify_qualification($param_eq_id, $param_employee_code,$param_status){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `verify_qualification`(:param_eq_id, :param_employee_code, :param_status, :param_action_by, @`result`)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_eq_id', $param_eq_id); 
        $command->bindValue(':param_employee_code', $param_employee_code); 
        $command->bindValue(':param_status', $param_status); 
        $command->bindValue(':param_action_by', Yii::$app->user->identity->e_id); 
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    
    public function hr_verify_family_member($param_ef_id, $param_e_id,$param_status, $param_medical_benefit, $param_edu_allowances, $param_is_child_twins){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_verify_family_member`(:param_ef_id, :param_e_id, :param_status,:param_approved_by, :param_medical_benefit, :param_edu_allowances, :param_is_child_twins, @`result`)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_ef_id', $param_ef_id); 
        $command->bindValue(':param_e_id', $param_e_id); 
        $command->bindValue(':param_status', $param_status); 
        $command->bindValue(':param_approved_by', Yii::$app->user->identity->e_id); 
        $command->bindValue(':param_medical_benefit', $param_medical_benefit); 
        $command->bindValue(':param_edu_allowances', $param_edu_allowances); 
        $command->bindValue(':param_is_child_twins', $param_is_child_twins); 
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function update_rbac_status($param_map_id, $param_status){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `update_rbac_status`(:param_map_id, :param_status, @`result`)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_map_id', $param_map_id); 
        $command->bindValue(':param_status', $param_status); 
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
	public function get_dept_emp($param_dept_id){
        $Param_dept_map_id = $Param_emp_code = NULL;
        $result = Yii::$app->utility->hr_get_dept_mapping($param_dept_id, $Param_emp_code, $Param_dept_map_id);
        if(!empty($result)){
            $n = array();
            $i=0;
            foreach($result as $resul){
                $info = Yii::$app->utility->get_employees($resul['employee_code']);
                if(!empty($info)){
                    $id = base64_encode($resul['employee_code']);
                    $name = $resul['name_hindi']." / ".$resul['fullname'].", ".$info['desg_name'];
                    $n[$i]['employee_code']=$id;
                    $n[$i]['name']=$name;
                    $i++;
                }
            }
            $result = $n;
        }
        return $result;
    }
    // public function get_dept_emp($param_dept_id){
        // $connection=   Yii::$app->db;
        // $connection->open();
        // $sql =" CALL `get_dept_emp`(:param_dept_id)";
	// $command = $connection->createCommand($sql); 
        // $command->bindValue(':param_dept_id', $param_dept_id);
        // $result=$command->queryAll();
        // $connection->close();
        // if(!empty($result)){
            // $n = array();
            // $i=0;
            // foreach($result as $resul){
                // $id = base64_encode($resul['employee_code']);
                
                // $name = $resul['name'].", ".$resul['desg_name'];
                // $n[$i]['employee_code']=$id;
                // $n[$i]['name']=$name;
                // $i++;
            // }
            // $result = $n;
        // }
        // return $result;
    // }
    public function getDeptEmp($param_dept_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_dept_emp`(:param_dept_id)";
	$command = $connection->createCommand($sql); 
        $command->bindValue(':param_dept_id', $param_dept_id);
        $result=$command->queryAll();
        $connection->close();
        return $result;
    }
    
    public function get_marital_status(){
        $type = MaritalStatus;
        $i=0;
        $list = array();
        foreach($type as $key=>$val){
            //$id = Yii::$app->utility->encryptString($val);
            $id = base64_encode($val);
            $list[$i]['id']=$id;
            $list[$i]['type']=$val;
            $i++;
        }
        return $list;
    }

    public function get_rank_list(){
        $type = RankList;
        $i=0;
        $list = array();
        foreach($type as $key=>$val){
            //$id = Yii::$app->utility->encryptString($val);
            $id = base64_encode($val);
            $list[$i]['id']=$id;
            $list[$i]['type']=$val;
            $i++;
        }
        return $list;
    }

    public function get_unit_list(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM `distLocation` ORDER BY `distLocation`.`location` ASC";
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();
        $connection->close();
        return $result;
    }

    public function get_listemployement_status(){
        $type = ListEmployementStatus;
        $i=0;
        $list = array();
        foreach($type as $key=>$val){
            //$id = Yii::$app->utility->encryptString($val);
            $id = base64_encode($val);
            $list[$i]['id']=$id;
            $list[$i]['type']=$val;
            $i++;
        }
        return $list;
    }

    public function get_blood_gourp(){
        
        $type = BloodGroups;
        $i=0;
        $list = array();
        foreach($type as $key=>$val){
            //$id = Yii::$app->utility->encryptString($val);
            $id = base64_encode($val);
            $list[$i]['id']=$id;
            $list[$i]['type']=$val;
            $i++;
        }
        return $list;
    }
    
    public function get_rbac_employee($param_map_id=null){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_rbac_employee`(:param_map_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_map_id', $param_map_id); 
        $result=$command->queryAll();
        $connection->close();
        return $result;
    }
       
    public function replaceSpecialChar($string){
        $string = str_replace("@", '[at]', $string);
        $string = str_replace(".", '[dot]', $string);
        
        return $string; 
    }
    
    public function get_mapped_menu($param_menu_type, $param_role_id, $param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_mapped_menu`(:param_menu_type, :param_role_id, :param_employee_code)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_menu_type', $param_menu_type); 
        $command->bindValue(':param_role_id', $param_role_id);
        $command->bindValue(':param_employee_code', $param_employee_code);
        $result=$command->queryAll();
        $connection->close();
        return $result;
    }
    public function get_master_menu($param_menu_id, $param_parent_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_master_menu`(:param_menu_id, :param_parent_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_menu_id', $param_menu_id); 
        $command->bindValue(':param_parent_id', $param_parent_id); 
        if(!empty($param_menu_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        
        $connection->close();
        return $result;
    }
    
    public function add_update_menu_mapping($param_map_id, $param_menuid, $param_role_id, $param_employee_code, $param_is_active){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_menu_mapping`(:param_map_id, :param_menuid, :param_role_id, :param_employee_code, :param_is_active, @`result`)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_map_id', $param_map_id); 
        $command->bindValue(':param_menuid', $param_menuid); 
        $command->bindValue(':param_role_id', $param_role_id); 
        $command->bindValue(':param_employee_code', $param_employee_code); 
        $command->bindValue(':param_is_active', $param_is_active); 
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function add_update_master_menu($param_menuid, $param_menu_name, $param_menu_dsc, $param_menu_url, $menu_type, $param_parent, $param_order, $param_is_active){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_master_menu`(:param_menuid, :param_menu_name, :param_menu_dsc, :param_menu_url, :menu_type, :param_parent, :param_order, :param_is_active, @`result`)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_menuid', $param_menuid);
        $command->bindValue(':param_menu_name', $param_menu_name);
        $command->bindValue(':param_menu_dsc', $param_menu_dsc);
        $command->bindValue(':param_menu_url', $param_menu_url);
        $command->bindValue(':menu_type', $menu_type);
        $command->bindValue(':param_parent', $param_parent);
        $command->bindValue(':param_order', $param_order);
        $command->bindValue(':param_is_active', $param_is_active);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function activities_logs($param_action_name, $param_action_url=NULL, $param_action_for, $param_data_json, $param_remarks){
        if(!empty(Yii::$app->user->identity->e_id)){
        $url =  Yii::$app->urlManager->parseRequest(Yii::$app->request);
        $param_action_url = $url['0'];
        //echo "<pre>";print_r($aaa);die;
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `activities_logs`(:param_employee_code, :param_role_id, :param_action_name, :param_action_url, :param_action_for, :param_data_json, :param_remarks, :param_ip_address, :param_user_agent, @`result`)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', Yii::$app->user->identity->e_id);
        $command->bindValue(':param_role_id', Yii::$app->user->identity->role);
        $command->bindValue(':param_action_name', $param_action_name);
        $command->bindValue(':param_action_url', $param_action_url);
        $command->bindValue(':param_action_for', $param_action_for);
        $command->bindValue(':param_data_json', $param_data_json);
        $command->bindValue(':param_remarks', $param_remarks);
        $command->bindValue(':param_ip_address', $_SERVER['REMOTE_ADDR']);
        $command->bindValue(':param_user_agent', $_SERVER['HTTP_USER_AGENT']);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
        }
    }
    
    public function get_last_emp_code(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_last_emp_code`()";
	    $command = $connection->createCommand($sql); 
        $result=$command->queryOne();
        $connection->close();
        return $result;       
    }
    
    public function get_rbac_employee_role(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_rbac_employee_role`(:param_employee_code)";
	$command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', Yii::$app->user->identity->employee_code);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
    
    public function get_roles($param_role_id=NULL){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_roles`(:param_role_id)";
	$command = $connection->createCommand($sql); 
        $command->bindValue(':param_role_id', $param_role_id);
        if(!empty($param_role_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        
        $connection->close();
        return $result;       
    }
    public function get_emp_code_with_role_id($param_role_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_emp_code_with_role_id`(:param_role_id)";
	$command = $connection->createCommand($sql); 
        $command->bindValue(':param_role_id', $param_role_id);
        $result=$command->queryOne();
        $connection->close();
        return $result;       
    }
    
    public function get_emp_allowance($param_designation_id, $param_emp_type, $param_financial_yr){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_emp_allowance`(:param_designation_id, :param_emp_type, :param_financial_yr)";
	$command = $connection->createCommand($sql); 
        $command->bindValue(':param_designation_id', $param_designation_id);
        $command->bindValue(':param_emp_type', $param_emp_type);
        $command->bindValue(':param_financial_yr', $param_financial_yr);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
        
    public function add_update_emp_allowance($param_id, $param_designation_id, $param_emp_type, $param_financial_yr, $param_allowance_type, $param_amount, $param_sanc_type){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_emp_allowance`(:param_id, :param_designation_id, :param_emp_type, :param_financial_yr, :param_allowance_type, :param_amount, :param_sanc_type, :param_updated_by, @`result`)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_id', $param_id);
        $command->bindValue(':param_designation_id', $param_designation_id);
        $command->bindValue(':param_emp_type', $param_emp_type);
        $command->bindValue(':param_financial_yr', $param_financial_yr);
        $command->bindValue(':param_allowance_type', $param_allowance_type);
        $command->bindValue(':param_amount', $param_amount);
        $command->bindValue(':param_sanc_type', $param_sanc_type);
        $command->bindValue(':param_updated_by', Yii::$app->user->identity->e_id);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
        
    public function get_menu_mapping($param_menuid, $param_role_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_menu_mapping`(:param_menuid, :param_role_id)";
	$command = $connection->createCommand($sql); 
        $command->bindValue(':param_menuid', $param_menuid);
        $command->bindValue(':param_role_id', $param_role_id);
        if(!empty($param_menuid) AND !empty($param_role_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        
        $connection->close();
        return $result;       
    }
    
    public function getclaimtypename($shortname){
        $claimtype="";
        if(!empty($shortname)){
            $Emp_Allowances = Emp_Allowances;
            foreach($Emp_Allowances as $e){
                if($e['shortname']==$shortname){
                    $claimtype=$e['name'];
                }
            }
        }
        return $claimtype;
    }
    
    public function add_update_master_role($param_role_id, $param_role, $param_desc, $param_is_active){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_master_role`(:param_role_id, :param_role, :param_desc, :param_is_active, @`result`)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_role_id', $param_role_id);
        $command->bindValue(':param_role', $param_role);
        $command->bindValue(':param_desc', $param_desc);
        $command->bindValue(':param_is_active', $param_is_active);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function pr_get_emp_project_roles($Param_emp_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `pr_get_emp_project_roles`(:Param_emp_id)";
	$command = $connection->createCommand($sql); 
        $command->bindValue(':Param_emp_id', $Param_emp_id);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
    
function numberTowords($number)
{

	$no = round($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'one', '2' => 'two',
         '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
         '7' => 'seven', '8' => 'eight', '9' => 'nine',
         '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
         '13' => 'thirteen', '14' => 'fourteen',
         '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
         '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
         '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
         '60' => 'sixty', '70' => 'seventy',
         '80' => 'eighty', '90' => 'ninety');
        $digits = array('', 'hundred', 'thousand', 'lac', 'crore');
        while ($i < $digits_1) {
          $divider = ($i == 2) ? 10 : 100;
          $number = floor($no % $divider);
          $no = floor($no / $divider);
          $i += ($divider == 10) ? 1 : 2;
          if ($number) {
             $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
             $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
             $str [] = ($number < 21) ? $words[$number] .
                 " " . $digits[$counter] . $plural . " " . $hundred
                 :
                 $words[floor($number / 10) * 10]
                 . " " . $words[$number % 10] . " "
                 . $digits[$counter] . $plural . " " . $hundred;
          } else $str[] = null;
       }
       $str = array_reverse($str);
       $result = implode('', $str);
       $points = ($point) ?
         "." . $words[$point / 10] . " " . 
               $words[$point = $point % 10] : '';

     if(empty($result)){
     $result = "Zero Only";
     }else{
     $result = $result." Only";
     }

       return ucwords($result);
    }	
 
    public function validatePdfFile($file){
//        echo '<pre>';print_r($file); die;
        if(!empty($file)){
            $f = fopen($file, 'rb');
            $header1 = fread($f, 3);
            fclose($f);   
            $check1 = strncmp($header1, "\x50\x44\x46", 3)==0 && strlen ($header1)==3;

            $f = fopen($file, 'rb');
            $header2 = fread($f, 4);
            fclose($f);   
            $check2 = strncmp($header2, "\x25\x50\x44\x46", 4)==0 && strlen ($header2)==4;

            if($check1 || $check2){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
	
	public function hr_get_dept_mapping($Param_dept_id, $Param_emp_code, $Param_dept_map_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_get_dept_mapping`(:Param_dept_id, :Param_emp_code, :Param_dept_map_id)";
	$command = $connection->createCommand($sql); 
        $command->bindValue(':Param_dept_id', $Param_dept_id);
        $command->bindValue(':Param_emp_code', $Param_emp_code);
        $command->bindValue(':Param_dept_map_id', $Param_dept_map_id);
        
        if(!empty($Param_dept_map_id) ){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }


    //grievance
     public function add_grievance($param_app_id, $param_app_title,$param_app_description,$param_app_document,$param_complaint_type,$param_app_uplodatedby,$param_app_status,$param_app_docketno,$param_app_role,$param_nature){ 
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_grievance`(:param_app_id, :param_app_title, :param_app_description,:param_app_document,:param_complaint_type, :param_app_uplodatedby, :param_app_status, :param_app_docketno, :param_app_role,:param_nature, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_app_id', $param_app_id); 
        $command->bindValue(':param_app_title', $param_app_title); 
        $command->bindValue(':param_app_description', $param_app_description); 
         $command->bindValue(':param_app_document', $param_app_document); 
         $command->bindValue(':param_complaint_type', $param_complaint_type);
          $command->bindValue(':param_app_uplodatedby', $param_app_uplodatedby, PDO::PARAM_STR);
           $command->bindValue(':param_app_status', $param_app_status);       
         $command->bindValue(':param_app_docketno', $param_app_docketno); 
          $command->bindValue(':param_app_role', $param_app_role); 
            $command->bindValue(':param_nature', $param_nature); 

        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
     public function get_grievance_details($param_employee_fla,$param_employee_sla,$param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_grievance_details`(:param_employee_fla,:param_employee_sla,:param_employee_code)";        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_fla', $param_employee_fla);
         $command->bindValue(':param_employee_sla', $param_employee_sla); 
          $command->bindValue(':param_employee_code', $param_employee_code); 
        if(!empty($param_employee_fla) and !empty($param_employee_sla) ){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
public function update_grievance_status($param_app_id, $param_app_role,$param__app_empcode,$param_app_comment,$param_app_status){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `upd_grievance_status`(:param_app_id,   :param_app_role, :param__app_empcode, :param_app_comment, :param_app_status,  @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_app_id', $param_app_id);            
           $command->bindValue(':param_app_role', $param_app_role);
            $command->bindValue(':param__app_empcode', $param__app_empcode);
           $command->bindValue(':param_app_comment', $param_app_comment);
            $command->bindValue(':param_app_status', $param_app_status);    
       
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
        
public function update_grievance_resubmit($param_app_id, $param_app_role,$param__app_empcode,$param_app_status){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `grievance_resubmit`(:param_app_id,   :param_app_role, :param__app_empcode, :param_app_status,   @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_app_id', $param_app_id);            
           $command->bindValue(':param_app_role', $param_app_role);
            $command->bindValue(':param__app_empcode', $param__app_empcode);
       
            $command->bindValue(':param_app_status', $param_app_status);    
       
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }


public function grievance_request($param_app_id, $param_app_comment,$param_app_status,$param_app_role,$param_app_emp){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `grievance_request_change`(:param_app_id, :param_app_comment, :param_app_status, :param_app_role, :param_app_emp,  @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_app_id', $param_app_id);        
           $command->bindValue(':param_app_status', $param_app_status);
           $command->bindValue(':param_app_role', $param_app_role);
           $command->bindValue(':param_app_comment', $param_app_comment);     
         $command->bindValue(':param_app_emp', $param_app_emp); 

        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function get_grievance_type($param_grie_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_grievance_type`(:param_grie_id)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_grie_id', $param_grie_id);
        if(!empty($param_grie_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
    public function add_update_grievance($param_grie_id, $param_grie_name,$param_grie_desc,$param_is_active,$param_nature,$param_emp_type){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_grievance`(:param_grie_id, :param_grie_name, :param_grie_desc,:param_is_active,:param_nature,:param_emp_type, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_grie_id', $param_grie_id); 
        $command->bindValue(':param_grie_name', $param_grie_name); 
        $command->bindValue(':param_grie_desc', $param_grie_desc); 
        $command->bindValue(':param_is_active', $param_is_active); 
        $command->bindValue(':param_nature', $param_nature);
               $command->bindValue(':param_emp_type', $param_emp_type); 

        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
   
    



      /* Rewards module started*/
        
    public function get_rewards($param_reward_id=NULL){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_rewards`(:param_reward_id)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_reward_id', $param_reward_id);
        if(!empty($param_reward_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
    public function get_applied_rewards($param_reward_id=NULL,$param_e_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_applied_rewards`(:param_reward_id,:param_e_id)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_reward_id', $param_reward_id);
        $command->bindValue(':param_e_id', $param_e_id);
        if(!empty($param_reward_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
    public function get_rewards_apply_detail($param_reward_apply_id=NULL){
        
        //echo $param_reward_apply_id;die;
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_rewards_apply_detail`(:param_reward_apply_id)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_reward_apply_id', $param_reward_apply_id);
        if(!empty($param_reward_apply_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
    public function get_reward_category(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `reward_get_category`()";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
    public function get_cat_item($cat_code) {
        //
        $connection = Yii::$app->db;
        $connection->open();
        $sql = " CALL `reward_get_cat_item`(:cat_code)";
       //  echo "<pre>dd";print_r($cat_code);die;
        $command = $connection->createCommand($sql);
        $command->bindValue(':cat_code', $cat_code);
        $result = $command->queryAll();
        $connection->close();
        return $result;
    }
    
     public function apply_rewards($param_reward_apply_id=NULL,$param_reward_id,$param_eid,$param_status){
         
        // echo $param_reward_apply_id;die;
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `apply_reward`(:param_reward_apply_id,:param_reward_id,:param_eid,:param_status, @Result)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_reward_apply_id', $param_reward_apply_id);
        $command->bindValue(':param_reward_id', $param_reward_id);
        $command->bindValue(':param_eid', $param_eid);
        $command->bindValue(':param_status', $param_status);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut;    
    }
    public function add_update_reward($param_reward_id, $param_name,$param_description,$param_is_active,$param_reward_type_id,$param_reward_sub_cat,$param_created_by,$emp_of_month,$resonse_time,$erv_no){
        $connection=   Yii::$app->db;
        $connection->open();
       
        $sql =" CALL `add_update_reward`(:param_reward_id, :param_name, :param_description,:param_is_active,:param_reward_type_id,:param_reward_sub_cat,:param_created_by,:param_emp_of_month,:params_resonse_time,:params_erv_no,@Result)";
        $command = $connection->createCommand($sql);
        $command->bindValue(':param_reward_id', $param_reward_id);
        $command->bindValue(':param_name', $param_name);
        $command->bindValue(':param_description', $param_description);
        $command->bindValue(':param_is_active', $param_is_active);
        $command->bindValue(':param_reward_type_id', $param_reward_type_id);
        $command->bindValue(':param_reward_sub_cat', $param_reward_sub_cat);
        $command->bindValue(':param_created_by', $param_created_by);

        $command->bindValue(':param_emp_of_month', $emp_of_month);
        $command->bindValue(':params_resonse_time', $resonse_time);
        $command->bindValue(':params_erv_no', $erv_no);

        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut;
    }
    
     public function reward_list_for_approval($param_fla,$param_sla,$param_emp_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `reward_list_for_approval`(:param_fla,:param_sla,:param_emp_code)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_fla', $param_fla);
        $command->bindValue(':param_sla', $param_sla);
        $command->bindValue(':param_emp_code', $param_emp_code);
       
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
    
    /* Rewards module ended*/
    /* Recognition  module Started*/
    
    public function add_update_recognition($param_reco_id, $param_name,$param_description,$param_reco_type,$param_from_type,$param_from_department,$param_cash_amt,$param_is_active,$param_created_by){
        $connection=   Yii::$app->db;
        $connection->open();

       
        $sql =" CALL `add_update_recognition`(:param_reco_id, :param_name,:param_description,:param_reco_type,:param_from_type,:param_from_department,:param_cash_amt,:param_is_active,:param_created_by, @Result)";


        $command = $connection->createCommand($sql);
        $command->bindValue(':param_reco_id', $param_reco_id);
        $command->bindValue(':param_name', $param_name);
        $command->bindValue(':param_description', $param_description);        
        $command->bindValue(':param_reco_type', $param_reco_type);
        $command->bindValue(':param_from_type', $param_from_type);
        $command->bindValue(':param_from_department', $param_from_department);
        $command->bindValue(':param_cash_amt',$param_cash_amt);        
        $command->bindValue(':param_is_active', $param_is_active);
        $command->bindValue(':param_created_by', $param_created_by);
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut;
    }
            
    public function get_recognitions($param_reco_id=NULL,$param_emp_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_recognitions`(:param_reco_id,:param_emp_id)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_reco_id', $param_reco_id);
        $command->bindValue(':param_emp_id', $param_emp_id);
        if(!empty($param_reco_id)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
    
    public function recognition_list_check($param_fla,$param_sla){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `recognition_list_check`(:param_fla,:param_sla)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_fla', $param_fla);
        $command->bindValue(':param_sla', $param_sla);
       
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
    /* Recognition module ended*/


    public function add_update_policy_mst($param_pol_id, $param_pol_name, $param_is_active,$param_created_by){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_policy_mst`(:param_pol_id, :param_pol_name,:param_is_active,:param_created_by,   @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_pol_id', $param_pol_id); 
        $command->bindValue(':param_pol_name', $param_pol_name); 
        $command->bindValue(':param_is_active', $param_is_active);    
        $command->bindValue(':param_created_by', $param_created_by);    
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
    public function get_policy_master($param_pol_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_policy_master`(:param_pol_id)";        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_pol_id', $param_pol_id);
        if(!empty($param_pol_id)){
            $result=$command->queryAll();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
     public function add_update_policies($param_pol_id, $param_pol_name,$param_pol_desc,$param_pol_doc,$param_is_active,$param_valid_date,$param_created_by,$param_policy_id,$param_year){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_policies`(:param_pol_id, :param_pol_name, :param_pol_desc,:param_pol_doc,:param_is_active,:param_valid_date,:param_created_by, :param_policy_id,:param_year,@Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_pol_id', $param_pol_id); 
        $command->bindValue(':param_pol_name', $param_pol_name); 
        $command->bindValue(':param_pol_desc', $param_pol_desc); 
        $command->bindValue(':param_pol_doc', $param_pol_doc);
        $command->bindValue(':param_is_active', $param_is_active); 
        $command->bindValue(':param_valid_date', $param_valid_date);
        $command->bindValue(':param_created_by', $param_created_by); 
          $command->bindValue(':param_policy_id', $param_policy_id); 
          $command->bindValue(':param_year', $param_year); 
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
     public function get_policies_gui($param_pol_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_policies_gui`(:param_pol_id)";        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_pol_id', $param_pol_id);
        if(!empty($param_pol_id)){
            $result=$command->queryAll();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
    public function get_employee_list($param_fla_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_employee_list`(:param_fla_id)";        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_fla_id', $param_fla_id);
        if(!empty($param_pol_id)){
            $result=$command->queryAll();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;       
    }
     public function add_update_transfer_promotion($param_tra_id, $param_tra_title,$param_tra_remarks,$param_tra_emp,$param_tra_req,$param_tra_created){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_update_transfer_promotion`(:param_tra_id, :param_tra_title, :param_tra_remarks, :param_tra_emp, :param_tra_req,:param_tra_created,@Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_tra_id', $param_tra_id); 
        $command->bindValue(':param_tra_title', $param_tra_title); 
        $command->bindValue(':param_tra_remarks', $param_tra_remarks); 
        $command->bindValue(':param_tra_emp', $param_tra_emp);
        $command->bindValue(':param_tra_req', $param_tra_req); 
        $command->bindValue(':param_tra_created', $param_tra_created);        
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }
      public function get_transfer_promotion_details($param_fla_id,$param_sla_id,$param_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_transfer_promotion_details`(:param_fla_id,:param_sla_id,:param_id)";        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_fla_id', $param_fla_id);
           $command->bindValue(':param_sla_id', $param_sla_id);
            $command->bindValue(':param_id', $param_id);
      $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
     public function update_status_transfer_promotion($param_tra_id, $param_tra_status,$param_tra_emp,$param_comment){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `update_status_transfer_promotion`(:param_tra_id, :param_tra_status,:param_tra_emp,:param_comment, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_tra_id', $param_tra_id); 
        $command->bindValue(':param_tra_status', $param_tra_status); 
         $command->bindValue(':param_tra_emp', $param_tra_emp);
         $command->bindValue(':param_comment', $param_comment);             
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }

    public function getall_departments()
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_dept`(NULL)";
        // $sql =" select * from master_department where is_active='Y'";
        $command = $connection->createCommand($sql); 
        $valueOut = $command->queryAll();
        $connection->close();
        return $valueOut; 
    }
    public function getall_course_master()
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `tr_get_courses`(NULL)";
        // $sql =" select * from trng_course_master where active='1'";
        $command = $connection->createCommand($sql); 
        $valueOut = $command->queryAll();
        $connection->close();
        return $valueOut; 
    }

    public function getall_all_district_name($PARAM_venue_id)
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `trng_venues_districts`(:PARAM_venue_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':PARAM_venue_id', $PARAM_venue_id); 
        $valueOut = $command->queryAll();

        $connection->close();
        return $valueOut; 
    }

    public function getall_employeefortraining($PARAM_district_id,$PARAM_course_id,$PARAM_department_id,$PARAM_tpm_id)
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `trg_employee_list`(:PARAM_district_id,:PARAM_course_id,:PARAM_department_id,:PARAM_tpm_id)";

        /*$sql =" select * from trng_applied tp
                LEFT Join trng_training_program_master tpm on tp.tpm_id = tpm.tpm_id
                LEFT Join employee emp on tp.employee_code  = emp.employee_code
                where tp.District_id = '$District_id' and tpm.course_id = $Course_Id and tpm.department_id = $department_Id and tp.tpm_id='$tpm_id' order by applied_id ASC";*/
        $command = $connection->createCommand($sql); 
        $command->bindValue(':PARAM_district_id', $PARAM_district_id); 
        $command->bindValue(':PARAM_course_id', $PARAM_course_id); 
        $command->bindValue(':PARAM_department_id', $PARAM_department_id); 
        $command->bindValue(':PARAM_tpm_id', $PARAM_tpm_id); 
        $valueOut = $command->queryAll();
        $connection->close();
        return $valueOut; 
    }

    public function getall_traningprogramdata($PARAM_Course_Id,$PARAM_department_Id)
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `trg_get_program_detail`(:PARAM_Course_Id,:PARAM_department_Id)";

        // PARAM_Course_Id
        // PARAM_department_Id

        // $sql =" select * from trng_training_program_master tpm where tpm.course_id = $Course_Id and tpm.department_id = $department_Id";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':PARAM_Course_Id', $PARAM_Course_Id); 
        $command->bindValue(':PARAM_department_Id', $PARAM_department_Id); 
        $valueOut = $command->queryAll();
        $connection->close();
        return $valueOut; 
    }

    // getall_traningprogramdata

    // getall_employeefortraining
    // getall_course_master
    // training_batch_master

    public function gettrgvenues()
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `trng_venues`()";
        // $sql =" select * from trng_venues where is_active='1'";
        $command = $connection->createCommand($sql); 
        $valueOut = $command->queryAll();
        $connection->close();
        return $valueOut; 
    }


    public function assign_trng_grp($PARAMdepartment_Id,$PARAMCourse_Id,$PARAMGroupType,$PARAM_tpm_Id,$PARAM_venue_id,$PARAMstudentId_for_group,$PARAM_District_id)
    {
        $connection = Yii::$app->db;
        $connection->open();
        // $sql ="";
        $sql = "CALL `USP_InsertGroup`(:PARAMdepartment_Id,:PARAMCourse_Id,:PARAMGroupType,:PARAM_tpm_Id,:PARAM_venue_id,:PARAMstudentId_for_group,:PARAM_District_id,@Result)";
        
        /*echo "<pre>"; 
            print_r($valueOut);
        die();*/

        return $valueOut; 
    }

     public function get_addmore_leaves($param_id){

        $connection=   Yii::$app->db;
        $connection->open();

        // SET @run_balqty :=0
        Yii::$app->db->createCommand("SET @run_balqty :=null;")->execute();

        $sql =" CALL `hr_get_addmore_leave`(:param_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_id', $param_id);
        $result=$command->queryOne();
        
        
        $connection->close();
        return $result;       
    }

     public function add_more_leaves($param_id, $param_total,$param_bal){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `hr_addmore_leave_toemp`(:param_id, :param_total, :param_bal, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_id', $param_id); 
        $command->bindValue(':param_total', $param_total); 
        $command->bindValue(':param_bal', $param_bal); 
          
        $command->execute();
        $valueOut = $connection->createCommand("select @Result as ress;")->queryScalar();
        $connection->close();
        return $valueOut; 
    }

    public function get_employees_by_empcode($param_employee_code=NULL){ 
       // echo "sdfdsg"; die();
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_emp_by_empcode`(:param_employee_code)";
        
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $param_employee_code);
        if(!empty($param_employee_code)){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        //     echo "<pre>"; 
        //     print_r($result);
        // die();
        return $result;       
    }
    

    public function add_service_details($PARAM_emp_code,$PARAM_dept_id,$PARAM_designation_id,$PARAM_authority1,$PARAM_authority2){

        // echo "<pre>"; print_r($allData); die('yyyyyyyyyy=====');
        // $employee_code = $allData->employee_code;
        // $dept_id = $allData->dept_id;
        // $designation_id = $allData->designation_id;
        // $authority1 = $allData->authority1;
        // $authority2 = $allData->authority2;
        $updated_by = Yii::$app->user->identity->e_id;
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `add_services_by_emp`(:param_emp_code,:param_dept_id,:param_designation_id,:param_authority1,:param_authority2, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_emp_code', $PARAM_emp_code);
        $command->bindValue(':param_dept_id', $PARAM_dept_id);
        $command->bindValue(':param_designation_id', $PARAM_designation_id);
        $command->bindValue(':param_authority1', $PARAM_authority1);
        $command->bindValue(':param_authority2', $PARAM_authority2);
      
        
        $result = $command->execute();
        $connection->close();

        return $result;       
    }
     function get_employees_by_datetime($dbDate){
        $connection=   Yii::$app->db;
        $connection->open();
       
        $sql = "
         SELECT ahe.*,e.*,(SELECT desg_name FROM `master_designation` md WHERE md.desg_id=e.designation_id) as desg_name FROM employee ahe
        LEFT JOIN rbac_employee b ON (b.employee_code = ahe.employee_code)
        LEFT JOIN hr_service_details e ON (e.employee_code=ahe.employee_code)
        WHERE ahe.`created_date` >= '".$dbDate['date']."' AND ahe.org_id=1 ORDER BY `ahe`.`e_id` DESC;
        ";
       // die('<pre>'.$sql.'</pre>');
        $command = $connection->createCommand($sql);      
        $result=$command->queryAll();
        $connection->close();
        return $result;       


    }

    function getDepartmentDetails($did=NULL){
        $connection=   Yii::$app->db;
        $connection->open();
        if($did!=NULL){
            $sql ="SELECT dept_name FROM `master_department` WHERE dept_id=".$did;
        }else{
            $sql ="SELECT dept_name FROM `master_department`";
        }        
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();

        
        $connection->close();
       
        if(isset($result[0])){
            $arrRet = $result;
        }else{
            $arrRet = array();
        }
      
        return $arrRet;

    }

  function get_return_request_status($voucher_no,$employee_code)
  {
    $connection=   Yii::$app->db;
    $connection->open();
    
    $sql ="SELECT * FROM `store_material_issue_request_return` irr
          LEFT JOIN store_flag_status AS e ON e.Flag=irr.Flag
          WHERE voucher_no='".$voucher_no."' and employee_code='".$employee_code."'" ;
            
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();

    
    $connection->close();
   
    if(isset($result[0])){
        $arrRet = $result[0];
    }else{
        $arrRet = array();
    }
  
    return $arrRet;
  }



  function get_return_request_data_status($voucher_no,$employee_code)
  {
    /*$connection=   Yii::$app->db;
    $connection->open();
    
    $sql ="SELECT * FROM `store_material_issue_request_return` irr
          LEFT JOIN store_flag_status AS e ON e.Flag=irr.Flag
          WHERE voucher_no='".$voucher_no."' and employee_code='".$employee_code."'" ;
            
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();

    
    $connection->close();
   
    if(isset($result[0])){
        $arrRet = $result[0];
    }else{
        $arrRet = array();
    }
  
    return $arrRet;*/


      $connection=   Yii::$app->db;
      $connection->open();
      $sql =" CALL `store_get_return_request_data_status`(:voucher_no,:employee_code)";
      
      $command = $connection->createCommand($sql); 
      $command->bindValue(':voucher_no', $voucher_no);
      $command->bindValue(':employee_code', $employee_code);
      $result=$command->queryOne();
      $connection->close();
      //     echo "<pre>"; 
      //     print_r($result);
      // die();
      return $result;       
  }


    function getUnitDetails($uid=NULL){
        $connection=   Yii::$app->db;
        $connection->open();
        if($uid!=NULL){
            $sql ="SELECT unit_location,unit_location_code FROM `emp_dept_units` WHERE id=".$uid;
        }else{
            $sql ="SELECT unit_location,unit_location_code FROM `emp_dept_units`";
        }        
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();
        $connection->close();
        return $result[0];

    }

    //WEB SERVICES

    function ervApiCall($agentID){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT *  FROM `erv_api_details` WHERE `agentId` LIKE '".$agentID."'";
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();
        $connection->close();
        //pr($result);die;
        if(isset($result[0])){
            return $result[0];
        }else{
            return $result;
        }
        
    }

    function convertmilliDate($mil){
        //$mil = 1227643821310;
        $seconds = $mil / 1000;
        return date("F j, Y, g:i a", $seconds);
    }
    function tr_get_upcoming_courses(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="CALL `tr_get_upcoming_courses`(NULL)";
        // $sql =" select * from trng_course_master where active='1'";
        $command = $connection->createCommand($sql); 
        $valueOut = $command->queryAll();
        $connection->close();
        return $valueOut;
    }

    function get_eom_data(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM `reward_master` WHERE `is_active`='Y'";
        $command = $connection->createCommand($sql); 
        $arrRecs = $command->queryAll();
        $connection->close();
        return $arrRecs;
    }

     function get_success_stories(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT DISTINCT(`description`) FROM `reward_master` WHERE `reward_type_id`='5' AND `is_active`='Y' ";
        $command = $connection->createCommand($sql); 
        $arrRecs = $command->queryAll();
        $connection->close();
        return $arrRecs;
    }

    function update_eom_data($bestco,$bestdo,$besterv,$filepath,$isActive,$valid){
        $connection=   Yii::$app->db;
        $connection->open();
        
        $sql ="UPDATE `employeeofmonth` SET `bestco`='".$bestco."',`bestdo`='".$bestdo."', `besterv`='".$besterv."',`is_active`='".$isActive."',`valid_upto`='".$valid."',`document`='".$filepath."'
        WHERE `id` = 1;";
        
        $command = $connection->createCommand($sql); 
        $ret = $command->execute();
        $connection->close();
        return $ret;
    }

    function add_eom_data($bestco,$bestdo,$besterv,$filepath,$isActive,$valid){
        $connection=   Yii::$app->db;
        $connection->open();
        
        $sql ="INSERT INTO `employeeofmonth` (`bestco`, `bestdo`, `besterv`, `document`, `is_active`, `sdate`) VALUES 
        ('".$bestco."','".$bestdo."','".$besterv."','".$filepath."','Y', '".$valid."');";
        $command = $connection->createCommand($sql); 
        $ret = $command->execute();
        $connection->close();
        return $ret;
    }

    function get_employee_of_month(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM employeeofmonth";
        // $sql =" select * from trng_course_master where active='1'";
        $command = $connection->createCommand($sql); 
        $valueOut = $command->queryAll();
        $connection->close();
        return $valueOut;

    }

    //---------------
    public function getDistUnits(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM `view_unit_list`";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return @$result; 
    }
    //---------------

    function erv_stf_dist($unitname){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="
        SELECT DISTINCT(SELECT COUNT(erv_jobprofileinharyana112) FROM erv_employee WHERE erv_jobprofileinharyana112='ERV INCHARGE' AND erv_district_unit='".$unitname."') as t_inch,
        (SELECT COUNT(erv_jobprofileinharyana112) FROM erv_employee WHERE erv_jobprofileinharyana112 LIKE '%ERV INCHARGE BUFFER%' AND erv_district_unit='".$unitname."') as t_inch_buf,
        (SELECT COUNT(erv_jobprofileinharyana112) FROM erv_employee WHERE erv_jobprofileinharyana112 LIKE '%ERV SUPPORTING STAFF%' AND erv_district_unit='".$unitname."') as t_support_staff,
        (SELECT COUNT(erv_jobprofileinharyana112) FROM erv_employee WHERE erv_jobprofileinharyana112 LIKE '%ERV SUPPORTING STAFF BUFFER%' AND erv_district_unit='".$unitname."') as t_support_staff_buffer,
        (SELECT COUNT(erv_jobprofileinharyana112) FROM erv_employee WHERE erv_jobprofileinharyana112 LIKE '%ERV DRIVER%' AND erv_district_unit='".$unitname."') as t_driver,
        (SELECT COUNT(erv_jobprofileinharyana112) FROM erv_employee WHERE erv_jobprofileinharyana112 LIKE '%ERV DRIVER BUFFER%' AND erv_district_unit='".$unitname."') as t_driver_buffer
        FROM `erv_employee`  GROUP BY erv_district_unit='".$unitname."';
        ";


       
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return @$result[0]; 
    }

	public function get_hp_dept(){

			$connection=   Yii::$app->db;
			$connection->open();
			$sql ="SELECT * FROM `master_department` WHERE dept_id IN (43,53,83,49,82,87,89,104,107,45)  
			ORDER BY `master_department`.`dept_name`  ASC";
			// $sql =" select * from trng_course_master where active='1'";
			$command = $connection->createCommand($sql); 
			$result = $command->queryAll();
			$connection->close();
			return $result;
	}

	public function get_cdac_dept(){

			$connection=   Yii::$app->db;
			$connection->open();
			$sql ="SELECT * FROM `master_department` WHERE dept_id IN (101)  
			ORDER BY `master_department`.`dept_name`  ASC";
			// $sql =" select * from trng_course_master where active='1'";
			$command = $connection->createCommand($sql); 
			$result = $command->queryAll();
			$connection->close();
			return $result;
	}

	public function get_fms_dept(){
			$connection=   Yii::$app->db;
			$connection->open();
			$sql ="SELECT * FROM `master_department` WHERE dept_id IN (102,88,84)  
			ORDER BY `master_department`.`dept_name`  ASC";
			// $sql =" select * from trng_course_master where active='1'";
			$command = $connection->createCommand($sql); 
			$result = $command->queryAll();
			$connection->close();
			return $result;
	}

	public function get_bms_dept(){
			$connection=   Yii::$app->db;
			$connection->open();
			$sql ="SELECT * FROM `master_department` WHERE dept_id IN (106)  
			ORDER BY `master_department`.`dept_name`  ASC";
			// $sql =" select * from trng_course_master where active='1'";
			$command = $connection->createCommand($sql); 
			$result = $command->queryAll();
			$connection->close();
			return $result;
	}

    public function get_outer_dept($id){
            $connection=   Yii::$app->db;
            $connection->open();
            $sql ="SELECT * FROM `master_department` WHERE dept_id IN (".$id.")  
            ORDER BY `master_department`.`dept_name`  ASC";
            // $sql =" select * from trng_course_master where active='1'";
            $command = $connection->createCommand($sql); 
            $result = $command->queryAll();
            $connection->close();
            return $result;
    }

	public function get_all_erv_employees(){
			$connection=   Yii::$app->db;
			$connection->open();
			$sql ="SELECT e.employee_code,e.fname,e.lname,eemp.erv_rank,eemp.erv_beltnumer,eemp.erv_district_unit,eemp.erv_jobprofileinharyana112,(SELECT count(*) as flg FROM erv_api_details as ed WHERE e.employee_code=ed.agentId) as dep_status FROM erv_employee eemp
			LEFT JOIN employee e ON (eemp.employee_code = e.employee_code);
			;";
			// $sql =" select * from trng_course_master where active='1'";
			$command = $connection->createCommand($sql); 
			$result = $command->queryAll();
			$connection->close();
			return $result;
	
	}

	public function get_all_trained_employees($loc,$ttype,$jprofile){
			$connection=   Yii::$app->db;
			$connection->open();
			$sql ="SELECT e.employee_code,e.fname,e.lname,eemp.erv_rank,eemp.erv_beltnumer,eemp.erv_district_unit,eemp.erv_jobprofileinharyana112,erv_trainingcentre,(SELECT count(*) as flg FROM erv_api_details as ed WHERE e.employee_code=ed.agentId) as dep_status FROM erv_employee eemp
			LEFT JOIN employee e ON (eemp.employee_code = e.employee_code);
			;";
			// $sql =" select * from trng_course_master where active='1'";
			$command = $connection->createCommand($sql); 
			$result = $command->queryAll();
			$connection->close();
			return $result;
	
	}


	public function get_ervdeployed_emp($depid){			
	
			$connection=   Yii::$app->db;
			$connection->open();

			if($depid=='2'){
				$sql ="SELECT * FROM harayana_erv_deployed_staff WHERE dep_status='1';";				
			}else{
				$sql ="SELECT e.employee_code,e.fname,e.lname,eemp.erv_rank,eemp.erv_beltnumer,eemp.erv_district_unit,eemp.erv_jobprofileinharyana112,(SELECT count(*) as flg FROM erv_api_details as ed WHERE e.employee_code=ed.agentId) as dep_status FROM erv_employee eemp
				LEFT JOIN employee e ON (eemp.employee_code = e.employee_code);";			
			}
			
			// $sql =" select * from trng_course_master where active='1'";
			$command = $connection->createCommand($sql); 
			$result = $command->queryAll();
			$connection->close();
			return $result;
	
	}

    public function trng_get_total_trgcentercount($city){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT COUNT(*) as Total  FROM `erv_employee` WHERE `erv_trainingcentre` LIKE '%".$city."%'";
        // $sql =" select * from trng_course_master where active='1'";
        $command = $connection->createCommand($sql); 
        $result = $command->queryAll();
        $connection->close();
        return $result[0]['total'];
    }

    function logincheck(){
        
    }

    function checkDeployedERV($ecod){

            $connection=   Yii::$app->db;
			$connection->open();
			$sql ="SELECT COUNT(*) as terv FROM `erv_employee` WHERE employee_code LIKE '".$ecod."';";
			// $sql =" select * from trng_course_master where active='1'";
			$command = $connection->createCommand($sql); 
			$result = $command->queryAll();
			$connection->close();
			return $result;        
    }

    public function api_get_all_erv_employees($paramsemp){


                $fname=NULL;
                $lname=NULL;
                $location=NULL;
                $designation=NULL;
                $str='';
                $connection=   Yii::$app->db;
                $connection->open();
                if(!empty($paramsemp['fname']))
                $fname = trim($paramsemp['fname']);
                if(!empty($paramsemp['lname']))
                $lname = trim($paramsemp['lname']);
                if(!empty($paramsemp['posting']))
                $location = trim($paramsemp['posting']);
                if(!empty($paramsemp['jobprofile']))
                $jobprofile = trim($paramsemp['jobprofile']);       
                // print_r($_REQUEST);


                $str =array();
                if(!empty($fname))        
                $str[] = "`fname` LIKE '".$fname."'";
                if(!empty($lname))
                $str[] = "`lname` LIKE '".$lname."'";
                if(!empty($location))
                $str[]  = "`erv_district_unit`='".$location."'";
                if(!empty($designation))
                $str[] = "eemp.erv_jobprofileinharyana112 LIKE '".$jobprofile."'";
                $text='';


                
                foreach($str as $k=>$s){
                if($k==0){
                    $text = $s;
                }else{                
                    $text = $text."  AND ".$s;            
                }

                }
                // echo $text;
       
            $connection=   Yii::$app->db;
            $connection->open();
            /*
            $sql ="SELECT e.employee_code,e.fname,e.lname,eemp.erv_rank,eemp.erv_beltnumer,eemp.erv_district_unit,eemp.erv_jobprofileinharyana112,(SELECT count(*) as flg FROM erv_api_details as ed WHERE e.employee_code=ed.agentId) as dep_status FROM erv_employee eemp
            LEFT JOIN employee e ON (eemp.employee_code = e.employee_code);
            ";*/


                $sql = "
           SELECT e.employee_code,e.fname,e.lname,e.location,eemp.erv_rank,eemp.erv_beltnumer,eemp.erv_district_unit,eemp.erv_jobprofileinharyana112,(SELECT count(*) as flg FROM erv_api_details as ed WHERE e.employee_code=ed.agentId) as dep_status FROM erv_employee eemp LEFT JOIN employee e ON (eemp.employee_code = e.employee_code)";
           
           if(!empty($paramsemp)){
                    $sql .= "
                WHERE 1=1
                and (".$text.")";

           }
             
            
            //die($sql);
            $command = $connection->createCommand($sql); 
            $result = $command->queryAll();
            $connection->close();
            return $result;
    

    }

    public function get_issue_req_dashboard($param_fla_id,$param_role_id)
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_issue_req_dashboard`(:param_fla_id,:param_role_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_fla_id', $param_fla_id);
        $command->bindValue(':param_role_id', $param_role_id);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }

    public function get_return_issue_req_dashboard($param_fla_id,$param_role_id)
    {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_return_issue_req_dashboard`(:param_fla_id,:param_role_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_fla_id', $param_fla_id);
        $command->bindValue(':param_role_id', $param_role_id);
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }

    public function get_erss_jobprofile(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM `master_erv_job_profile`";
        $command = $connection->createCommand($sql); 
        $result = $command->queryAll();
        $connection->close();

        return $result;
    }

     public function get_erss_placeofposting(){

        $type = Yii::$app->utility->get_unit_list();
        $i=0;
        $list = array();
        foreach($type as $key=>$val){
            //$id = Yii::$app->utility->encryptString($val);
            //$id = base64_encode($val);
            $list[$i]['id']=$val['location'];
            $list[$i]['location']=$val['location'];
            $i++;
        }
        return $list;

    }

    public function get_employees_adinfo($e_id){

        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT employement_status,erss_job_profile,erv_deployed,offclocation  FROM `employee` WHERE `employee_code` LIKE '".$e_id."'";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result[0];

    }

    public function get_emp_counter($org_type){

        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT COUNT(*) as total_emp FROM employee WHERE `is_active` = 'Y' AND `org_id` = $org_type";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result[0];



    }


    public function get_bms_counter(){

        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT COUNT(*) as total_emp FROM employee WHERE `employee_code` IN (SELECT `employee_code` FROM `hr_service_details` WHERE `dept_id` IN ('106'));";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result[0];
    }

    public function get_fms_counter(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT COUNT(*) as total_emp FROM employee WHERE `employee_code` IN (SELECT `employee_code` FROM `hr_service_details` WHERE `dept_id` IN ('102','84','88'));";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result[0];
    }



    public function get_firedpt_counter(){
        $connection=   Yii::$app->db;
        $connection->open();
         $sql ="SELECT count(*)  as total_emp FROM `employee` WHERE `employee_code` LIKE '%FIR%' ORDER BY `e_id`  DESC";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result[0];
    }

    public function get_healthdpt_counter(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT count(*)  as total_emp FROM `employee` WHERE `employee_code` LIKE '%HEALTH%' ORDER BY `e_id`  DESC";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result[0];
    }


    public function update_hr_service_details($PARAM_emp_code,$PARAM_dept_id,$PARAM_designation_id,$PARAM_authority1,$PARAM_authority2){

        
        $updated_by = Yii::$app->user->identity->e_id;
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `update_services_by_emp`(:param_emp_code,:param_dept_id,:param_designation_id,:param_authority1,:param_authority2, @Result)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_emp_code', $PARAM_emp_code);
        $command->bindValue(':param_dept_id', $PARAM_dept_id);
        $command->bindValue(':param_designation_id', $PARAM_designation_id);
        $command->bindValue(':param_authority1', $PARAM_authority1);
        $command->bindValue(':param_authority2', $PARAM_authority2);
      
        
        $result = $command->execute();
        $connection->close();

        return $result;       
    }
     public function delete_reward_emp($rewardid){

        $connection = Yii::$app->db;
        $connection->open();
        $sql ="DELETE FROM `reward_master` WHERE `reward_master`.`id` = ".$rewardid;
        $command = $connection->createCommand($sql); 
         $result = $command->execute();
        $connection->close();
        return $result;    

    }
    
    public function get_rewards_type_info($rid){
        $connection = Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM `reward_type` WHERE `reward_type`.`id` = ".$rid;
        $command = $connection->createCommand($sql);
        $result=$command->queryOne();
        $connection->close();
        return $result;

    }

    public function saverewrdsdoc($arrDoc){

        $connection=   Yii::$app->db;
        $connection->open();

        $sql ="INSERT INTO `reward_document` (`doc_type`, `doc_name`, `doc_url`,`is_download_allow`) VALUES ('".$arrDoc['doc_type']."','".$arrDoc['doc_name']."','".$arrDoc['doc_url']."','".$arrDoc['is_download_allow']."');";
        $command = $connection->createCommand($sql); 
        $ret = $command->execute();
        $connection->close();
        return $ret;

    }

    public function getbirthdayemp(){

        $connection = Yii::$app->db;
        $connection->open();
        $today = date('m-d');
        $sql ="SELECT * FROM `employee` WHERE `dob` LIKE '%".$today."' ORDER BY `dob` ASC ";

        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result;

    }

function get_erv_employees_by_datetime($dbDate){
        $connection=   Yii::$app->db;
        $connection->open();
       
        $sql = "
        SELECT ahe.*,b.*,e.*,(SELECT desg_name FROM `master_designation` md WHERE md.desg_id=e.designation_id) as desg_name FROM employee ahe
        LEFT JOIN rbac_employee b ON (b.employee_code = ahe.employee_code)
        LEFT JOIN hr_service_details e ON (e.employee_code=ahe.employee_code)
        WHERE ahe.`created_date` >= '".$dbDate['date']."' AND ahe.`erv_deployed`='Y' AND ahe.org_id=1 ORDER BY `ahe`.`e_id` DESC;
        ";
        //die('<pre>'.$sql.'</pre>');
        $command = $connection->createCommand($sql);      
        $result=$command->queryAll();
        $connection->close();
        return $result;       


    }
    public function get_transfer_promotion_history($param_tra_id){

        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_transfer_promotion_history`(:param_tra_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_tra_id', $param_tra_id);
       
        $result=$command->queryAll();
        $connection->close();
        return $result;  

    }

    public function get_rewrds_dept(){

            $connection=   Yii::$app->db;
            $connection->open();
            $sql ="SELECT * FROM `master_department` WHERE dept_id IN (43,53,83,49,82,87,89,104,107,45,101,102,88,84)
            ORDER BY `master_department`.`dept_name`  ASC";
            // $sql =" select * from trng_course_master where active='1'";
            $command = $connection->createCommand($sql);
            $result = $command->queryAll();
            $connection->close();
            return $result;
    }
	/*Dharamveer Code Here 04-10-2022*/
	public function addnotication($arr){
		$connection=   Yii::$app->db;
		$connection->open();
		$sql ="INSERT INTO `notification_tbl` (`notificationNo`,`notimsg`, `created_by`) 
		VALUES ('".$arr['notificationNo']."','".$arr['notimsg']."','".$arr['created_by']."')";
		$command = $connection->createCommand($sql); 
		$ret = $command->execute();
		$connection->close();
		return $ret;
  }
  
  
  public function getdataall($table){

            $connection=   Yii::$app->db;
            $connection->open();
            $sql ="SELECT * FROM  ".$table." order by id DESC";
            // $sql =" select * from trng_course_master where active='1'";
            $command = $connection->createCommand($sql);
            $result = $command->queryAll();
            $connection->close();
            return $result;
    }
	
	function getnotification(){
        $connection=   Yii::$app->db;
        $connection->open();
		$sql ="SELECT * FROM notification_tbl WHERE `created_date` >= NOW() - INTERVAL 10 DAY AND `created_date`  < NOW();";
        // $sql =" select * from trng_course_master where active='1'";
        $command = $connection->createCommand($sql); 
        $valueOut = $command->queryAll();
        $connection->close();
        return $valueOut;

    }
	public function get_notificationdetails($notiid){
    $connection = Yii::$app->db;
    $connection->open();
    $sql="SELECT * FROM `notification_tbl` WHERE `id`=".$notiid;
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result;
	}
	/*Dharamveer Code Here*/
   
   
   
   public function get_driving_license($param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql = "Select license_no,fname,lname,license_expired_date from employee where employee_code = '$param_employee_code' AND license_expired_date IS NOT NULL;";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }
	public function get_bank_details($param_employee_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql = "Select * from bank_details where employee_code = '".$param_employee_code."'";
        $command = $connection->createCommand($sql); 
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }

    public function send_mail($email,$message, $subject)
    {
        try {

            $headers = '';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
            require_once './PHPMailer/PHPMailerAutoload.php';
            $mail = new \PHPMailer;
            $mail->SMTPDebug  = 0;
            $mail->isSMTP();
            $mail->Host = MAIL_HOST;                                      // Specify main and backup SMTP servers
            $mail->SMTPAuth = SMTP_AUTH;
            $mail->SMTPSecure = SMTP_PROTOCAL;                              // Enable SMTP authentication
            $mail->Username = MAIL_USERNAME;                              // SMTP username
            $mail->Password = MAIL_PASSWORD;                        // SMTP password
            $mail->Port = MAIL_PORT;                                    // TCP port to connect to
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(MAIL_FROM);
            $mail->Subject = $subject;
            $mail->Body = $message;
          
            foreach ($email as $id => $emailId) {
                $mail->addAddress($emailId);
            }
            $mail->SMTPOptions = array('ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => false
            ));
            $mail->send();
            return true;
        } catch (Exception $ex) {
            throw new Exception(500, $ex);
        }
    }
    public function get_emp_code_by_grievience_id($id)
    {
        $connection =   Yii::$app->db;
        $connection->open();
        $sql = "Select createdby from grievance where id = ".$id ."";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        $connection->close();
        return $result;
    }

    public function get_grievience_officer_emp_id()
    {
        $connection =   Yii::$app->db;
        $connection->open();
        $sql = "Select employee_code from rbac_employee_role where role_id = 26";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        $connection->close();
        return $result;
    }

    public function view_all_co_feeback($month_val){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT rco.*,e.fname,e.lname FROM `reward_cofeedback` rco 
        LEFT JOIN employee e ON (e.employee_code=rco.employee_code)
        WHERE rco.`Month`='".$month_val."'
    
        ";
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();
        $connection->close();
        return $result; 
      }


}
