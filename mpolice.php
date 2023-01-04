<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

<script type="text/javascript" language="javascript" src="<?=Yii::$app->homeUrl?>/js/forexcel/jszip.js"></script>
<script type="text/javascript" language="javascript" src="<?=Yii::$app->homeUrl?>/js/forexcel/buttons.js"></script>
<?php
	$this->title= 'Manage Employees ';
	$lists = Yii::$app->utility->get_employees(null);
	$menuid = "";
	if(isset($_GET['securekey']) AND !empty($_GET['securekey'])){
	    $menuid = Yii::$app->utility->decryptString($_GET['securekey']);
	    if(empty($menuid)){
	        header('Location: '.Yii::$app->homeUrl); 
	        exit;
	    }
	    $menuid = Yii::$app->utility->encryptString($menuid);
	}

	$depts = Yii::$app->utility->get_hp_dept();
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
		<a style="margin-bottom:10px;" href="<?=Yii::$app->homeUrl?>admin/manageemployees/add?securekey=<?=$menuid?>" class="btn btn-success btn-sm mybtn">Add New Employee</a>	
		<br/>
		<a style="margin-bottom:10px;" href="<?=Yii::$app->homeUrl?>admin/manageemployees/printlarge?securekey=<?=$menuid?>" class="btn btn-success btn-sm btn-xs" style="height:80px;" target="_blank">Print Large View</a>	
	</div>

</div>
<div class="row">
	<div id="subjectTable_wrapper" >
         <div class="col-sm-12" style="margin-bottom: 10px;">
    </div>
    </div>
	<div class="col-sm-12" id="employee_data_disp">

		 <div class="dataTables_wrapper no-footer table-scroll trngdata" id="subjectTable_wrapper1">
		 	 <div class="col-md-6">
      </div>  
      <small style="float:right;color:#ab0501;"> * Select Department Before Search </small>
		<table id="dataTableShowdata" class="display" cellspacing="0" style="width:100%">
			<thead>
				<tr>
					<th>Sr.</th>
                    <th>Employee Code</th>
                    <th width='5%'>joining date</th>
                    <th width='5%'>Rank</th>
					<th width='10%'>Name</th>
					<th width='5%'>No</th>
            
                    <th>Present Posting</th>
                    <th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if(!empty($lists)){
					$i =1;
					foreach($lists as $l){ 
					$name = ucwords($l['fname'])." ".ucwords($l['lname']);
					$employee_code = $l['employee_code'];
					$desg_name = $l['desg_name'];
					$adress = $l['address'].", ".$l['city']." -".$l['zip']." (".$l['state'].")";
					//$dob = date('d-m-Y', strtotime($l['dob']));
					$empltype="-";
					$joining_date=$l['joining_date'];
				
					if($i%2==0){
							$csstext = '';
					}else{
							$csstext = '';
					}
					$encry = Yii::$app->utility->encryptString($l['employee_code']);
					$viewUrl = Yii::$app->homeUrl."admin/manageemployees/viewemployee?securekey=$menuid&empid=$encry";
					$editUrl = Yii::$app->homeUrl."admin/manageemployees/updateemployee?securekey=$menuid&empid=$encry";
					// ERV API DETAILS
					$method = 'GET';

				
					$ERV_NO = '';
					$PLACE_OF_POSTING = '';
					
					?>
					<tr>
					<td><?=$i?></td>
                    <td><?=$employee_code?></td>
                    <td><?=$joining_date?></td>
                    <td><?=$l['rank1']?></td>
                    <td><?=$name?></td>
                    <td><?=$l['belt_no']?></td>
      
                    <td><?=$l['location']?></td>					
					<td style="padding: 0;"><div align="center" class="">
						<a href="<?=$viewUrl?>" class="btn btn-success btn-sm btn-xs">View</a> <a href="<?=$editUrl?>" class="btn btn-info btn-sm btn-xs">Edit</a>
					</div></td>
					</tr>	
				<?php $i++;	}
				}
				?>
			</tbody>
			<!--tfoot>
				<th>Sr.</th>
					<th>Name</th>
					<th>Designation</th>
					<th>Address</th>
					<th>Date of Birth</th>
					<th>Department</th>
					<th>Emp. Type</th>
					<th>Phone</th>
					<th>Action</th>
			</tfoot-->
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
        var url = BASEURL+"admin/manageemployees/getemppless_by_department?securekey="+menuid;

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
                  $('.enable_disableCls').removeAttr("disabled");
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
<script>
   $(document).ready(function() {
        var table = $('#dataTableShowdata').DataTable( {
            lengthChange: true,
            buttons: [ 'copy', 'excel', 'print' ]
        } );

        table.buttons().container()
            .appendTo( '#subjectTable_wrapper1 .col-md-6:eq(0)' );

            $('.dataTables_filter input').attr("placeholder","Select Department Before Serach");
            $('.dataTables_filter input').addClass("enable_disableCls");
            $('.dataTables_filter input').attr("disabled","disabled");



    } );






</script>