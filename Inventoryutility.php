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
class Inventoryutility extends Component{
    
    public function get_projects($project_id=NULL){
        $connection=   Yii::$app->db;
        $connection->open();
        $dept_id=Yii::$app->user->identity->dept_id;
        $sql =" CALL `pr_get_projects`(:dept_id, :project_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':dept_id', $dept_id);
        $command->bindValue(':project_id', $project_id);
        if($project_id){
            $result=$command->queryOne();
        }else{
            $result=$command->queryAll();
        }
        $connection->close();
        return $result;
    }
    
  public function get_empname($eid){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="CALL `get_employees`(:param_employee_code)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_employee_code', $eid);
        $result=$command->queryOne();
        $connection->close();
        if(empty($result)){
            $sql ="SELECT fname FROM employee WHERE employee_code=".$eid;
            $command = $connection->createCommand($sql); 
            $result=$command->queryOne();
            return $result['fname'];       
        }else{
            return $result['fname'];

        }   
    }

  public function get_allStockData($param_CLASSIFICATION_CODE,$param_ITEM_CAT_CODE){
    $connection=   Yii::$app->db;
    $connection->open();
    //  die($param_ITEM_CAT_CODE);
    $sql ="CALL `get_all_stock_data`(:param_CLASSIFICATION_CODE,:param_ITEM_CAT_CODE)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_CLASSIFICATION_CODE', $param_CLASSIFICATION_CODE);
    $command->bindValue(':param_ITEM_CAT_CODE', $param_ITEM_CAT_CODE);
    $result=$command->queryAll();
    $connection->close();
    //pr($result);die;
    return $result;   
  }

