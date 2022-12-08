<?php

namespace app\modules\inventory\controllers;
use Yii;
use yii\web\Controller;
use app\models\Item; 

class ItemController extends Controller
{
	public function beforeAction($action){
		$url =Yii::$app->homeUrl;
        if (!\Yii::$app->user->isGuest) {
            if(isset($_GET['securekey']) AND !empty($_GET['securekey'])){
                $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
				
                if(empty($menuid)){ 
 					header("Location: $url",  true,  301 );die;	
					//return $this->redirect(Yii::$app->homeUrl);
				}
                 $chkValid = Yii::$app->utility->validate_url($menuid);
                 if(empty($chkValid)){ return $this->redirect(Yii::$app->homeUrl); }
                return true;
            }else{ return $this->redirect(Yii::$app->homeUrl); }
        }else{
             header("Location: $url");die;
        }
        parent::beforeAction($action);
    }

   public function actionIndex()
    {
        $this->layout = '@app/views/layouts/admin_layout.php';    
         //return $this->render('index');
         $groups=Yii::$app->inventory->get_groups();
	     $category=Yii::$app->inventory->get_category();
         //echo "<pre>";print_r($category);die;
         $lists = Yii::$app->inventory->get_cat_item(NULL,NULL); //Inventoryutility
        $inactivelists = Yii::$app->inventory->get_inactive_cat_item(NULL,NULL); //Inventoryutility
        $connection=   Yii::$app->db;
        // $connection->open(); 
         $sql =" SELECT Count(is_active) FROM `store_item_master` WHERE `is_active` = 'N'";
         $command = $connection->createCommand($sql); 
         $countInactive=$command->queryOne();
         return $this->render('index', ['groups'=>$groups,'category'=>$category,'lists'=>$lists,'inactivelists'=>$inactivelists,'countInactive'=>$countInactive]);
    }

    public function actionViewfilter()
    {
        $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
        $menuid = Yii::$app->utility->encryptString($menuid);
        $this->layout = '@app/views/layouts/admin_layout.php';    
        $groups=Yii::$app->inventory->get_groups();
        $category=Yii::$app->inventory->get_category();
        //echo "<pre>";print_r($category);die;
        $cat_code=$_REQUEST['cat_id'];
        $class_code=$_REQUEST['ccode'];

        $item_code=$_REQUEST['cat_id'];

        //  pr($category);die;
        $lists = Yii::$app->inventory->get_comparison_items($cat_code,$class_code); //Inventoryutility    
        //pr($lists);die;
        $html = $this->renderPartial('viewfilter', ['groups'=>$groups,'category'=>$category,'lists'=>$lists,'menuid'=>$menuid]);
        $allConcat['result'] = $html;
        //echo json_encode($allConcat);
        echo $allConcat['result'];
        die();
    } 

    public function actionGet_item_detail_rec()
    {
        //echo "<pre>";print_r(Yii::$app->user->identity);die;
        $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
        $menuid = Yii::$app->utility->encryptString($menuid);
        
        $data = array();

        if(isset($_POST) && !empty($_POST))
        {
            $itmtype_id = $_POST['itmtype_id'];
            $itemcode = $_POST['itemcode'];
            // $lists = Yii::$app->inventory->actionGet_item_detail_rec($itemcode); //Inventoryutility  
			$lists = Yii::$app->inventory->get_item_detail_by_itemcode($itemcode); //Inventoryutility			
            // 
            $html = $this->renderPartial('item_data', array('item_data'=>$lists));
			// pr($html);die('xxxxxxxx');
            $allConcat['result'] = $html;

        }
        echo json_encode($allConcat);
        die();
    }


