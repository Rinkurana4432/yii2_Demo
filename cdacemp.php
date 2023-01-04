<?php
	$this->title= 'Manage Employees ';
	$dept_id = '101';
	$lists = Yii::$app->inventory->get_dept_emp($dept_id);
	//pr($lists);die('xxxx');
	//$lists = Yii::$app->utility->get_employees(null);
	$menuid = "";
	if(isset($_GET['securekey']) AND !empty($_GET['securekey'])){
	    $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
	    if(empty($menuid)){
	        header('Location: '.Yii::$app->homeUrl); 
	        exit;
	    }
	    $menuid = Yii::$app->utility->encryptString($menuid);
	}

	$depts = Yii::$app->utility->get_cdac_dept();
	$unitdetails = Yii::$app->utility->getUnitDetails(12);
	
?>
<input type="hidden" id="menuid" value="<?=$menuid?>" />
<div class="row" style="margin-bottom: 10px;">
	<div class="col-sm-6" style="">
		<div class="col-sm-6" style="float:left;">
			<label class="control-label" for="department_data">Department</label>
			<select class="form-control form-control-sm" name="forward_to[dept_id]" id="dept_id" required="required" onchange="dptChange(this.value)">
        <option value="-1">Select Department</option>
        <?php 
	        if(!empty($depts)){
            foreach($depts as $d){
              echo "<option value='$d[dept_id]'>$d[dept_name]</option>";
            }
	        }
        ?>
	    </select>
      <span id="dpts_error" style="display: none;">Please Select Department.</span>
		</div>
		<!-- <div class="col-sm-6" style="float:left;">
      <label>Designation</label>
      <select class="form-control form-control-sm" id="desg_id" name="forward_to[designation_id]" required="required" onchange="dsgChange(this.value)">
          <option value="-1">Select Designation</option>
      </select>
      <span id="dsg_error" style="display: none;">Please Select Designation.</span>
    </div> -->
	</div>
	<div class="col-sm-6 text-right">
		<a style="margin-bottom:10px;" href="<?=Yii::$app->homeUrl?>admin/manageemployees/add_cdac?securekey=<?=$menuid?>" class="btn btn-success btn-sm mybtn">Add New Employee</a>	
	</div>
</div>
<div class="row">
	<div class="col-sm-12" id="employee_data_disp">
		<table id="dataTableShow" class="display" style="width:100%" border="0">
			<thead>
				<tr>
					<th>Sr.</th>
					<th>Employee Code</th>
					<th>Designation</th>
					<th>Name</th>
					<th><div align="center">Action</div></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if(!empty($lists)){
					$i =1;
					foreach($lists as $l){ 
					$name = $l['name'];
					$employee_code = $l['employee_code'];
					$desg_name = $l['desg_name'];
					
					//$dob = date('d-m-Y', strtotime($l['dob']));
					$empltype="-";
					// if($l['employment_type'] == 'R'){
					// 	$empltype="Regular";
					// }elseif($l['employment_type'] == 'C'){
					// 	$empltype="Contract";
					// } if(!empty($l['unit'])){}
					//$encry = Yii::$app->utility->encryptStringUrl($l['e_id']);
					if($i%2==0){
							$csstext = '';
					}else{
							$csstext = '';
					}
					$encry = Yii::$app->utility->encryptString($l['employee_code']);
					$viewUrl = Yii::$app->homeUrl."admin/manageemployees/viewemployee_cdac?securekey=$menuid&empid=$encry";
					$editUrl = Yii::$app->homeUrl."admin/manageemployees/updateemployee_cdac?securekey=$menuid&empid=$encry";
					// ERV API DETAILS
					$method = 'GET';
					$apiurl = API_URL.'?agentId='.$employee_code;
					$ervAPIdetails = Yii::$app->utility->ervApiCall($method,$apiurl,$data=NULL,$employee_code);
					$ERV_NO = '';
					$PLACE_OF_POSTING = '';
					
					if(isset($ervAPIdetails['httpStatus'])){
						//pr($ervAPIdetails);die('xxxx');
						if(isset($ervAPIdetails['payLoad'][0]['vehicleDetails']['vehicleRegistration'])){
							$ERV_NO = $ervAPIdetails['payLoad'][0]['vehicleDetails']['vehicleRegistration'];
						}
						if(isset($ervAPIdetails['payLoad'][0]['district'])){
							$PLACE_OF_POSTING = $ervAPIdetails['payLoad'][0]['district'];
						}
						
						//$ERV_NO = $ervAPIdetails['payLoad'][0]['vehicleDetails']['vehicleRegistration'];
						//$PLACE_OF_POSTING = $ervAPIdetails['payLoad'][0]['district'];
					}
					
					//die('xxx');
					/*
					if(isset($ervAPIdetails)){
					$ERV_NO = $ervAPIdetails['payLoad'][0]['vehicleDetails']['vehicleRegistration'];
					$PLACE_OF_POSTING = $ervAPIdetails['payLoad'][0]['district'];						
					}
					*/
					
					//die('xxxxxx');
					?>
					<tr>
					<td><?=$i?></td>
					<td><?=$employee_code?></td>
					<td style='background-color:<?php echo $csstext;?>'><?=$l['rank1']?></td>
					<td><?=$name?></td>				
					<td style="padding: 0;"><div align="center"><a href="<?=$viewUrl?>" class="btn btn-success btn-sm btn-xs">View</a> <a href="<?=$editUrl?>" class="btn btn-info btn-sm btn-xs">Edit</a></div></td>
					</tr>	
				<?php $i++;	}
				}
				?>
			</tbody>
		
		</table>
	</div>
</div>


<script type="text/javascript">
	
	function dptChange(dptID)
  {
  	startLoader();

    if(dptID != -1)
    {
        $('#employee_data_disp').hide();
        

        var dept_id = dptID;
        var menuid = $('#menuid').val();


        // getdeptempdropdown
        var url = BASEURL+"admin/manageemployees/getemppless_by_department_cdac?securekey="+menuid;

        $.ajax({
            type: "POST",
            url: url,
            dataType: 'JSON',
            data:{ dept_id:dept_id },
            success: function(data)
            {
                // console.log(data.Status);

                if(data.Status == 'SS')
                {
                	$('#employee_data_disp').show();
                  $('#employee_data_disp').html(data.Res);
                }

                stopLoader();
            }
        });
    }
    else
    {
    		stopLoader();
        $('#employee_data_disp').show();
        /*$('#employee_data_disp').css('color','Red');
        $('#employee_data_disp').html('No data exist.');*/
    }
  }


  function startLoader()
  {
     $("#loading").show();
  }
   
  function stopLoader()
  {
      $("#loading").hide();
  }

</script>