  public function get_issued_qty($param_item_code){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_issued_qty`(:param_item_code)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_item_code', $param_item_code);
    $result=$command->queryOne();
    $connection->close();
    //print_r($result);die;
    return $result;   
  } 
  


  

   
public function get_empdept($eid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_employees`(:param_employee_code)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_employee_code', $eid);
    $result=$command->queryOne();
    $connection->close();
    // echo'<pre>'; print_r($result);die;
    return $result['dept_name'];   
}
public function get_alldept(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_dept`('')";
    $command = $connection->createCommand($sql); 
    $result=$command->queryAll();
    $connection->close();
    return $result;   
}
public function get_all_supplier(){
    /*$connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_supplier`(0,0)";
    $command = $connection->createCommand($sql); 
    $result=$command->queryAll();
    $connection->close();*/

    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_supplier`(:PARAM_ITEM_CAT_CODE,:PARAM_supplier_code)";
    $command = $connection->createCommand($sql); 

    $command->bindValue(':PARAM_ITEM_CAT_CODE', 0);
    $command->bindValue(':PARAM_supplier_code', 0);
    $result=$command->queryAll();
    $connection->close();
    
    return $result;   
}
public function get_supplier_list(){       

    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_supplier_list`()";
    $command = $connection->createCommand($sql);        
    $result=$command->queryAll();
    $connection->close();        
    return $result;   
}

public function get_dept_emp($deptid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_dept_emp`(:param_dept)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_dept', $deptid);
    $result=$command->queryAll();
    $connection->close();
    return $result;   
}

public function get_dept_dsg_emp($deptid,$dsgid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_dept_dsg_emp`(:param_dept,:param_dsgid)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_dept', $deptid);
    $command->bindValue(':param_dsgid', $dsgid);
    $result=$command->queryAll();
    $connection->close();
    return $result;   
}


public function get_groups(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_groups`()";
    $command = $connection->createCommand($sql); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}
public function get_category(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_category`()";
    $command = $connection->createCommand($sql); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function get_item_type(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_item_type`()";
    $command = $connection->createCommand($sql); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function get_cost_centre($deptid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_cost_centre`(:param_dept)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_dept', $deptid);
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

/////////////  Master Data ////////////////// Amarpreet Kaur //////////////////////////////////////////////////////////////////////////

public function get_cat_item($cat_code,$class_code){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="CALL `store_get_cat_item`(:cat_code,:class_code)";
    $command = $connection->createCommand($sql);
    $command->bindValue(':cat_code', $cat_code);
    $command->bindValue(':class_code', $class_code);                
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}
// Comparision Report Data 

public function get_comparison_items($cat_code,$class_code){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="
    SELECT itm_id,ITEM_CODE,item_name,Quantity,tm.Type_id,tm.Item_type,um.Unit_Name Measuring_Unit,CLASSIFICATION_NAME,ITEM_CAT_NAME
            FROM `store_item_master` i
            LEFT JOIN store_classification_master cl ON (cl.CLASSIFICATION_CODE=i.CLASSIFICATION_CODE)
            LEFT JOIN store_item_cat_master c ON (c.ITEM_CAT_CODE=i.ITEM_CAT_CODE)
            LEFT JOIN store_item_type_master tm ON (tm.Type_id =i.Item_type1)
            LEFT JOIN store_unit_master um ON (um.Unit_id =i.Measuring_Unit)
            WHERE i.is_active='Y' AND i.ITEM_CODE=".$cat_code." AND i.CLASSIFICATION_CODE=".$class_code." order by item_name;
    ";
    //die($sql);
    $command = $connection->createCommand($sql);
    $command->bindValue(':cat_code', $cat_code);
    $command->bindValue(':class_code', $class_code);                
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}
// actionGet_item_detail_rec($itemcode)

public function actionGet_item_detail_rec($Param_itemcode){

      $connection = Yii::$app->db;
      $connection->open();
      // $sql="SELECT * FROM `store_item_details` WHERE item_code = $Param_itemcode";
      $sql="CALL `store_get_item_details`(:Param_itemcode)";
      $command = $connection->createCommand($sql);
      $command->bindValue(':Param_itemcode', $Param_itemcode);
      $result=$command->queryAll();
      $connection->close();
      return $result;       
      
}

public function get_store_allitems_detailsbyid($Param_itemcode){
    $connection = Yii::$app->db;
    $connection->open();
    $sql="SELECT * FROM `store_item_details` WHERE item_code = $Param_itemcode";
    $command = $connection->createCommand($sql);
    $command->bindValue(':Param_itemcode', $Param_itemcode);
    $result=$command->queryAll();
    $connection->close();
    return $result; 

}

public function get_item_detail_for_issue($Param_itemcode){
      $connection = Yii::$app->db;
      $result = array();
      $connection->open();
      // $sql="SELECT * FROM `store_item_details` WHERE item_code = $Param_itemcode";

      $sql="CALL `store_get_item_details_for_issue`(:Param_itemcode)";
      //$sql="CALL `store_get_all_item_details_for_issue`(:Param_itemcode)";


      $command = $connection->createCommand($sql);
      $command->bindValue(':Param_itemcode', $Param_itemcode);
      $result=$command->queryAll();
      $connection->close();

     



  return $result;       
}


public function actionGet_initiate_item_detail($Param_itemcode,$Param_emp_code,$Param_voucher_no){
  $connection = Yii::$app->db;
      $connection->open();
      // $sql="SELECT * FROM `store_item_details` WHERE item_code = $Param_itemcode";

      $sql="CALL `store_get_initiate_issue_item_details`(:Param_itemcode,:Param_emp_code,:Param_voucher_no)";


      $command = $connection->createCommand($sql);
      $command->bindValue(':Param_itemcode', $Param_itemcode);
      $command->bindValue(':Param_emp_code', $Param_emp_code);
      $command->bindValue(':Param_voucher_no', $Param_voucher_no); 
      $result=$command->queryAll();
      $connection->close();
  return $result;       
}
public function actionGet_item_detail($Param_itemcode,$Param_emp_code,$Param_voucher_no){
  $connection = Yii::$app->db;
      $connection->open();
      // $sql="SELECT * FROM `store_item_details` WHERE item_code = $Param_itemcode";

      $sql="CALL `store_get_issue_item_details`(:Param_itemcode,:Param_emp_code,:Param_voucher_no)";


      $command = $connection->createCommand($sql);
      $command->bindValue(':Param_itemcode', $Param_itemcode);
      $command->bindValue(':Param_emp_code', $Param_emp_code);
      $command->bindValue(':Param_voucher_no', $Param_voucher_no); 
      $result=$command->queryAll();
      $connection->close();
  return $result;       
}

public function actionGet_return_item_detail($Param_itemcode,$Param_emp_code,$Param_voucher_no){
  $connection = Yii::$app->db;
      $connection->open();
      // $sql="SELECT * FROM `store_item_details` WHERE item_code = $Param_itemcode";

      $sql="CALL `store_get_return_item_details`(:Param_itemcode,:Param_emp_code,:Param_voucher_no)";


      $command = $connection->createCommand($sql);
      $command->bindValue(':Param_itemcode', $Param_itemcode);
      $command->bindValue(':Param_emp_code', $Param_emp_code);
      $command->bindValue(':Param_voucher_no', $Param_voucher_no); 
      $result=$command->queryAll();
      $connection->close();
  return $result;       
}



public function store_insert_Item_details($data){

    if(empty($data['expiry_date_maint'])){
        $data['expiry_date_maint']=NULL;
    }
    if(empty($data['is_item_defactive'])){
        $data['is_item_defactive']='N';
    }
    if(empty($data['item_store_id'])){
        $data['item_store_id']=0;
    }
     if(empty($data['rack_id'])){
        $data['rack_id']=0;
    }
    extract($data);
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_insert_Item_details`(:PARAM_CLASSIFICATION_CODE,:PARAM_ITEM_CAT_CODE,:PARAM_item_name,:PARAM_item_type,:PARAM_item_unit,:PARAMS_exp_date,:PARAMS_is_annual,:PARAMS_is_item_defactive,:PARAMS_item_store_id,:PARAMS_rack_id,:PARAMS_item_safe_count,@Result)";
    $command=$connection->createCommand($sql);
   

    $command->bindValue(':PARAM_CLASSIFICATION_CODE', $Classification_Code);
    $command->bindValue(':PARAM_ITEM_CAT_CODE', $Item_Cat_Code);
    $command->bindValue(':PARAM_item_name', $item_name);
    $command->bindValue(':PARAM_item_type', $Item_type);
    $command->bindValue(':PARAM_item_unit', $Measuring_Unit);
    $command->bindValue(':PARAMS_exp_date', $expiry_date_maint);
    $command->bindValue(':PARAMS_is_annual', $is_annual_maint);
    $command->bindValue(':PARAMS_is_item_defactive',$is_item_defactive);
    $command->bindValue(':PARAMS_item_store_id', $item_store_id);
    $command->bindValue(':PARAMS_rack_id', $rack_id);
    $command->bindValue(':PARAMS_item_safe_count', $item_safe_count);
   	$command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;
}

public function get_unit_master(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_unit_master`()";
    $command = $connection->createCommand($sql); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function get_store_loc(){
    $connection = Yii::$app->db;
    $connection->open();
    $sql="SELECT * FROM `master_store_location`";
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result;    

}

public function get_rack_loc(){
        $connection = Yii::$app->db;
        $connection->open();
        $sql="SELECT * FROM `store_rack_location` WHERE `is_available`='Y'";
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();
        $connection->close();
        return $result;    

}


public function add_unit_master($data){
    extract($data);
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_insert_Unit_details`(:Param_Unit_Name,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':Param_Unit_Name', $Unit_Name);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function store_get_supplier($PARAM_ITEM_CAT_CODE, $PARAM_supplier_code){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_supplier`(:PARAM_ITEM_CAT_CODE, :PARAM_supplier_code)";
    // $sql =" CALL `store_get_supplier`(:PARAM_ITEM_CAT_CODE, :PARAM_supplier_code, @Result)";
    $command = $connection->createCommand($sql); 
            $command->bindValue(':PARAM_ITEM_CAT_CODE', $PARAM_ITEM_CAT_CODE);
            $command->bindValue(':PARAM_supplier_code', $PARAM_supplier_code);
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function add_store_insert_Supplier_details($data){
    extract($data);
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_insert_Supplier_details`(:Param_supplier_name,:Param_supplier_address,:Param_supplier_phone_no,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':Param_supplier_name', $Supplier_name);
    $command->bindValue(':Param_supplier_address', $Supplier_address);
    $command->bindValue(':Param_supplier_phone_no', $Phone_no);    
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function get_supplier_data_byid($PARAM_supplier_code){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT * FROM store_supplier_master WHERE `Supplier_Code`='".$PARAM_supplier_code."'";
    
    $command = $connection->createCommand($sql); 
    $result=$command->queryOne();
    $connection->close();
    return $result;       
}

public function update_store_insert_Supplier_details($data){
    extract($data);
   
    $connection=   Yii::$app->db;
    $connection->open();    
    $sql ="UPDATE store_supplier_master SET 
    `Supplier_name`='".$data['Supplier_name']."',
    `Supplier_address`='".$data['Supplier_address']."',
    `Phone_no`='".$data['Phone_no']."'
     WHERE `Supplier_code`= '".$data['Supplier_code']."'";
    $command=$connection->createCommand($sql);     
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function delete_Supplier_details($data){
    //pr($data);die;
    $connection=   Yii::$app->db;
    $connection->open();    
    $sql ="UPDATE store_supplier_master SET 
    `is_active`='N'  
     WHERE `Supplier_code`= '".$data['Supplier_code']."'";
    $command=$connection->createCommand($sql);     
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;     

}

public function add_group_master($data){
    extract($data);
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_insert_classification_details`(:ParamCLASSIFICATION_NAME,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':ParamCLASSIFICATION_NAME', $CLASSIFICATION_NAME);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function add_category_master($data){
    extract($data);
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_insert_store_item_cat_details`(:ParamITEM_CAT_NAME,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':ParamITEM_CAT_NAME', $ITEM_CAT_NAME);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function insert_quotation_item_pur_req($Item_name,$Req_Qty,$Item_description,$Indent_no,$submitted_date,$end_date,$term_and_conditions_docs,$additional_document){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_Pr_insert_Quotation_invite`(:Param_Item_name,:Param_Req_Qty,:Param_Item_description,:Param_Indent_no,:Param_submitted_date,:Param_end_date,:Param_term_and_conditions_docs,:Param_additional_document,@Result,@Q_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':Param_Item_name', $Item_name); 
    $command->bindValue(':Param_Req_Qty', $Req_Qty);
    $command->bindValue(':Param_Item_description', $Item_description); 
    $command->bindValue(':Param_Indent_no', $Indent_no);       
    $command->bindValue(':Param_submitted_date', $submitted_date); 
    $command->bindValue(':Param_end_date', $end_date); 
    $command->bindValue(':Param_term_and_conditions_docs', $term_and_conditions_docs); 
    $command->bindValue(':Param_additional_document', $additional_document); 
    $result=$command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $qOut = $connection->createCommand("select @Q_id as res1;")->queryScalar();
    $connection->close();
    return $qOut; 

}


public function get_all_quotation_item_pur_req(){
    
    $connection = Yii::$app->db;
    $connection->open();
    $sql ="SELECT * FROM `store_pr_quotation_invite` WHERE `is_active`='Y'";
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result;       

}

public function update_quotation_item_pur_req($item_id,$quantity_required,$Item_description,$voucher_no){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `store_Pr_insert_Quotation_invite`(:Param_Item_name,:Param_Req_Qty,:Param_Item_description,:Param_Indent_no, @Result,@Q_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':Param_Item_name', $item_id); 
        $command->bindValue(':Param_Req_Qty', $quantity_required); 
        $command->bindValue(':Param_Item_description', $Item_description); 
        $command->bindValue(':Param_Indent_no', $voucher_no);       
        $result=$command->execute();
        $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
        $qOut = $connection->createCommand("select @Q_id as res1;")->queryScalar();
        $connection->close();
        return $qOut; 

}

public function get_allquotation_items($Qid){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="CALL `store_Pr_notice_Quotation_invite`(:Param_Q_id,@Result)";
        $command = $connection->createCommand($sql);
        $command->bindValue(':Param_Q_id', $Qid);
        $result=$command->queryAll();
        $connection->close();
        return $result;

}



public function Store_Pr_get_supplier_list($Param_ITEM_CAT_CODE){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `Store_Pr_get_supplier_list`(:Param_ITEM_CAT_CODE)";
    $command = $connection->createCommand($sql);
            $command->bindValue(':Param_ITEM_CAT_CODE', $Param_ITEM_CAT_CODE);
            
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function quotation_mapping($Param_Q_id,$Param_supplier_code){
       //echo "<pre>==";print_r($data);die;
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `Store_pr_insert_item_quotation_mapping_details`(:Param_Q_id,:Param_supplier_code, @Result)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':Param_Q_id', $Param_Q_id); 
    $command->bindValue(':Param_supplier_code', $Param_supplier_code); 

    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
$result=$command->execute();
    $connection->close();
    return $valueOut;
}

public function Store_get_Quotation_details($Param_Indent_no){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `Store_get_Quotation_details`(:Param_Indent_no)";
    $command = $connection->createCommand($sql);
            $command->bindValue(':Param_Indent_no', $Param_Indent_no);
            
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}


public function get_quotation_item_pur_req($qid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT * FROM `store_pr_quotation_invite` WHERE Q_id=".$qid;
    $command = $connection->createCommand($sql);
    $result=$command->queryOne();
    $connection->close();
    return $result; 
}

public function sppliercat_mapping($Param_supplier_code,$Param_ITEM_CAT_CODE){
      //echo "<pre>==";print_r($Param_ITEM_CAT_CODE);die;
$connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_insert_supplier_itemcat_details`(:Param_supplier_code,:Param_ITEM_CAT_CODE, @Result)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':Param_supplier_code', $Param_supplier_code); 
    $command->bindValue(':Param_ITEM_CAT_CODE', $Param_ITEM_CAT_CODE); 

    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
$result=$command->execute();
    $connection->close();
    return $valueOut;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  

public function add_issue_request($data){
    extract($data);
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_issue_request`(:Division,:Emp_code,:Classification_Code,:Item_Cat_Code,:Item_Code,
            :Item_Type,:Item_Type_Id,:Measuring_Unit,:Quantity_Required,:Item_Purpose,:Remarks,:Role,:Flag,:FLA,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':Division', $Division);
    //$command->bindValue(':Cost_Centre_Code', $Cost_Centre_Code);
    $command->bindValue(':Emp_code', $Emp_code);
    $command->bindValue(':Classification_Code', $Classification_Code);
    $command->bindValue(':Item_Cat_Code', $Item_Cat_Code);
    $command->bindValue(':Item_Code', $Item_Code);
    $command->bindValue(':Item_Type', $Item_Type);
    $command->bindValue(':Item_Type_Id', $Item_Type_Id);
    $command->bindValue(':Measuring_Unit', $Measuring_Unit);
    $command->bindValue(':Quantity_Required', $Quantity_Required);
    $command->bindValue(':Item_Purpose', $Item_Purpose);
    $command->bindValue(':Remarks', $Remarks);
    $command->bindValue(':Role', $Role);
    $command->bindValue(':Flag', $Flag);
    $command->bindValue(':FLA', $FLA);
    //$command->bindValue(':Qty_Approved', $Qty_Approved);
    //$command->bindValue(':Approval_Date', $Approval_Date);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}



public function get_issue_request_status($eid){
$connection=   Yii::$app->db;
$connection->open();
$sql =" CALL `store_issue_request_status`(:emp_id)";
$command = $connection->createCommand($sql); 
$command->bindValue(':emp_id', $eid); 
$result=$command->queryAll();
$connection->close();
return $result;       
}

public function get_met_issue_request_status($eid){
$connection=   Yii::$app->db;
$connection->open();
$sql =" CALL `store_met_issue_request_status`(:emp_id)";
$command = $connection->createCommand($sql); 
$command->bindValue(':emp_id', $eid); 
$result=$command->queryAll();
$connection->close();
return $result;       
}

public function store_Initiate_issue_request_status($eid){
$connection=   Yii::$app->db;
$connection->open();
$sql =" CALL `store_Initiate_issue_request_status`(:emp_id)";
$command = $connection->createCommand($sql); 
$command->bindValue(':emp_id', $eid); 
$result=$command->queryAll();
$connection->close();
return $result;       
}  

public function get_issue_request_status_con($eid){
  $connection=   Yii::$app->db;
  $connection->open();
  $sql =" CALL `store_issue_request_status_con`(:emp_id)";
  $command = $connection->createCommand($sql); 
  $command->bindValue(':emp_id', $eid); 
  $result=$command->queryAll();
  $connection->close();
  return $result;       
}


public function get_return_request_status_con($eid){
  $connection=   Yii::$app->db;
  $connection->open();
  $sql =" CALL `store_get_return_request_status_con`(:emp_id)";
  $command = $connection->createCommand($sql); 
  $command->bindValue(':emp_id', $eid); 
  $result=$command->queryAll();
  $connection->close();
  return $result;       
}


public function get_capital_issue_request_status(){
$connection=   Yii::$app->db;
$connection->open();
$sql =" CALL `store_capital_issue_request_status`()";
    $command = $connection->createCommand($sql); 
    //$command->bindValue(':emp_id', $eid); 
$result=$command->queryAll();
$connection->close();
return $result;       
}
 
public function get_emp_by_role($role=NULL){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_emp_by_role`(:role)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':role', $role); 
    $result=$command->queryAll();
    $connection->close();
    return $result;  
}
public function pending_issue_requests($param_role,$param_e_id){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_pending_issue_requests`(:param_role,:param_e_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_role', $param_role); 
    $command->bindValue(':param_e_id', $param_e_id); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function get_pending_issue_requests($param_role,$param_e_id){
    $connection=   Yii::$app->db;
    $connection->open();

    $sql =" CALL `store_get_pending_issue_requests`(:param_role,:param_e_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_role', $param_role); 
    $command->bindValue(':param_e_id', $param_e_id); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function get_pending_return_issue_requests($param_role,$param_e_id){
  $connection=   Yii::$app->db;
  $connection->open();
  $sql =" CALL `store_get_pending_return_issue_requests`(:param_role,:param_e_id)";
  $command = $connection->createCommand($sql); 
  $command->bindValue(':param_role', $param_role); 
  $command->bindValue(':param_e_id', $param_e_id); 
  $result=$command->queryAll();
  $connection->close();
  return $result;       
}    


public function get_return_issue_requests_report($param_role,$param_e_id){
    $connection=   Yii::$app->db;
  $connection->open();
  $sql =" CALL `store_get_return_issue_requests_report`(:param_role,:param_e_id)";
  $command = $connection->createCommand($sql); 
  $command->bindValue(':param_role', $param_role); 
  $command->bindValue(':param_e_id', $param_e_id); 
  $result=$command->queryAll();
  $connection->close();
  return $result;       
}    


public function get_all_return_approve_issue_requests(){
  $connection=Yii::$app->db;
  $connection->open();
  $sql ="CALL `store_get_all_return_issue_requests`()";
  $command = $connection->createCommand($sql); 
  $result=$command->queryAll();
  $connection->close();
  return $result;       
}

public function get_all_return_pending_issue_requests(){
  $connection=Yii::$app->db;
  $connection->open();
  $sql ="CALL `store_getAllReturnPendingIssueRequests`()";
  $command = $connection->createCommand($sql); 
  $result=$command->queryAll();
  $connection->close();
  return $result;       
}

   


public function get_request_data($param_role,$param_e_id){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_ar_data`(:param_role,:param_e_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_role', $param_role); 
    $command->bindValue(':param_e_id', $param_e_id); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function updateqty($role,$voucher_no,$qty){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_request_updateqty`(:param_role,:param_voucher_no,:param_qty,  @Result)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_role', $role); 
    $command->bindValue(':param_voucher_no', $voucher_no); 
    $command->bindValue(':param_qty', $qty); 
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;     
}

public function apr_rej_irequest($PARAMID,$PARAMHOD_ID,$PARAMrole,$PARAMApproveReject,$PARAM_forward) {
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_issue_request_approve_reject`(:PARAMID,:PARAMHOD_ID,:PARAMrole,:PARAMApproveReject,:PARAM_forward,  @Result)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAMID', $PARAMID); 
    $command->bindValue(':PARAMHOD_ID', $PARAMHOD_ID); 
    $command->bindValue(':PARAMrole', $PARAMrole); 
    $command->bindValue(':PARAMApproveReject', $PARAMApproveReject); 
    $command->bindValue(':PARAM_forward', $PARAM_forward); 
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;
}

public function apr_rej_ireturnrequest($PARAMID,$PARAMHOD_ID,$PARAMrole,$PARAMApproveReject,$PARAM_forward) {
$connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_return_issue_request_approve_reject`(:PARAMID,:PARAMHOD_ID,:PARAMrole,:PARAMApproveReject,:PARAM_forward,  @Result)";
$command = $connection->createCommand($sql); 
$command->bindValue(':PARAMID', $PARAMID); 
$command->bindValue(':PARAMHOD_ID', $PARAMHOD_ID); 
$command->bindValue(':PARAMrole', $PARAMrole); 
$command->bindValue(':PARAMApproveReject', $PARAMApproveReject); 
    $command->bindValue(':PARAM_forward', $PARAM_forward); 
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;
}




public function issue_str_item($PARAMItemdetailids,$PARAMID,$PARAMrole) {



    
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_issue_item`(:PARAMID,@Result)";
    $command = $connection->createCommand($sql); 
    //$command->bindValue(':PARAMItemdetailids', $PARAMItemdetailids); 
    $command->bindValue(':PARAMID', $PARAMID); 
    //$command->bindValue(':PARAMrole', $PARAMrole); 
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();

    //GET ISSUER  DATA

    $connection->open();
    $sql ="SELECT * FROM `store_material_issue_request` WHERE `ID`=".$PARAMID;
    
    $command = $connection->createCommand($sql); 
    $result=$command->queryOne();
    $connection->close();
    //===============

    if (!empty($PARAMItemdetailids)) {
        //UPDATE `store_item_details` TABLE 
        $connection->open();
        $sql2 ="UPDATE store_item_details set issued_status='Y',`emp_code`='".@$result['emp_code']."',`issue_voucher_no`='".@$result['voucher_no']."' WHERE id IN (".$PARAMItemdetailids.")";
        //die($sql2);
        $command = $connection->createCommand($sql2);
        $valueOut = $command->execute();
        $connection->close();

    //----------------------------------
        //die('done');
    }
    


    return $valueOut;
}

 
public function get_mat_receipt_tmp(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_mat_receipt_tmp`(:PARAM_added_by)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAM_added_by', Yii::$app->user->identity->e_id); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function delete_mat_receipt_tmp($ID){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_delete_mat_receipt_tmp`(:PARAM_ID)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAM_ID', $ID);
    $command->execute();
    $connection->close();
    return 1;   
}

/*public function submit_mat_receipts(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_submit_mat_receipts`(:Param_added_by)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':Param_added_by', Yii::$app->user->identity->e_id);
    $command->execute();
    $connection->close();
    return 1;   
}*/

public function submit_mat_receipts($param_doc_path){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_submit_mat_receipts`(:Param_added_by,:Param_doc_path)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':Param_added_by', Yii::$app->user->identity->e_id);
    $command->bindValue(':Param_doc_path', $param_doc_path);
    $command->execute();
    $connection->close();
    return 1;   
}
public function insert_material_receipt($data){
	
	

    extract($data);

     if($supplier_code=='other'){
     $supplier_code=NULL;
     }
     if($ITEM_CODE=='000'){
     $ITEM_CODE=NULL;
     }


     if(!isset($ID)){$ID=NULL;}

    //die($ID);
    $PO_Date=date('Y-m-d',strtotime($PO_Date));
    $Memo_Date=date('Y-m-d',strtotime($Memo_Date));
    $connection=   Yii::$app->db;
    $connection->open();

    $sql ="CALL `store_insert_material_receipt`(:PARAM_temp_id,:PARAM_MRN_No,:PARAM_PO_no, :PARAM_PO_Date, :PARAM_Indent_no, :PARAM_Dept_code, :PARAM_Cost_code, :PARAM_Emp_code, :PARAM_added_by, :PARAM_supplier_code,:PARAM_Supplier_name, :PARAM_Supplier_address, :PARAM_Supplier_phone_no, :PARAM_Memo_no , :PARAM_Memo_Date , :PARAM_Receipt_mode , :PARAM_Consignment_no, :PARAM_Vehicle_no, :PARAM_CLASSIFICATION_CODE, :PARAM_ITEM_CAT_CODE, :PARAM_Remark, :PARAM_Description, :PARAM_SED, :PARAM_Octroi, :PARAM_Discount, :PARAM_Packing_Forword, :PARAM_Insurance, :PARAM_Cartage, :PARAM_Edu_Cess, :PARAM_QtyS, :PARAM_ED, :PARAM_Surcharge, :PARAM_Sale_tax_per, :PARAM_Sale_tax, :PARAM_Rate_per_unit, :PARAM_Measuring_Unit, :PARAM_QtyR, :PARAM_QtyO, :PARAM_ITEM_CODE, :PARAM_item_name, :PARAM_item_type,:PARAM_item_unit, :PARAM_gst_number,:PARAM_item_img,:PARAM_is_annual_maint,:PARAM_expiry_date_maint,:PARAM_item_expiraydate, :PARAM_erv_regno, @Result)";

    $command=$connection->createCommand($sql); 
    $command->bindValue(':PARAM_temp_id', $ID);
    $command->bindValue(':PARAM_MRN_No', $MRN_No);
    $command->bindValue(':PARAM_PO_no', $PO_no);
    $command->bindValue(':PARAM_PO_Date', $PO_Date);
    $command->bindValue(':PARAM_Indent_no', $Indent_no);
    $command->bindValue(':PARAM_Dept_code', $Dept_code);
    $command->bindValue(':PARAM_Cost_code', $Cost_Centre_Code);
    $command->bindValue(':PARAM_Emp_code', $Emp_code);
    $command->bindValue(':PARAM_added_by', Yii::$app->user->identity->e_id);
    $command->bindValue(':PARAM_supplier_code', $supplier_code);
     if(!isset($supplier_name)){$supplier_name=NULL;}
     if(!isset($address)){$address=NULL;}
     if(!isset($phoneno)){$phoneno=NULL;}
    $command->bindValue(':PARAM_Supplier_name', $supplier_name);
    $command->bindValue(':PARAM_Supplier_address', $address);
    $command->bindValue(':PARAM_Supplier_phone_no', $phoneno);
    $command->bindValue(':PARAM_Memo_no', $Memo_no);
    $command->bindValue(':PARAM_Memo_Date', $Memo_Date);
    $command->bindValue(':PARAM_Receipt_mode', $Receipt_mode);
    $command->bindValue(':PARAM_Consignment_no', $Consignment_no);
    $command->bindValue(':PARAM_Vehicle_no', $Vehicle_no);
    $command->bindValue(':PARAM_CLASSIFICATION_CODE', $CLASSIFICATION_CODE);
    $command->bindValue(':PARAM_ITEM_CAT_CODE', $ITEM_CAT_CODE);
     $command->bindValue(':PARAM_item_img',$item_image);
     $command->bindValue(':PARAM_is_annual_maint',$is_annual_maint);
     $command->bindValue(':PARAM_expiry_date_maint',$expiry_date_maint);
     $command->bindValue(':PARAM_item_expiraydate',$item_expiraydate);
     $command->bindValue(':PARAM_erv_regno',$erv_regno);

    if($Remark != '')
    {
        $Remark = (string)$Remark;
    }
    if($Description != '')
    {
        $Description = (string)$Description;
    }
    $command->bindValue(':PARAM_Remark', $Remark);
    $command->bindValue(':PARAM_Description', $Description);
    $command->bindValue(':PARAM_SED', $SED);
    $command->bindValue(':PARAM_Octroi', $Octroi);
    $command->bindValue(':PARAM_Discount', $Discount);
    $command->bindValue(':PARAM_Packing_Forword', $Packing_Forword);
    $command->bindValue(':PARAM_Insurance', $Insurance);
    $command->bindValue(':PARAM_Cartage', $Cartage);
    $command->bindValue(':PARAM_Edu_Cess', $Edu_Cess);
    $command->bindValue(':PARAM_QtyS', $QtyS);
    $command->bindValue(':PARAM_ED', $ED);
    $command->bindValue(':PARAM_Surcharge', $Surcharge);
    $command->bindValue(':PARAM_Sale_tax_per', $Sale_tax_per);
    $command->bindValue(':PARAM_Sale_tax', $Sale_tax);
    $command->bindValue(':PARAM_Rate_per_unit', $Rate_per_unit);
    $command->bindValue(':PARAM_Measuring_Unit', $Measuring_Unit);
    $command->bindValue(':PARAM_QtyR', $QtyR);
    $command->bindValue(':PARAM_QtyO', $QtyO);
    $command->bindValue(':PARAM_ITEM_CODE', $ITEM_CODE);
     if(!isset($item_name)){$item_name=NULL;}
     if(!isset($item_type)){$item_type=NULL;}
     if(!isset($units)){$units=NULL;}
    $command->bindValue(':PARAM_item_name', $item_name);
    $command->bindValue(':PARAM_item_type', $item_type);
    $command->bindValue(':PARAM_item_unit', $Measuring_Unit);

    
    $command->bindValue(':PARAM_gst_number', $gst_number);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function editrejected_material_receipt($data){

     extract($data);
     if($supplier_code=='other'){
     $supplier_code=NULL;
     }
     if($ITEM_CODE=='000'){
     $ITEM_CODE=NULL;
     }


     if(!isset($ID)){$ID=NULL;}

    //die($ID);
    $PO_Date=date('Y-m-d',strtotime($PO_Date));
    $Memo_Date=date('Y-m-d',strtotime($Memo_Date));
    $connection=   Yii::$app->db;
    $connection->open();

    $sql ="CALL `store_editrejected_material_receipt`(:PARAM_temp_id,:PARAM_MRN_No,:PARAM_PO_no, :PARAM_PO_Date, :PARAM_Indent_no, :PARAM_Dept_code, :PARAM_Cost_code, :PARAM_Emp_code, :PARAM_added_by, :PARAM_supplier_code,:PARAM_Supplier_name, :PARAM_Supplier_address, :PARAM_Supplier_phone_no, :PARAM_Memo_no , :PARAM_Memo_Date , :PARAM_Receipt_mode , :PARAM_Consignment_no, :PARAM_Vehicle_no, :PARAM_CLASSIFICATION_CODE, :PARAM_ITEM_CAT_CODE, :PARAM_Remark, :PARAM_Description, :PARAM_SED, :PARAM_Octroi, :PARAM_Discount, :PARAM_Packing_Forword, :PARAM_Insurance, :PARAM_Cartage, :PARAM_Edu_Cess, :PARAM_QtyS, :PARAM_ED, :PARAM_Surcharge, :PARAM_Sale_tax_per, :PARAM_Sale_tax, :PARAM_Rate_per_unit, :PARAM_Measuring_Unit, :PARAM_QtyR, :PARAM_QtyO, :PARAM_ITEM_CODE, :PARAM_item_name, :PARAM_item_type,:PARAM_item_unit, :PARAM_gst_number,:PARAM_item_img, @Result)";

    $command=$connection->createCommand($sql); 
    $command->bindValue(':PARAM_temp_id', $ID);
    $command->bindValue(':PARAM_MRN_No', $MRN_No);
    $command->bindValue(':PARAM_PO_no', $PO_no);
    $command->bindValue(':PARAM_PO_Date', $PO_Date);
    $command->bindValue(':PARAM_Indent_no', $Indent_no);
    $command->bindValue(':PARAM_Dept_code', $Dept_code);
    $command->bindValue(':PARAM_Cost_code', $Cost_Centre_Code);
    $command->bindValue(':PARAM_Emp_code', $Emp_code);
    $command->bindValue(':PARAM_added_by', Yii::$app->user->identity->e_id);
    $command->bindValue(':PARAM_supplier_code', $supplier_code);
     if(!isset($supplier_name)){$supplier_name=NULL;}
     if(!isset($address)){$address=NULL;}
     if(!isset($phoneno)){$phoneno=NULL;}
    $command->bindValue(':PARAM_Supplier_name', $supplier_name);
    $command->bindValue(':PARAM_Supplier_address', $address);
    $command->bindValue(':PARAM_Supplier_phone_no', $phoneno);
    $command->bindValue(':PARAM_Memo_no', $Memo_no);
    $command->bindValue(':PARAM_Memo_Date', $Memo_Date);
    $command->bindValue(':PARAM_Receipt_mode', $Receipt_mode);
    $command->bindValue(':PARAM_Consignment_no', $Consignment_no);
    $command->bindValue(':PARAM_Vehicle_no', $Vehicle_no);
    $command->bindValue(':PARAM_CLASSIFICATION_CODE', $CLASSIFICATION_CODE);
    $command->bindValue(':PARAM_ITEM_CAT_CODE', $ITEM_CAT_CODE);
     $command->bindValue(':PARAM_item_img',$item_image);

    if($Remark != '')
    {
        $Remark = (string)$Remark;
    }
    if($Description != '')
    {
        $Description = (string)$Description;
    }
    $command->bindValue(':PARAM_Remark', $Remark);
    $command->bindValue(':PARAM_Description', $Description);
    $command->bindValue(':PARAM_SED', $SED);
    $command->bindValue(':PARAM_Octroi', $Octroi);
    $command->bindValue(':PARAM_Discount', $Discount);
    $command->bindValue(':PARAM_Packing_Forword', $Packing_Forword);
    $command->bindValue(':PARAM_Insurance', $Insurance);
    $command->bindValue(':PARAM_Cartage', $Cartage);
    $command->bindValue(':PARAM_Edu_Cess', $Edu_Cess);
    $command->bindValue(':PARAM_QtyS', $QtyS);
    $command->bindValue(':PARAM_ED', $ED);
    $command->bindValue(':PARAM_Surcharge', $Surcharge);
    $command->bindValue(':PARAM_Sale_tax_per', $Sale_tax_per);
    $command->bindValue(':PARAM_Sale_tax', $Sale_tax);
    $command->bindValue(':PARAM_Rate_per_unit', $Rate_per_unit);
    $command->bindValue(':PARAM_Measuring_Unit', $Measuring_Unit);
    $command->bindValue(':PARAM_QtyR', $QtyR);
    $command->bindValue(':PARAM_QtyO', $QtyO);
    $command->bindValue(':PARAM_ITEM_CODE', $ITEM_CODE);
     if(!isset($item_name)){$item_name=NULL;}
     if(!isset($item_type)){$item_type=NULL;}
     if(!isset($units)){$units=NULL;}
    $command->bindValue(':PARAM_item_name', $item_name);
    $command->bindValue(':PARAM_item_type', $item_type);
    $command->bindValue(':PARAM_item_unit', $Measuring_Unit);

    
    $command->bindValue(':PARAM_gst_number', $gst_number);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function insert_material_receipt1111($data){
// echo "<pre>==";print_r($data); 
    extract($data);
     if($supplier_code=='other'){
     $supplier_code=NULL;
     }
     if($ITEM_CODE=='000'){
     $ITEM_CODE=NULL;
     }
   // die; 
     if(!isset($ID)){$ID=NULL;}
    $PO_Date=date('Y-m-d',strtotime($PO_Date));
    $Memo_Date=date('Y-m-d',strtotime($Memo_Date));
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_insert_material_receipt`(:PARAM_temp_id,:PARAM_MRN_No,:PARAM_PO_no, :PARAM_PO_Date, :PARAM_Indent_no, :PARAM_Dept_code, :PARAM_Cost_code, :PARAM_Emp_code, :PARAM_added_by, :PARAM_supplier_code,:PARAM_Supplier_name, :PARAM_Supplier_address, :PARAM_Supplier_phone_no, :PARAM_Memo_no , :PARAM_Memo_Date , :PARAM_Receipt_mode , :PARAM_Consignment_no, :PARAM_Vehicle_no, :PARAM_CLASSIFICATION_CODE, :PARAM_ITEM_CAT_CODE, :PARAM_Remark, :PARAM_Description, :PARAM_SED, :PARAM_Octroi, :PARAM_Discount, :PARAM_Packing_Forword, :PARAM_Insurance, :PARAM_Cartage, :PARAM_Edu_Cess, :PARAM_QtyS, :PARAM_ED, :PARAM_Surcharge, :PARAM_Sale_tax_per, :PARAM_Sale_tax, :PARAM_Rate_per_unit, :PARAM_Measuring_Unit, :PARAM_QtyR, :PARAM_QtyO, :PARAM_ITEM_CODE, :PARAM_item_name, :PARAM_item_type,:PARAM_item_unit,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':PARAM_temp_id', $ID);
    $command->bindValue(':PARAM_MRN_No', $MRN_No);
    $command->bindValue(':PARAM_PO_no', $PO_no);
    $command->bindValue(':PARAM_PO_Date', $PO_Date);
    $command->bindValue(':PARAM_Indent_no', $Indent_no);
    $command->bindValue(':PARAM_Dept_code', $Dept_code);
    $command->bindValue(':PARAM_Cost_code', $Cost_Centre_Code);
    $command->bindValue(':PARAM_Emp_code', $Emp_code);
    $command->bindValue(':PARAM_added_by', Yii::$app->user->identity->e_id);
    $command->bindValue(':PARAM_supplier_code', $supplier_code);
     if(!isset($supplier_name)){$supplier_name=NULL;}
     if(!isset($address)){$address=NULL;}
     if(!isset($phoneno)){$phoneno=NULL;}
    $command->bindValue(':PARAM_Supplier_name', $supplier_name);
    $command->bindValue(':PARAM_Supplier_address', $address);
    $command->bindValue(':PARAM_Supplier_phone_no', $phoneno);
     
    $command->bindValue(':PARAM_Memo_no', $Memo_no);
    $command->bindValue(':PARAM_Memo_Date', $Memo_Date);
    $command->bindValue(':PARAM_Receipt_mode', $Receipt_mode);
    $command->bindValue(':PARAM_Consignment_no', $Consignment_no);
    $command->bindValue(':PARAM_Vehicle_no', $Vehicle_no);
    $command->bindValue(':PARAM_CLASSIFICATION_CODE', $CLASSIFICATION_CODE);
    $command->bindValue(':PARAM_ITEM_CAT_CODE', $ITEM_CAT_CODE);

    if($Remark != '')
    {
        $Remark = (string)$Remark;
    }
    if($Description != '')
    {
        $Description = (string)$Description;
    }
    $command->bindValue(':PARAM_Remark', $Remark);
    $command->bindValue(':PARAM_Description', $Description);
    $command->bindValue(':PARAM_SED', $SED);
    $command->bindValue(':PARAM_Octroi', $Octroi);
    $command->bindValue(':PARAM_Discount', $Discount);
    $command->bindValue(':PARAM_Packing_Forword', $Packing_Forword);
    $command->bindValue(':PARAM_Insurance', $Insurance);
    $command->bindValue(':PARAM_Cartage', $Cartage);
    $command->bindValue(':PARAM_Edu_Cess', $Edu_Cess);
    $command->bindValue(':PARAM_QtyS', $QtyS);
    $command->bindValue(':PARAM_ED', $ED);
    $command->bindValue(':PARAM_Surcharge', $Surcharge);
    $command->bindValue(':PARAM_Sale_tax_per', $Sale_tax_per);
    $command->bindValue(':PARAM_Sale_tax', $Sale_tax);
    $command->bindValue(':PARAM_Rate_per_unit', $Rate_per_unit);
    $command->bindValue(':PARAM_Measuring_Unit', $Measuring_Unit);
    $command->bindValue(':PARAM_QtyR', $QtyR);
    $command->bindValue(':PARAM_QtyO', $QtyO);
    $command->bindValue(':PARAM_ITEM_CODE', $ITEM_CODE);
     if(!isset($item_name)){$item_name=NULL;}
     if(!isset($item_type)){$item_type=NULL;}
     if(!isset($units)){$units=NULL;}
    $command->bindValue(':PARAM_item_name', $item_name);
    $command->bindValue(':PARAM_item_type', $item_type);
    $command->bindValue(':PARAM_item_unit', $Measuring_Unit);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}



public function update_material_receipt($data){
// echo "<pre>==";print_r($data);die; 
    extract($data);
    if($supplier_code=='other'){
     $supplier_code=NULL;
     }
    $PO_Date=date('Y-m-d',strtotime($PO_Date));
    $Memo_Date=date('Y-m-d',strtotime($Memo_Date));
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_update_mat_receipt`(:PARAM_accessid,:PARAM_PO_no, :PARAM_PO_Date, :PARAM_Indent_no, :PARAM_Dept_code, :PARAM_Cost_code, :PARAM_Emp_code, :PARAM_added_by, :PARAM_supplier_code, :PARAM_Memo_no , :PARAM_Memo_Date , :PARAM_Receipt_mode , :PARAM_Consignment_no, :PARAM_Vehicle_no, :PARAM_CLASSIFICATION_CODE, :PARAM_ITEM_CAT_CODE, :PARAM_Remark, :PARAM_Description, :PARAM_SED, :PARAM_Octroi, :PARAM_Discount, :PARAM_Packing_Forword, :PARAM_Insurance, :PARAM_Cartage, :PARAM_Edu_Cess, :PARAM_QtyS, :PARAM_ED, :PARAM_Surcharge, :PARAM_Sale_tax_per, :PARAM_Sale_tax, :PARAM_Rate_per_unit, :PARAM_Measuring_Unit, :PARAM_QtyR, :PARAM_QtyO, :PARAM_ITEM_CODE,:gst_number,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':PARAM_accessid', $ID);
    $command->bindValue(':PARAM_PO_no', $PO_no);
    $command->bindValue(':PARAM_PO_Date', $PO_Date);
    $command->bindValue(':PARAM_Indent_no', $Indent_no);
    $command->bindValue(':PARAM_Dept_code', $Dept_code);
    $command->bindValue(':PARAM_Cost_code', $Cost_Centre_Code);
    $command->bindValue(':PARAM_Emp_code', $Emp_code);
    $command->bindValue(':PARAM_added_by', Yii::$app->user->identity->e_id);
    $command->bindValue(':PARAM_supplier_code', $supplier_code);
    /*if(!isset($supplier_name)){$supplier_name=NULL;}
     if(!isset($address)){$address=NULL;}
     if(!isset($phoneno)){$phoneno=NULL;}
    $command->bindValue(':PARAM_Supplier_name', $supplier_name);
    $command->bindValue(':PARAM_Supplier_address', $address);
    $command->bindValue(':PARAM_Supplier_phone_no', $phoneno);*/
    
    $command->bindValue(':PARAM_Memo_no', $Memo_no);
    $command->bindValue(':PARAM_Memo_Date', $Memo_Date);
    $command->bindValue(':PARAM_Receipt_mode', $Receipt_mode);
    $command->bindValue(':PARAM_Consignment_no', $Consignment_no);
    $command->bindValue(':PARAM_Vehicle_no', $Vehicle_no);
    $command->bindValue(':PARAM_CLASSIFICATION_CODE', $CLASSIFICATION_CODE);
    $command->bindValue(':PARAM_ITEM_CAT_CODE', $ITEM_CAT_CODE);
    $command->bindValue(':PARAM_Remark', $Remark);
    $command->bindValue(':PARAM_Description', $Description);
    $command->bindValue(':PARAM_SED', $SED);
    $command->bindValue(':PARAM_Octroi', $Octroi);
    $command->bindValue(':PARAM_Discount', $Discount);
    $command->bindValue(':PARAM_Packing_Forword', $Packing_Forword);
    $command->bindValue(':PARAM_Insurance', $Insurance);
    $command->bindValue(':PARAM_Cartage', $Cartage);
    $command->bindValue(':PARAM_Edu_Cess', $Edu_Cess);
    $command->bindValue(':PARAM_QtyS', $QtyS);
    $command->bindValue(':PARAM_ED', $ED);
    $command->bindValue(':PARAM_Surcharge', $Surcharge);
    $command->bindValue(':PARAM_Sale_tax_per', $Sale_tax_per);
    $command->bindValue(':PARAM_Sale_tax', $Sale_tax);
    $command->bindValue(':PARAM_Rate_per_unit', $Rate_per_unit);
    $command->bindValue(':PARAM_Measuring_Unit', $Measuring_Unit);
    $command->bindValue(':PARAM_QtyR', $QtyR);
    $command->bindValue(':PARAM_QtyO', $QtyO);
    $command->bindValue(':PARAM_ITEM_CODE', $ITEM_CODE);
$command->bindValue(':gst_number', $gst_number);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function get_new_rm_no($flag){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_new_rm_no`(:PARAM_flag)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAM_flag', $flag);
    $result=$command->queryAll();
   // echo "<pre>==";print_r($result);die; 
    $connection->close();
    return $result;   
}

 public function get_new_mrn_no(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_new_rm_no`(NULL)";
    $command = $connection->createCommand($sql); 
    $result=$command->queryOne();
    $connection->close();
    return $result;   
}


public function get_mrn_records($mrno,$rno=NULL,$flag=1)
{ //echo $mrno;die;
    $connection= Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_mrn_records`(:PARAM_MRN_no,:PARAM_accessid,:PARAM_flag)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAM_MRN_no', $mrno);
    $command->bindValue(':PARAM_accessid', $rno);
    $command->bindValue(':PARAM_flag', $flag);
    $result=$command->queryAll();
     // echo"<pre>"; print_r($result);
    $connection->close();
    return $result;  
 

}

public function get_records_by_mrn($mrno)
{
    $connection= Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_records_by_mrn`(:PARAM_MRN_no)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAM_MRN_no', $mrno);
    $result=$command->queryAll();
    $connection->close();
    return $result;   
}

 public function get_mat_receipt_detail($mrno,$rno=NULL){
    $connection= Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_mat_receipt_detail`(:PARAM_id,:PARAM_MRN_no)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAM_MRN_no', $mrno);
    $command->bindValue(':PARAM_id', $rno);
    $result=$command->queryOne();
    $connection->close();
    return $result;   
}

 public function get_mat_receipt_tmp_detail($mrno,$rno=NULL){
    $connection= Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_mat_receipt_tmp_detail`(:PARAM_id,:PARAM_MRN_no)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAM_MRN_no', $mrno);
    $command->bindValue(':PARAM_id', $rno);
    $result=$command->queryOne();
    $connection->close();
    return $result;   
}

 public function get_mat_receipt_detail_rejected($mrno,$rno=NULL){

    $connection = Yii::$app->db;
    $connection->open();
    $sql="SELECT * FROM store_mat_receipt WHERE `Accessid`=".$rno." AND `MRN_No`=".$mrno.";";    
    // $sql="CALL `store_get_item_details_for_issue`(:Param_itemcode)";
    $command = $connection->createCommand($sql);
    $result=$command->queryOne();
    $connection->close();
    return $result;       
}


 public function get_mat_records($mrno,$rno=NULL){
    $connection= Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_mat_records`(:PARAM_MRN_no,:PARAM_accessid)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAM_MRN_no', $mrno);
    $command->bindValue(':PARAM_accessid', $rno);
    $result=$command->queryAll();
    $connection->close();
    return $result;   
}

public function update_mat_records($data){
    extract($data);
     $connection=Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_update_mat_inspection`(:PARAM_MRN_No, :PARAM_accessid,:PARAM_Qty_Accepted,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':PARAM_MRN_No', $MRN_No);
    $command->bindValue(':PARAM_accessid', $rno);
    $command->bindValue(':PARAM_Qty_Accepted', $qty_accepted);

    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut; 
}

public function update_mat_records_by_store($data){
     extract($data);
//       echo "<pre>";
//       print_r($data); die;
    $insp_date=date('Y-m-d',strtotime($insp_date));
    $connection=Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_update_mat_inspection_bystore`(:PARAM_MRN_No, :PARAM_accessid, :PARAM_Inspection_Date, :PARAM_Rejection_Reason, :PARAM_Qty_Accepted, :PARAM_Qty_Rejected, :PARAM_Committee_member , :PARAM_Inspected_by_comitee,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':PARAM_MRN_No', $MRN_No);
    $command->bindValue(':PARAM_accessid', $rno);
    $command->bindValue(':PARAM_Inspection_Date', $insp_date);
    $command->bindValue(':PARAM_Qty_Accepted', $qty_accepted);
    $command->bindValue(':PARAM_Qty_Rejected', $qtyrej);
    $command->bindValue(':PARAM_Rejection_Reason', $rreason);
    $command->bindValue(':PARAM_Committee_member', $cmember);
    $command->bindValue(':PARAM_Inspected_by_comitee', $ysno);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

 /******************************************* Purchase *******************************************************/
function get_large_amount_in_words($number){ //error_reporting(0);
    $no = floor($number);
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
               $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
               while ($i < $digits_1) {
                 $divider = ($i == 2) ? 10 : 100;
                 $number = floor($no % $divider);
                 $no = floor($no / $divider);
                 $i += ($divider == 10) ? 1 : 2;
                 if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
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
                " " . $words[$point / 10] . " " . 
                      $words[$point = $point % 10] : '';
    if($points){$points=$points . " Paise";}
              $return= $result .$points;    
    return Ucfirst($return). " crore";;
}

public function get_amount_in_words($number){ //error_reporting(0);
                // $number=188129544178;
                $prefix='';
                if(strlen($number)>9){
                    $place=strlen($number)-7;
                    $rest = substr($number, 0,$place);
                    $prefix=$this->get_large_amount_in_words($rest);
                    $number= substr($number, $place);
                }
               $no = floor($number);
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
               $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
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
                " " . $words[$point / 10] . " " . 
                      $words[$point = $point % 10] : '';
    if($points){$points=$points . " Paise";}
              $return= $prefix.' '.$result . "Rupees  ".$points;    
    return Ucfirst($return).' Only';
    
}

public function get_purchase_request_status($eid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_purchase_request_status`(:emp_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':emp_id', $eid); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}
public function remove_purchase_item($id){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_remove_purchase_item`(:item_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':item_id', $id); 
    $result=$command->execute();
    $connection->close();
    return $result;       
}
public function purchase_request_view($req_id){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_purchase_request_view`(:req_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':req_id', $req_id); 
    $result=$command->queryOne();
    $connection->close();
    return $result;       
}
public function pending_purchase_items($forward_to=NULL){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_pending_purchase_items`(:forward_to)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':forward_to', $forward_to); 
    $result=$command->queryAll();
    $connection->close();
    return $result;  





}
public function purchase_items_forward($itemids,$forward_to,$ipurchase_mod=NULL,$remarks=NULL){
    $connection=   Yii::$app->db;
    $connection->open();
    $eid=Yii::$app->user->identity->e_id;
    $sql =" CALL `store_pending_purchase_items_forward`(:item_ids,:forward_to,:purchase_mod,:remarks)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':item_ids', $itemids); 
    $command->bindValue(':forward_to', $forward_to); 
    $command->bindValue(':purchase_mod', $ipurchase_mod); 
    $command->bindValue(':remarks', $remarks); 
    $result=$command->execute();
    $connection->close();
    return $result;  
}

public function pending_purchase_requests($param_role,$param_e_id,$req_id=NULL){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_pending_purchase_requests`(:param_role,:param_e_id,:req_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_role', $param_role); 
    $command->bindValue(':param_e_id', $param_e_id); 
    $command->bindValue(':req_id', $req_id); 
    if($req_id)
    $result=$command->queryOne();
    else
    $result=$command->queryAll();
    
    $connection->close();
    return $result;       
}
public function update_item_pur_req($item_id,$req_id,$purc_status,$ipurchase_mod,$remarks){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_update_item_pur_req`(:item_id,:req_id,:purc_status,:ipurchase_mod,:remarks)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':item_id', $item_id); 
    $command->bindValue(':req_id', $req_id); 
    $command->bindValue(':purc_status', $purc_status); 
    $command->bindValue(':ipurchase_mod', $ipurchase_mod); 
    $command->bindValue(':remarks', $remarks); 
    $result=$command->execute();
    $connection->close();
    return $result; 
}
public function view_purchase_item($id){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_view_purchase_requests`(NULL,NULL,:param_item_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_item_id', $id); 
    $result=$command->queryOne();
    $connection->close();
    return $result; 
}
public function view_purchase_requests($id){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_view_purchase_requests`(:param_req_id,NULL,NULL)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_req_id', $id); 
    $result=$command->queryAll();
    $connection->close();
    return $result; 
}

public function apr_rej_prequest($PARAMID,$PARAMHOD_ID,$PARAMrole,$PARAMApproveReject) {
    $reject_remarks=NULL;
    if(isset($_POST['reject_remarks'])){
            $reject_remarks=trim($_POST['reject_remarks']);
        }
    $connection=   Yii::$app->db;
    $connection->open();
    $eid =Yii::$app->user->identity->e_id;
    $sql =" CALL `store_purchase_request_approve_reject`(:PARAMID,:PARAMHOD_ID,:PARAMrole,:PARAMApproveReject,:PARAMEID, :PARAMREJREASON,  @Result)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':PARAMID', $PARAMID); 
    $command->bindValue(':PARAMHOD_ID', $PARAMHOD_ID); 
    $command->bindValue(':PARAMrole', $PARAMrole); 
    $command->bindValue(':PARAMApproveReject', $PARAMApproveReject); 
    $command->bindValue(':PARAMEID', $eid); 
    $command->bindValue(':PARAMREJREASON', $reject_remarks); 
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    foreach(explode(",",$PARAMID) as $id){
        Yii::$app->inventory->store_purchase_request_logs($id,$PARAMApproveReject,$_POST);
    }
    return $valueOut;
}

public function get_material_purchase_crequest(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_material_purchase_crequest`()";
    $command = $connection->createCommand($sql); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function get_p_request_data($param_role,$param_e_id,$purchase_emp){
    $connection=   Yii::$app->db;
    $connection->open();
   
    $sql =" CALL `store_purchase_ar_data`(:param_role,:param_e_id,:purchase_emp)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_role', $param_role); 
    $command->bindValue(':param_e_id', $param_e_id); 
    $command->bindValue(':purchase_emp', $purchase_emp); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function get_approved_requests(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_approved_requests`()";
    $command = $connection->createCommand($sql); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function get_p_request_items(){
        
        $connection=   Yii::$app->db;
        $connection->open();
        $eid=Yii::$app->user->identity->e_id;
        $sql =" CALL `store_view_purchase_requests`(NULL,:param_emp_id,NULL)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_emp_id', $eid); 
        $result=$command->execute();
        $connection->close();
        return $result; 
}

public function update_req_id_with_item($reqid){
    
        $connection=   Yii::$app->db;
        $connection->open();
        $eid=Yii::$app->user->identity->e_id;
        $sql =" CALL `store_update_preq_id_with_item`(:param_emp_id,:param_req_id,NULL,NULL,NULL)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_emp_id', $eid); 
        $command->bindValue(':param_req_id', $reqid); 
        $result=$command->execute();
        $connection->close();
        
}

public function update_status_discuss($req_id,$remarks=NULL,$msj_for,$status){
    $role = Yii::$app->user->identity->role;
        if($role==7){
            $status=1;
        }elseif($role==2 && $msj_for=='ED'){
            $status=2;
        }
        elseif($role==2 && $msj_for=='EMP'){
            $status=3;
        }
        elseif($role==3){
            $status=4;
        }
        $connection=   Yii::$app->db;
        $connection->open();    
        $sql="UPDATE `store_material_purchase_request` SET discuss_WCH='".$status."' where id=".$req_id; 
        $command = $connection->createCommand($sql); 
        $result=$command->execute();
        if($remarks){
            $emp_id =Yii::$app->user->identity->e_id; 
            $role   =Yii::$app->user->identity->role; 
            $sql="INSERT INTO `store_material_purchase_request_msj` (`emp_id`, `role`, `req_id`, `msj_for`, `msj`) VALUES ($emp_id, $role, $req_id, '$msj_for','$remarks')";
            $command = $connection->createCommand($sql); 
            $result=$command->execute();
        }
        $connection->close();
}


public function update_pur_req_FM_CH($flag,$req_id,$p_heads=NULL,$p_funds=NULL){
        $connection=   Yii::$app->db;
        $connection->open();
        $e_id=Yii::$app->user->identity->e_id;
        $role=Yii::$app->user->identity->role;
        $project_info=$reject_remarks="";
        if(isset($_POST['reject_remarks'])){
            $reject_remarks=trim($_POST['reject_remarks']);
            $reject_remarks=",reject_remarks='$reject_remarks'";
        }
        if(isset($_POST['project_id']) && !empty($_POST['project_id'])){
            $project_id=trim($_POST['project_id']);
            $project=trim($_POST['project']);
            $project_info=",project_id='$project_id', project='$project' ";
        }
        if($role!=6){
            if($role==17){
                $purc_status=$_POST['purc_status'];
                $mod='';
                if($purc_status=='Order-Declined'){$mod=',purchase_remarks=NULL ';}
                if(isset($_POST['purchase_mod']) && !empty($_POST['purchase_mod']) ){
                    $mod=',purchase_mod="'.$_POST['purchase_mod'].'"';
                    
                    if($_POST['purchase_mod']!='Gem' && $_POST['purchase_mod']!='CPP'){
                    if(isset($_POST['remarks']) && !empty($_POST['remarks']) ){
                        $mod.=',purchase_remarks="'.trim($_POST['remarks']).'"';
                        }
                    }
                }
                
        $sql="UPDATE `store_material_purchase_request` SET purchase_status='".$purc_status."' $mod $reject_remarks where id=".$req_id; 
            }else{
                $chremarks=NULL;
                if(isset($_POST['chremarks']) && !empty($_POST['chremarks'])){
                    $chremarks=trim($_POST['chremarks']);
                }
                $sql="UPDATE `store_material_purchase_request` SET flag=$flag, CH_remarks='$chremarks' , CH_action_date=now(), approved_by_CH='$e_id' $reject_remarks where id=".$req_id;
            }
        }else{
            if($flag==12){
        $sql="UPDATE `store_material_purchase_request` SET flag=$flag, FM_action_date=now(), approved_by_FM='$e_id'       $reject_remarks where id=".$req_id;
            }else{
        $sql="UPDATE `store_material_purchase_request` SET flag=$flag, FM_action_date=now(), approved_by_FM='$e_id',  project_head='".$p_heads."' , project_funds='".$p_funds."' $project_info $reject_remarks where id=".$req_id;
            }
        }
        $command = $connection->createCommand($sql); 
        $result=$command->execute();
        $connection->close();
        
        Yii::$app->inventory->store_purchase_request_logs($req_id,NULL,$_POST,$flag);
        return $result;
}
public function update_pur_req($mainstatus,$req_id,$flag){
        $connection=   Yii::$app->db;
        $connection->open();
        $e_id=Yii::$app->user->identity->e_id;
        $sql="UPDATE `store_material_purchase_request` SET flag=$flag, storeinc_action_date=now(), Approved_by_storeinc='$e_id', available_in_store='".$mainstatus."' where id=".$req_id;
        $command = $connection->createCommand($sql); 
        $result=$command->execute();
        $connection->close();
        Yii::$app->inventory->store_purchase_request_logs($req_id,NULL,$_POST,$flag);
        return $result;
}
    
public function store_purchase_request_logs($req_id,$PARAMApproveReject,$params,$flag=NULL){
        //echo "<pre>==";print_r($params);
        if(isset($params['_csrf'])){unset($params['_csrf']);}
        if(isset($params['req_id'])){unset($params['req_id']);}
        if(isset($params['v_nos'])){unset($params['v_nos']);}
        if(isset($params['auth_id'])){unset($params['auth_id']);}
        // echo "<pre>==";print_r($params);die; 
        $params=json_encode($params);
        $connection=   Yii::$app->db;
        $connection->open();
        $eid=Yii::$app->user->identity->e_id;
        $role=Yii::$app->user->identity->role;
        $sql =" CALL `store_material_purchase_request_logs`(:user_id,:v_roleid,:req_id,:PARAMApproveReject,:params,:flag)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':user_id', $eid); 
        $command->bindValue(':v_roleid', $role); 
        $command->bindValue(':req_id', $req_id); 
        $command->bindValue(':PARAMApproveReject', $PARAMApproveReject); 
        $command->bindValue(':params', $params); 
        $command->bindValue(':flag', $flag); 
        $result=$command->execute();
        $connection->close();
        return $result;
        
}

public function update_pur_req_temp($status,$avail_qty=NULL,$item_id){
        $connection=   Yii::$app->db;
        $connection->open();
        $eid=Yii::$app->user->identity->e_id;
        $sql =" CALL `store_update_preq_id_with_item`(NULL,NULL,:PARAM_qty_avail,:PARAM_qty,:PARAM_item_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':PARAM_qty_avail', $status); 
        $command->bindValue(':PARAM_qty', $avail_qty); 
        $command->bindValue(':PARAM_item_id', $item_id); 
        $result=$command->execute();
        $connection->close();
        return $result;
        
}

public function get_pur_temp_item(){
     
        $connection=   Yii::$app->db;
        $connection->open();
        $eid=Yii::$app->user->identity->e_id;
        $reqid=0;
        $sql =" CALL `store_view_purchase_requests`(:param_req_id,:param_emp_id,NULL)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':param_req_id', $reqid); 
        $command->bindValue(':param_emp_id', $eid); 
        $result=$command->queryAll();
        $connection->close();
        $html='';
        /*   $menuid = Yii::$app->utility->decryptString($_GET['securekey']); 
        $menuid = Yii::$app->utility->encryptString($menuid);
        foreach($result as $k=>$res){
                $html.='<tr>';
                $html.='<td>'.($k+1).'</td>';
                $html.='<td>'.$res['item_name'].'</td>';
                $html.='<td>'.$res['item_specification'].'</td>';
                $html.='<td>'.$res['purpose'].'</td>';
                $html.='<td>'.$res['quantity_required'].'</td>';
                $html.='<td>'.$res['approx_cost'].'</td>';
                $html.='<td><a target="_blank" href="'.Yii::$app->homeUrl.'inventory/view?securekey='.$menuid.'">Doc</a></td>';
                $html.='</tr>';
            } */
      
        return $result;
    }    

public function send_fpwd_email($email) {
        $connection=   Yii::$app->db;
        $connection->open();
        $sql="SELECT * FROM `rbac_employee` WHERE `username` = :username and is_active='Y'";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':username', $email); 
        $result=$command->queryAll();
        if(!empty($result)){
        try 
        {
        $MAIL_HOST = MAIL_HOST;
        $MAIL_FROM = MAIL_FROM;
        $MAIL_PASSWORD = MAIL_PASSWORD;
        $MAIL_PORT = MAIL_PORT;
        $MAIL_FROM_LABEL = MAIL_FROM_LABEL;
        if (!empty($MAIL_FROM) && filter_var($MAIL_FROM, FILTER_VALIDATE_EMAIL) && !empty($MAIL_PASSWORD) && !empty($MAIL_PORT)){
             
            $subject = "eMulazim Password Reset";
            //$emp = Yii::$app->utility->get_employees($sender_empcode);
            //$sender_name = $emp['fullname'].", ".$emp['desg_name']." ($emp[dept_name])";
            $link_CDAC = "Click here for login <a href='".emulazim_link_cdac."' style='color:red;font-weight:bold' title='".emulazim_lable."'>".emulazim_lable." (C-DAC Network)</a>";
            $link_Outside = "Click here for login <a href='".emulazim_link_outside."' style='color:red;font-weight:bold' title='".emulazim_lable."'>".emulazim_lable." (Other Network)</a>";
            $headers = '';
            
            $str_pwd = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $newpwd=  substr(str_shuffle($str_pwd),0, 5);
            $message = "<div style='font-size:13px;'>Dear Sir/Madam,<br><br> Your Password has been reset, Your New Password is <b>'$newpwd'<b><br></br>Thanks<br><b>eMulazim Team<br>C-DAC, Mohali</b></div>";

            
            
             
            $sql =" UPDATE `rbac_employee` SET `password` = md5('$newpwd') WHERE `username` = '$email';";
            $command = $connection->createCommand($sql); 
            $command->bindValue(':email', $email); 
            $result=$command->execute();
            $connection->close();
            
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
            require_once './PHPMailer/PHPMailerAutoload.php';
            $mail = new \PHPMailer;  



            $mail->isSMTP();                                         // Set mailer to use SMTP
            $mail->Host = $MAIL_HOST;                                      // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                                 // Enable SMTP authentication
            $mail->Username = $MAIL_FROM;                              // SMTP username
            $mail->Password = $MAIL_PASSWORD;                        // SMTP password
            $mail->SMTPSecure = 'tls';                              // Enable TLS encryption, `ssl` also accepted
            $mail->Port = $MAIL_PORT;                                    // TCP port to connect to
            $mail->isHTML(true);   
            $mail->setFrom($MAIL_FROM, $MAIL_FROM_LABEL);

            $mail->Subject = $subject;
            $mail->Body = $message;
             $mail->addAddress($email);
            $mail->send();
            return true;
     
           
        }

    } 
    catch (Exception $ex) 
    {
        throw new Exception(500, $ex);
    }
  }
}



// Comparison Report start

public function get_item_supplier($itemt_code){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_item_supplier`(:item_code)";
    $command = $connection->createCommand($sql);
    $command->bindValue(':item_code', $itemt_code);
    // $command->bindValue(':class_code', $class_code);
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}


 public function insert_camp_report($data){
     $connection=   Yii::$app->db;
    $connection->open();
    extract($data);

    // echo "<pre>==";print_r($data);die;
     $sql =" CALL `store_insert_camp_report`( :PARAM_CLASSIFICATION_CODE, :PARAM_ITEM_CAT_CODE, :PARAM_ITEM_CODE, :PARAM_supplier_code, :PARAM_Qty, :PARAM_tax, :PARAM_Amount, :PARAM_remarks,:PARAM_added_by,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':PARAM_CLASSIFICATION_CODE', $CLASSIFICATION_CODE);
    $command->bindValue(':PARAM_ITEM_CAT_CODE', $ITEM_CAT_CODE);
    $command->bindValue(':PARAM_ITEM_CODE', $ITEM_CODE);
    $command->bindValue(':PARAM_supplier_code', $Supplier_Code);
    $command->bindValue(':PARAM_Qty', $Qty);
    $command->bindValue(':PARAM_tax', $tax);
    $command->bindValue(':PARAM_Amount', $Amount);
    $command->bindValue(':PARAM_remarks', $remarks);
    $command->bindValue(':PARAM_added_by', Yii::$app->user->identity->e_id);
     $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();

    $connection->close();
// echo "<pre>==";print_r($valueOut); die;
    return $valueOut;      
   
}

public function update_camp_report($data){
//echo "<pre>==";print_r($data);die; 
    extract($data);
    
    // $PO_Date=date('Y-m-d',strtotime($PO_Date));
    // $Memo_Date=date('Y-m-d',strtotime($Memo_Date));
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_update_camp_report`(:PARAM_CLASSIFICATION_CODE, :PARAM_ITEM_CAT_CODE, :PARAM_ITEM_CODE, :PARAM_supplier_code, :PARAM_Qty, :PARAM_tax, :PARAM_Amount, :PARAM_remarks,:PARAM_ID, @Result)";
    $command=$connection->createCommand($sql);        
    $command->bindValue(':PARAM_CLASSIFICATION_CODE', $CLASSIFICATION_CODE);
    $command->bindValue(':PARAM_ITEM_CAT_CODE', $ITEM_CAT_CODE);
    $command->bindValue(':PARAM_ITEM_CODE', $ITEM_CODE);
    $command->bindValue(':PARAM_supplier_code', $supplier_code);
    $command->bindValue(':PARAM_Qty', $Qty);
    $command->bindValue(':PARAM_tax', $tax);
    $command->bindValue(':PARAM_Amount', $Amount);
    $command->bindValue(':PARAM_remarks', $remarks);
     $command->bindValue(':PARAM_ID', $id);       
   
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function get_camp_item(){
    $connection=   Yii::$app->db;
    $eid=Yii::$app->user->identity->e_id;
    $connection->open();
    $sql =" CALL `store_get_camp_item`(:param_emp_id)";
    $command = $connection->createCommand($sql);
    $command->bindValue(':param_emp_id', $eid);                 
    $result=$command->queryAll();
    $connection->close();  
    return $result;       
}

public function get_camp_supplier_details($Param_itemcode){
    $connection = Yii::$app->db;    
        $connection->open();
        // $sql="SELECT * FROM `store_item_details` WHERE item_code = $Param_itemcode";
        $sql="CALL `store_get_camp_supplier`(:Param_itemcode)";
        $command = $connection->createCommand($sql);
        $command->bindValue(':Param_itemcode', $Param_itemcode);
        $result=$command->queryAll();
        $connection->close();                
        return $result;       
}

 public function get_all_item_supllier_applicant($q_id=NULL){
        $connection = Yii::$app->db;
        $connection->open();
        $where ='';       
        $sql="SELECT * FROM `apply_quotation_form` aq LEFT JOIN store_pr_quotation_invite qi ON (aq.Q_id=qi.Q_id) ORDER BY aq.`id` ASC";
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();
        $connection->close();                
        return $result;
	} 
 public function get_camp_detail($id){
    $connection= Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_get_camp_detail`(:PARAM_id)";
    $command = $connection->createCommand($sql);      
    $command->bindValue(':PARAM_id', $id);
    $result=$command->queryOne();
    $connection->close();
    return $result;   
}
// Comparison Report End

 public function definalize_material_receipt($param_MRN_no,$ID){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_definalize_mat_receipts`(:Param_MRN_No,:Param_ID)";
    $command = $connection->createCommand($sql);
    $command->bindValue(':Param_MRN_No', $param_MRN_no);
    $command->bindValue(':Param_ID', $ID); 
    $command->execute();
    $connection->close();
    return 1;                   
}
// return 
public function get_return_request_status($eid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_return_request_status`(:emp_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':emp_id', $eid); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function add_return_request($data){
    extract($data);
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_return_request`(:Division,:Emp_code,:Classification_Code,:Item_Cat_Code,:Item_Code,
            :Item_Type,:Item_Type_Id,:Measuring_Unit,:Quantity_Required,:Item_Purpose,:Remarks,:Role,:Flag,:FLA,  @Result)";
    $command=$connection->createCommand($sql); 
    $command->bindValue(':Division', $Division);
    //$command->bindValue(':Cost_Centre_Code', $Cost_Centre_Code);
    $command->bindValue(':Emp_code', $Emp_code);
    $command->bindValue(':Classification_Code', $Classification_Code);
    $command->bindValue(':Item_Cat_Code', $Item_Cat_Code);
    $command->bindValue(':Item_Code', $Item_Code);
    $command->bindValue(':Item_Type', $Item_Type);
    $command->bindValue(':Item_Type_Id', $Item_Type_Id);
    $command->bindValue(':Measuring_Unit', $Measuring_Unit);
    $command->bindValue(':Quantity_Required', $Quantity_Required);
    $command->bindValue(':Item_Purpose', $Item_Purpose);
    $command->bindValue(':Remarks', $Remarks);
    $command->bindValue(':Role', $Role);
    $command->bindValue(':Flag', $Flag);
    $command->bindValue(':FLA', $FLA);
    //$command->bindValue(':Qty_Approved', $Qty_Approved);
    //$command->bindValue(':Approval_Date', $Approval_Date);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

public function save_return_request($data)
{
    extract($data);
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_save_return_request`(:voucher_no,:employee_code,:r_remark,:Flag,:FLA,:itemunqID,:rtn_qty,  @Result)";

    $command=$connection->createCommand($sql);
    $command->bindValue(':voucher_no', $voucher_no);
    $command->bindValue(':employee_code', $employee_code);
    $command->bindValue(':r_remark', $r_remark);
    $command->bindValue(':Flag', 1);
    $command->bindValue(':FLA', $FLA);
    $command->bindValue(':itemunqID', $itemunqID);
    $command->bindValue(':rtn_qty', $rtn_qty);          
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      

}   
 
public function get_list_return_vouchers(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT Voucher_No FROM `store_material_return_request`";
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function update_return_request_status($ret_ID,$currentUser,$status)
{
  $connection=   Yii::$app->db;
  $connection->open();
  $sql =" CALL `update_return_request_status`(:ret_id,:currentUser,:status,:ct_date, @Result)";
  $command = $connection->createCommand($sql); 
  $command->bindValue(':ret_id', $ret_ID); 
  $command->bindValue(':currentUser', $currentUser); 
  $command->bindValue(':status', $status); 
  $command->bindValue(':ct_date', date('Y-m-d')); 

  // echo $command->getRawSQL();die;
  $command->execute();
  $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
  $connection->close();
  return $valueOut;       
}

public function get_return_request_status_sel($vid){
  $connection=   Yii::$app->db;
  $connection->open();
  $sql =" CALL `store_return_request_status_sel`(:Voucher_No)";
  $command = $connection->createCommand($sql); 
  $command->bindValue(':Voucher_No', $vid); 
  $result=$command->queryAll();
  $connection->close();
  return $result;       
}
public function update_stock_after_return($Item_Code,$item_qty){ 
  $connection=   Yii::$app->db;
  $connection->open();   
  //  die("UPDATE `store_item_master` SET `Quantity` = (Quantity + ".$item_qty.") WHERE `store_item_master`.`ITEM_CODE` = ".$Item_Code.";");    
  $command = $connection->createCommand("UPDATE `store_item_master` SET `Quantity` = (Quantity + ".$item_qty.") WHERE `store_item_master`.`ITEM_CODE` = ".$Item_Code.";");
  $command->execute();        
}
  public function delete_stock_after_return($voucher_id){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql="UPDATE `store_material_return_request` SET deleted='Y' WHERE Voucher_No='".$voucher_id."'";
    $command = $connection->createCommand($sql); 
    $result=$command->execute();
    $connection->close();
    return $result;
}

// Methods added by Deepak Rathi on 01-Sep-2021

public function get_return_request_status_na($eid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_return_request_status_not_approved`(:emp_id)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':emp_id', $eid); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function get_return_request_status_na_sel($vid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_return_request_status_not_approved_sel`(:Voucher_No)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':Voucher_No', $vid); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}
public function get_list_return_vouchers_na(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT Voucher_No FROM `store_material_return_request` WHERE deleted='N'";
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

public function update_return_item_approved($voucher_id){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql="UPDATE `store_material_return_request` SET approval_by_procurement_offcr='Y' WHERE Voucher_No='".$voucher_id."'";
    $command = $connection->createCommand($sql); 
    $result=$command->execute();
    $connection->close();
    return $result;      
}
public function update_return_item_cancel($voucher_id){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql="UPDATE `store_material_return_request` SET approval_by_procurement_offcr='N' WHERE Voucher_No='".$voucher_id."'";
    $command = $connection->createCommand($sql); 
    $result=$command->execute();
    $connection->close();
    return $result;      
}


//FUNCTION UPDATE BY DEEPAK
public function get_item_detail_by_itemcode($Param_itemcode){
	  $connection = Yii::$app->db;
      $connection->open();
      $sql="SELECT * FROM `store_item_master` WHERE item_code = $Param_itemcode";
      // $sql="CALL `store_get_item_details_for_issue`(:Param_itemcode)";
      $command = $connection->createCommand($sql);
      $command->bindValue(':Param_itemcode', $Param_itemcode);
      $result=$command->queryOne();
      $connection->close();
      return $result;       
}

public function get_item_detail_by_itemid($Param_itemid){
	  $connection = Yii::$app->db;
      $connection->open();
		$sql="SELECT * FROM `store_item_master` WHERE itm_id = $Param_itemid";
      // $sql="CALL `store_get_item_details_for_issue`(:Param_itemcode)";
      $command = $connection->createCommand($sql);
      $command->bindValue(':Param_itemid', $Param_itemid);
      $result=$command->queryOne();
      $connection->close();
      return $result;       
}
//________________________



public function store_update_Item_details($data,$itm_id){
    extract($data);
    $connection=   Yii::$app->db;
    $connection->open();
	


   
    $data_item_name = addcslashes($data['item_name'],"'");
     if(empty($data['expiry_date_maint'])){
       $data['expiry_date_maint']='0000-00-00';
    }
	if(empty($data['item_store_id'])){
       $data['item_store_id']='0';
    }
	if(empty($data['rack_id'])){
       $data['rack_id']='0';
    }
	
    $sql ="UPDATE `store_item_master` SET 
    `CLASSIFICATION_CODE`= '".$data['group']."',
    `item_name` = '".$data_item_name."',
    `ITEM_CAT_CODE` = '".$data['category']."',
    `Item_type1` = '".$data['item_type']."',
    `Quantity` = '".$data['Quantity']."',
    `Measuring_Unit` = '".$data['units']."',
    `is_annual_maint` = '".$data['is_annual_maint']."',
    `expiry_date_maint` = '".$data['expiry_date_maint']."',
    `is_item_defactive` = '".$data['is_item_defactive']."',
    `item_store_id` = '".$data['item_store_id']."',
    `rack_id` = '".$data['rack_id']."',
    `item_safe_count` = '".$data['item_safe_count']."'   
    WHERE store_item_master.`itm_id` = ".$itm_id.";";
     // die($sql);die();
    $command = $connection->createCommand($sql); 
    $result=$command->execute();
    $connection->close();
    return $result;
}


public function store_Item_del($itm_id){
  
    $connection=   Yii::$app->db;
    $connection->open(); 
    
    $sql ="UPDATE `store_item_master` SET 
    `is_active`= 'N'
    WHERE store_item_master.`itm_id` = ".$itm_id.";";
    //die($sql);
    $command = $connection->createCommand($sql); 
    $result=$command->execute();
    $connection->close();
    return $result;
}


public function get_mon_invdata($param_role,$param_e_id,$frmDate,$toDate){
   $connection=   Yii::$app->db;
   $connection->open();
   $sql ="CALL `store_get_mon_invdata` (:param_role,:param_e_id,:params_frm_date,:params_to_date)";
   $command = $connection->createCommand($sql);
   $command->bindValue(':param_role', $param_role);
   $command->bindValue(':param_e_id', $param_e_id);
    $command->bindValue(':params_frm_date',$frmDate);
   $command->bindValue(':params_to_date',$toDate);
   $result=$command->queryAll();
   $connection->close();
   return $result;      
}

public function get_quotation_applicants_info($arrData){

    //pr($arrData);die;
    $result = array();
    
    $filename = $arrData['FILES']['ApplyQuotationForm'];

    $connection= Yii::$app->db;
    $connection->open(); 
    
    $sql ="
    INSERT INTO `apply_quotation_form`(`Q_id`,`supplier_name`,`supplier_address`,`supplier_phone`,`price_quote`,`upload_doc`,`created`) 
    VALUES ('".$arrData['ApplyQuotationForm']['Q_id']."','".$arrData['ApplyQuotationForm']['supplier_name']."','".$arrData['ApplyQuotationForm']['supplier_address']."','".$arrData['ApplyQuotationForm']['supplier_phone']."','".$arrData['ApplyQuotationForm']['price_quote']."','".$arrData['FILES']['Newname']."','".date("Y-m-d H:i:s")."');
    ";
    
    $command = $connection->createCommand($sql); 
    $result=$command->execute();
    $connection->close();
    
    return $result;


}

public function get_applicants_for_Quotation($qid){

    $connection = Yii::$app->db;
    $connection->open();
    $sql="SELECT * FROM `apply_quotation_form` aq LEFT JOIN store_pr_quotation_invite qi ON (aq.Q_id=qi.Q_id) WHERE aq.Q_id=".$qid;
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result;

}

public function actionGet_quotation_applicant($qid){

    $connection = Yii::$app->db;
    $connection->open();
    $sql="SELECT * FROM `apply_quotation_form` WHERE `Q_id`=".$qid;
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result;

}

public function get_quotation_details($qid){
    $connection = Yii::$app->db;
    $connection->open();
    $sql="SELECT * FROM `store_pr_quotation_invite` WHERE `Q_id`=".$qid;
    $command = $connection->createCommand($sql);
    $result=$command->queryOne();
    $connection->close();
    return $result;

}

/*get Lot Details By dharamveer Singh*/
public function get_lotDetails($item_codedata){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="CALL `store_get_lot_details`(:item_codedata)";
    $command = $connection->createCommand($sql);
    $command->bindValue(':item_codedata', $item_codedata);
    //$command->bindValue(':class_code', $class_code);                
    $result=$command->queryAll();
    $connection->close();
    return $result;       
}

    public function get_inwardstockdata($group_name , $cat_name){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `get_inwardstock_details`(:group_name,:cat_name)";
        $command = $connection->createCommand($sql); 
         $command->bindValue(':group_name', $group_name); 
         $command->bindValue(':cat_name', $cat_name); 
        $result=$command->queryAll();
        $connection->close();
        return $result;       
    }

    public function get_lot_data_byid($item_code){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT item_unique_id FROM store_item_details WHERE `item_code`='".$item_code."'";

    $command = $connection->createCommand($sql); 
    $result=$command->queryOne();
    $connection->close();
    return $result;       
    }

    /* by dharamveer 22-08-2022*/
    public function purchaseindentdata_requests($req_id=NULL){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql =" CALL `store_purchaseindentdata_requests`(:req_id)";
        $command = $connection->createCommand($sql); 
        $command->bindValue(':req_id', $req_id); 
        if($req_id)
        $result=$command->queryOne();
        else
        $result=$command->queryAll();
        
        $connection->close();
        return $result;       
    }
    
    
    public function get_supplier_master(){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT * FROM store_supplier_master";
    $command = $connection->createCommand($sql); 
    $result=$command->queryAll();
    $connection->close();
    return $result;       
    }
    
    public function get_allpurchaseorder(){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="CALL `store_get_purchase_order`()";
        $command = $connection->createCommand($sql);
        // $command->bindValue(':Param_Q_id', $Qid);
        $result=$command->queryAll();
        $connection->close();
        return $result;

}
    
    public function get_data_byid($table,$field,$itemid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql ="SELECT * FROM `".$table."` WHERE `".$field."`='".$itemid."'";

    $command = $connection->createCommand($sql); 
    $result=$command->queryOne();
    $connection->close();
    return $result;       
    }
    
    public function getlasttableid($table){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM $table ORDER BY id DESC LIMIT 1";
        $command = $connection->createCommand($sql); 
        $result=$command->queryOne();
        $connection->close();
        if(!empty($result)){
            return $result['id'];       
        }else{
            return 0;
        }   
    }
/*get Lot Details*/

/* by dharamveer 24-08-2022*/
    public function get_inactive_cat_item($cat_code,$class_code){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="CALL `store_get_inactivecat_item`(:cat_code,:class_code)";
        $command = $connection->createCommand($sql);
        $command->bindValue(':cat_code', $cat_code);
        $command->bindValue(':class_code', $class_code);                
        $result=$command->queryAll();
        $connection->close();
        return $result;     
    }
    
    public function update_itemapprovedisapprove($itemid){
        $connection=   Yii::$app->db;
        $connection->open(); 
    
        $sql ="UPDATE `store_item_master` SET 
        `is_active`= 'Y'
        WHERE store_item_master.`itm_id` = ".$itemid.";";
        // die($sql);
        $command = $connection->createCommand($sql); 
        $result=$command->execute();
        $connection->close();
        return $result;
    }
    
    public function store_item_delete($itm_id){
  
    $connection=   Yii::$app->db;
    $connection->open(); 
    
    $sql ="DELETE FROM `store_item_master` WHERE  `itm_id` = ".$itm_id.";";
    // die($sql);
    $command = $connection->createCommand($sql); 
    $result=$command->execute();
    $connection->close();
    return $result;
}
    
    
    /* by dharamveer 24-08-2022*/
    /* by dharamveer 30-08-2022*/
	  public function get_issued_mat($startdate,$enddate){
		
			$connection=   Yii::$app->db;
			$connection->open();
			$sql =" CALL `get_issued_mat`(:startdate,:enddate)";
			$command = $connection->createCommand($sql); 
			$command->bindValue(':startdate', $startdate);
			$command->bindValue(':enddate', $enddate);
			$result=$command->queryAll();
			$connection->close();
			//print_r($result);die;
			return $result;   
		  }
    /* by dharamveer 30-08-2022*/
	
	public function get_item_supllier_applicant($q_id=NULL){
        $connection = Yii::$app->db;
        $connection->open();
        $where ='';       
        $sql="SELECT * FROM `apply_quotation_form` where Q_id = '".$q_id."'";
        $command = $connection->createCommand($sql);
        $result=$command->queryAll();
        $connection->close();                
        return $result;
	}

	public function get_empdeptAlldetail($eid){
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `get_employees`(:param_employee_code)";
    $command = $connection->createCommand($sql); 
    $command->bindValue(':param_employee_code', $eid);
    $result=$command->queryOne();
    $connection->close();
    // echo'<pre>'; print_r($result);die;
    return $result;   
}

	public function getlasttableiddata($table){
        $connection=   Yii::$app->db;
        $connection->open();
        $sql ="SELECT * FROM $table ORDER BY Q_id DESC LIMIT 1";
        $command = $connection->createCommand($sql); 
        $result=$command->queryOne();
		// pr($result);die();
        $connection->close();
        if(!empty($result)){
            return $result['q_id'];       
        }else{
            return 0;
        }   
    }
	
	public function get_dataAlldata($qid){
		$connection=   Yii::$app->db;
		$connection->open();
		$sql ="SELECT * FROM `apply_quotation_form` where Q_id = '".$qid."' order by price_quote ASC limit 3 ";
		$command = $connection->createCommand($sql); 
		$result=$command->queryAll();
		$connection->close();
		return $result;       
    }
	
	public function get_quotationindentNo($q_id=NULL){
		$connection = Yii::$app->db;
        $connection->open();
        $where ='';       
        $sql="SELECT * FROM `store_pr_quotation_invite` where Q_id = '".$q_id."'";
        $command = $connection->createCommand($sql);
        $result=$command->queryOne();
        $connection->close();                
        return $result;
	}
	
	/*Dharamveer Code 02-09-2022*/
	public function store_Item_reserve($itm_id){
		$connection=   Yii::$app->db;
		$connection->open(); 
        $sql ="UPDATE `store_item_master` SET `reserve_or_not`= '1' WHERE store_item_master.`itm_id` = ".$itm_id.";";
        $command = $connection->createCommand($sql); 
		$result=$command->execute();
		$connection->close();
		return $result;
	}
	public function store_Item_unreserve($itm_id){
		$connection=   Yii::$app->db;
		$connection->open(); 
        $sql ="UPDATE `store_item_master` SET `reserve_or_not`= '0' WHERE store_item_master.`itm_id` = ".$itm_id.";";
        $command = $connection->createCommand($sql); 
		$result=$command->execute();
		$connection->close();
		return $result;
	}
	/*Dharamveer Code 02-09-2022*/
	/*Dharamveer Code 07-09-2022*/
	public function insert_maintencehistory($data){
	extract($data);
	$connection=   Yii::$app->db;
    $connection->open();
	$sql ="CALL `store_insert_material_historydetials`(:PARAM_expiry_date_maint,:PARAM_is_annual_maint,:PARAM_MRN_No,:PARAM_Dept_code,:PARAM_Emp_code,:PARAM_ITEM_CODE,:PARAM_lastid, @Result)";
	$command=$connection->createCommand($sql); 
    $command->bindValue(':PARAM_expiry_date_maint', $expiry_date_maint);
    $command->bindValue(':PARAM_is_annual_maint', $is_annual_maint);
    $command->bindValue(':PARAM_MRN_No', $MRN_No);
    $command->bindValue(':PARAM_Dept_code', $Dept_code);
    $command->bindValue(':PARAM_Emp_code', $Emp_code);
    $command->bindValue(':PARAM_ITEM_CODE', $ITEM_CODE);
    $command->bindValue(':PARAM_lastid', $lastid);
    $command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;      
}

    public function insert_expirydatedata($data){
		extract($data);
		$connection=   Yii::$app->db;
		$connection->open();
		$sql="INSERT INTO `expirydate_histry`( `lastid`, `MRN_No`, `Dept_code`, `Emp_code`, `ITEM_CODE`, `expiry_date`) VALUES  ('".$data['lastid']."','".$data['MRN_No']."','".$data['Dept_code']."','".$data['Emp_code']."','".$data['ITEM_CODE']."','".$data['expiry_date']."')";
        $command = $connection->createCommand($sql); 
        $result=$command->execute();
	}


		public function delete_mat_histrecord($ID){
			$connection=   Yii::$app->db;
			$connection->open();
			$sql ="DELETE FROM `maintenance_histry` WHERE  lastid = '".$ID."'";
			$sql ="DELETE FROM `expirydate_histry` WHERE  lastid = '".$ID."'";
			$command = $connection->createCommand($sql); 
			$result=$command->execute();
			$connection->close();
			
			return 1;   
		}

public function get_renew_contracts(){
    $connection = Yii::$app->db;
    $connection->open();
    //$sql="select * from store_item_master WHERE expiry_date_maint BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 2 MONTH) AND `is_annual_maint`='Y'";
    $sql="select * from maintenance_histry WHERE expiry_date_maint BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 2 MONTH) AND `is_annual_maint`='Y'";
    // $sql="CALL `store_get_item_details_for_issue`(:Param_itemcode)";
    $command = $connection->createCommand($sql);
    $result=$command->queryAll();
    $connection->close();
    return $result; 
}

	/*Dharamveer Code 07-09-2022*/
	/*Dharamveer Code 20-09-2022*/
	public function mailsendfun($content, $to, $subject){
			require './PHPMailer/class.phpmailer.php';
			require_once './PHPMailer/PHPMailerAutoload.php';
                $mail = new \PHPMailer;  
				$mail->SMTPDebug = 0;
				$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);// Enable verbose debug output
			$mail->isSMTP();                                            // Send using SMTP
			$mail->Host       = 'mail.haryanadial112.com';                    // Set the SMTP server to send through
			//$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
			$mail->SMTPAuth = true;
			$mail->SMTPAutoTLS = false; 
			
			$mail->Username   = 'haryanad';                     // SMTP username
			$mail->Password   = 'Q79q*rg6EMs9:M';                                // SMTP password
			$mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			$mail->Port       = 587;
			$mail->Timeout 	  = 7200;       
			$mail->addAddress($to);     // Add a recipient
			$mail->setFrom('dev@cdac.com','');
			$mail->isHTML(true);
			$mail->Body = $content;
			$mail->Subject = $subject;
			 $mail->send();
			// if($mail->send()){
				// echo 'emailsend';
           // }else{
             // echo "Mailer Error: " . $mail->ErrorInfo;
             // die;
           // }
    } 
        

	
	
	/*Dharamveer Code 20-09-2022*/


	public function store_insert_logistics_details($data){
		
		extract($data);
		// pr($data);  
					
					// die();
		
		
    $connection=   Yii::$app->db;
    $connection->open();
    $sql =" CALL `store_insert_logistics_details`(:PARAMS_challan_type,:PARAMS_challan_number,:PARAMS_depart_dilivry_Add,:PARAMS_vehicleNo,:PARAMS_challan_date,:PARAMS_transport_phone,:PARAMS_comments,:PARAMS_itemDtl,:PARAMS_created_by,:PARAMS_concrnOfiice_name,:PARAMS_concernoffice_phone,:PARAMS_expected_return_date,@Result)";
    $command=$connection->createCommand($sql);
    $command->bindValue(':PARAMS_challan_type',$challan_type);
    $command->bindValue(':PARAMS_challan_number',$challan_number);
    $command->bindValue(':PARAMS_depart_dilivry_Add',$depart_dilivry_Add);
    $command->bindValue(':PARAMS_vehicleNo',$vehicleNo);
    $command->bindValue(':PARAMS_challan_date',$challan_date);
    $command->bindValue(':PARAMS_transport_phone',$transport_phone);
    $command->bindValue(':PARAMS_comments',$comments);
    $command->bindValue(':PARAMS_itemDtl',$itemDtl);
    $command->bindValue(':PARAMS_created_by',$created_by);
    $command->bindValue(':PARAMS_concrnOfiice_name',$concrnOfiice_name);
    $command->bindValue(':PARAMS_concernoffice_phone',$concernoffice_phone);
    $command->bindValue(':PARAMS_expected_return_date',$expected_return_date);
	$command->execute();
    $valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
    $connection->close();
    return $valueOut;
		
	}

	public function get_outward_list(){       
		$connection=   Yii::$app->db;
		$connection->open();
		$sql =" CALL `get_outward_list`()";
		$command = $connection->createCommand($sql);        
		$result=$command->queryAll();
		$connection->close();        
		return $result;   
	}
	
	 public function data_delete($table,$field,$id){
			$connection=   Yii::$app->db;
			$connection->open(); 
			$sql ="DELETE FROM ".$table." WHERE  ".$field." = ".$id."";
			$command = $connection->createCommand($sql); 
			$result=$command->execute();
			$connection->close();
			return $result;
		}
	
	/* Update Challan Quantity By Dharamveer 14-10-2022 */
		public function updatestockqtyBy_Challan($table,$itemCode,$remQty){
			
				$connection=   Yii::$app->db;
				$connection->open();    
				$sql ="UPDATE `".$table."` SET 
				`Quantity`=".$remQty."
				 WHERE `ITEM_CODE`= ".$itemCode."";
				 
				 // echo $sql;die();
				$command=$connection->createCommand($sql);     
				$command->execute();
				$valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
				$connection->close();
				return $valueOut;      
			}
		public function get_challandtl_by_id($challanid){
			  $connection = Yii::$app->db;
			  $connection->open();
			  $sql="SELECT * FROM `outward_challan` WHERE id = $challanid";
			  // $sql="CALL `store_get_item_details_for_issue`(:Param_itemcode)";
			  $command = $connection->createCommand($sql);
			  //$command->bindValue(':Param_challanid', $Param_challanid);
			  $result=$command->queryOne();
			  $connection->close();
			  return $result;       
		}	
		
			
		public function updatechallanitemqty($table,$id,$datat){
		
				$connection=   Yii::$app->db;
				$connection->open();    
				$sql ="UPDATE `".$table."` SET 
				`itemDtl`= '".$datat['itemDtl']."',
				`return_or_not`='Y'
				 WHERE `id`= ".$id."";
				
				$command=$connection->createCommand($sql);     
				$command->execute();
				$valueOut = $connection->createCommand("select @Result as res;")->queryScalar();
				$connection->close();
				return $valueOut;      
			}	
			
			
			
			
	/* Update Challan Quantity By Dharamveer 14-10-2022 */	
	/* Code Dharamveer 03-11-2022 */	
				public function get_ervNumber(){
				$connection=   Yii::$app->db;
				$connection->open();
				$sql ="SELECT * From vehicle_reg_no";
				$command = $connection->createCommand($sql); 
				$result=$command->queryAll();
				$connection->close();
				return $result;       
			}
	/* Code By Dharamveer 03-11-2022 */	
		
		
		
	
	

    
}