    public function actionAdd()
    {

    //echo "<pre>";print_r(Yii::$app->user->identity);die;
    $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
    $menuid = Yii::$app->utility->encryptString($menuid);
    $this->view->title = 'Add New Item';
    $this->layout = '@app/views/layouts/admin_layout.php';
	if(isset($_POST) && !empty($_POST)){
     
         
		 
		  
			$post=$_POST['Item'];
			
			
			
			 if($post['is_annual_maint'] ==''){
			  $post['is_annual_maint'] = 'N';
		  }
			unset($_POST['Item']);
			$data['Classification_Code']    =$post['group'];
			$data['Item_Cat_Code']		=$post['category'];
            $data['item_name']		=$post['item_name'];
			$data['Item_type']		=$post['item_type'];
			$data['Measuring_Unit']		=$post['units'];
           // $data['expiry_date_maint']   =$post['expiry_date_maint'];
            $data['expiry_date_maint']   =date('Y-m-d');
            $data['is_annual_maint']     =$post['is_annual_maint'];
            $data['is_item_defactive']     =$post['is_item_defactive'];
            $data['item_store_id']     =$post['item_store_id'];
            $data['rack_id']     =$post['rack_id'];
           
			
			
			if($post['item_safe_count'] == ''){
				$data['item_safe_count']     = 0;
			}else{
             $data['item_safe_count']     = $post['item_safe_count'];
			}
			
			if($post['item_store_id'] == ''){
                $data['item_store_id']     = 0;
            }else{
             $data['item_store_id']     =$post['item_store_id'];
            }
			
			
			

		 // echo "<pre>";print_r($data);die;
			$res=Yii::$app->inventory->store_insert_Item_details($data);
			if($res == '1'){
			    Yii::$app->getSession()->setFlash('success', 'Item added successfully');
			    return $this->redirect(Yii::$app->homeUrl."inventory/item?securekey=".$menuid); 
			}
			else {
			Yii::$app->getSession()->setFlash('danger', 'Invalid / Empty params found');
			return $this->redirect(Yii::$app->homeUrl."inventory/item?securekey=".$menuid); 
		      }					
		}
        $store_loc=Yii::$app->inventory->get_store_loc();
        $rack_loc=Yii::$app->inventory->get_rack_loc();

		
		$groups=Yii::$app->inventory->get_groups();
		$category=Yii::$app->inventory->get_category();
		$cost_centre=array();//Yii::$app->inventory->get_cost_centre();
		$unit_master=Yii::$app->inventory->get_unit_master();
        $itemtype_master=Yii::$app->inventory->get_item_type();
 		$model = new Item();

        return $this->render('add', ['model'=>$model,'groups'=>$groups,'category'=>$category,'cost_centre'=>$cost_centre,'unit_master'=>$unit_master,'itemtype_master'=>$itemtype_master,'menuid'=>$menuid,'store_loc'=>$store_loc,'rack_loc'=>$rack_loc]);
    }

    public function actionUpdate(){
		
       
        $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
        $menuid = Yii::$app->utility->encryptString($menuid);
        $this->view->title = 'Update Item';
        $this->layout = '@app/views/layouts/admin_layout.php';
        $item_code = $_REQUEST['item_code'];
        $item_id = $_REQUEST['item_id'];
       // $lists = Yii::$app->inventory->get_item_detail_by_itemcode($item_code); //Inventoryutility
		
        $lists = Yii::$app->inventory->get_item_detail_by_itemid($item_id); //Inventoryutility
        //UPDATE THE RECORDS...
        // pr();die();
     // pr($_POST['Item']);die('HMMM');
      
        if(isset($_POST) && !empty($_POST)){
               

                //UPDATE ITEM DETAILS....
                $itemupdate = $_POST['Item'];

                $res=Yii::$app->inventory->store_update_Item_details($itemupdate,$item_id);
                //-----------------------
                
           
                if($res == '1'){
                Yii::$app->getSession()->setFlash('success', 'Item code : '.$item_code.' updated successfully');
                return $this->redirect(Yii::$app->homeUrl."inventory/item?securekey=".$menuid); 
                }
                else {
                Yii::$app->getSession()->setFlash('success', 'Not any change applied.');
                return $this->redirect(Yii::$app->homeUrl."inventory/item?securekey=".$menuid);  
                } 
        }

        $groups=Yii::$app->inventory->get_groups();
      
        
        $category=Yii::$app->inventory->get_category();
        //pr($category);
        $cost_centre=array();//Yii::$app->inventory->get_cost_centre();
        $unit_master=Yii::$app->inventory->get_unit_master();
        $itemtype_master=Yii::$app->inventory->get_item_type();

        $store_loc=Yii::$app->inventory->get_store_loc();
        $rack_loc=Yii::$app->inventory->get_rack_loc();

        $model = new Item();
        $model->CLASSIFICATION_CODE = $lists['classification_code'];
        $model->ITEM_CAT_CODE = $lists['item_cat_code'];
        $model->item_name = $lists['item_name'];
        $model->Item_type1 = $lists['item_type1'];
        $model->Quantity = $lists['quantity'];
        $model->Measuring_Unit = $lists['measuring_unit'];
        $model->is_annual_maint = $lists['is_annual_maint'];
        $model->expiry_date_maint = $lists['expiry_date_maint'];
        $model->is_item_defactive = $lists['is_item_defactive'];
        $model->item_store_id = $lists['item_store_id'];
        $model->rack_id = $lists['rack_id'];
        $model->item_safe_count = $lists['item_safe_count'];
		
		
		  // pr($lists);die('ss');

        return $this->render('update', ['lists'=>$lists,'model'=>$model,'groups'=>$groups,'category'=>$category,'cost_centre'=>$cost_centre,'unit_master'=>$unit_master,'itemtype_master'=>$itemtype_master,'menuid'=>$menuid,'store_loc'=>$store_loc,'rack_loc'=>$rack_loc]);
    }
	
	
    public function actionDelitem(){

        $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
        $menuid = Yii::$app->utility->encryptString($menuid);
        $this->view->title = 'Update Item';
        $this->layout = '@app/views/layouts/admin_layout.php';
        $item_code = $_REQUEST['item_code'];
        $item_id = $_REQUEST['item_id'];

        //DEL ITEM
        $res=Yii::$app->inventory->store_Item_del($item_id);
        if($res == '1'){
            Yii::$app->getSession()->setFlash('success', 'Item code : '.$item_code.' removed successfully');

        }
        return $this->redirect(Yii::$app->homeUrl."inventory/item?securekey=".$menuid); 


    }
	
	public function actionLotmanage(){
		 $this->layout = '@app/views/layouts/admin_layout.php';    
         $groups=Yii::$app->inventory->get_groups();
	     $category=Yii::$app->inventory->get_category();
        $lists = Yii::$app->inventory->get_cat_item(NULL,NULL); //Inventoryutility
         // echo "<pre>";print_r($lists);die;
         return $this->render('lotmange', ['groups'=>$groups,'category'=>$category,'lists'=>$lists]);
		
	}
	
	
	public function actionLogmanage(){
		$this->layout = '@app/views/layouts/admin_layout.php';
		$group_name = $cat_name = 0;
		$inwardata=Yii::$app->inventory->get_inwardstockdata($group_name,$cat_name);
		$eid = Yii::$app->user->identity->e_id;
		$roleID = Yii::$app->user->identity->role;
		// if($roleID == 3){
			//$data=Yii::$app->inventory->get_met_issue_request_status($eid);
		// }else{
			 $data=Yii::$app->inventory->get_request_data($roleID,$eid);
		// }
        return $this->render('logmange', ['outwarddata'=>$data,'inwardatadata'=>$inwardata]);
	}
	
	
	public function actionOutward(){
		$this->layout = '@app/views/layouts/admin_layout.php';
		$eid = Yii::$app->user->identity->e_id;
		$roleID = Yii::$app->user->identity->role;
		$data=Yii::$app->inventory->get_request_data($roleID,$eid);
		return $this->render('outward', ['data'=>$data]);
	}
	
	public function actionInward(){
		$this->layout = '@app/views/layouts/admin_layout.php';
		$connection=   Yii::$app->db;
		$connection->open();		
		$sql =" SELECT concat(b.fname,b.lname) as emp_name,c.item_name,d.ITEM_CAT_NAME,e.Supplier_name,e.Supplier_address,a.*
			FROM `store_mat_receipt` a 
			LEFT JOIN employee AS b ON b.employee_code=a.Emp_code
			LEFT JOIN store_item_master AS c ON c.ITEM_CODE=a.ITEM_CODE
			LEFT JOIN store_item_cat_master AS d ON d.ITEM_CAT_CODE=a.ITEM_CAT_CODE
			LEFT JOIN store_supplier_master AS e ON e.Supplier_Code=a.Supplier_Code";

		if(isset($_POST['frmDate']) && isset($_POST['toDate'])){
			// $sql.=" and a.CLASSIFICATION_CODE=".$_POST['group_name'];
			$sql.="  WHERE a.Inspection_Approval_Date >=  '".$_POST['frmDate'].' 01:00:00'."'  AND a.Inspection_Approval_Date <= '".$_POST['toDate'].' 23:59:59'."'";
		}
		
		
		$command = $connection->createCommand($sql); 
		$result=$command->queryAll();
		$connection->close();
		$groups=Yii::$app->inventory->get_groups();
		$category=Yii::$app->inventory->get_category();
		return $this->render('inward', ['data'=>$result,'groups'=>$groups,'category'=>$category]);
	}
	
	
	public function actionApprovedisapprve_item_detail_rec(){
        $this->view->title = 'Approve Item';
        $this->layout = '@app/views/layouts/admin_layout.php';
        $item_code = $_REQUEST['item_code'];
        $itemid = $_REQUEST['item_id'];
        $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
        $menuid = Yii::$app->utility->encryptString($menuid);
        $res=Yii::$app->inventory->update_itemapprovedisapprove($itemid);
         if($res == '1'){
          Yii::$app->getSession()->setFlash('success', 'Item code : '.$item_code.' Disapprove successfully');

        }
        return $this->redirect(Yii::$app->homeUrl."inventory/item?securekey=".$menuid); 
    }
    
    public function actionDeleteitemrec(){

        $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
        $menuid = Yii::$app->utility->encryptString($menuid);
        $this->view->title = 'Delete Item';
        $this->layout = '@app/views/layouts/admin_layout.php';
         $item_code = $_REQUEST['item_code'];
        $item_id = $_REQUEST['item_id'];

        //DEL ITEM
        $res=Yii::$app->inventory->store_Item_delete($item_id);
        if($res == '1'){
          Yii::$app->getSession()->setFlash('success', 'Item code : '.$item_code.' Delete successfully');

        }
        return $this->redirect(Yii::$app->homeUrl."inventory/item?securekey=".$menuid); 


    }
	
	
	public function actionReservitem(){
        $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
        $menuid = Yii::$app->utility->encryptString($menuid);
        $this->view->title = 'Update Item';
        $this->layout = '@app/views/layouts/admin_layout.php';
        $item_code = $_REQUEST['item_code'];
        $item_id = $_REQUEST['item_id'];
        $res=Yii::$app->inventory->store_Item_reserve($item_id);
        if($res == '1'){
            Yii::$app->getSession()->setFlash('success', 'Item code : '.$item_code.' Reserved successfully');
        }
        return $this->redirect(Yii::$app->homeUrl."inventory/item?securekey=".$menuid); 
	}
	public function actionUnreservitem(){
        $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
        $menuid = Yii::$app->utility->encryptString($menuid);
        $this->view->title = 'Update Item';
        $this->layout = '@app/views/layouts/admin_layout.php';
        $item_code = $_REQUEST['item_code'];
        $item_id = $_REQUEST['item_id'];
        $res=Yii::$app->inventory->store_Item_unreserve($item_id);
        if($res == '1'){
            Yii::$app->getSession()->setFlash('success', 'Item code : '.$item_code.' Unreserved successfully');
        }
        return $this->redirect(Yii::$app->homeUrl."inventory/item?securekey=".$menuid); 
	}
	
	
	
	
	
	
	


}
