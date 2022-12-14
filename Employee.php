<?php
namespace app\models;
use Yii;
class Employee extends \yii\db\ActiveRecord{
    
    public $employee_id,$fname; public $lname; public $name_hindi; public $gender;public $employment_type;public $dob;public $contact;public $address;public $city;public $state;public $zip;public $p_address;public $p_city;public $p_state;public $p_zip;public $joining_date;public $marital_status;public $blood_group;public $is_active;public $emergency_contact;public $created_date;public $contact1;public $contact2;public $personal_email;public $designation;public $bank_ac;public $bank_name;public $bank_ifsc;public $emp_image;public $emp_signature;public $dept_id, $dept_name, $desg_id, $desg_name, $authority1, $authority2, $effected_from, $grade_pay_scale,$emplevel,$basic_cons_pay, $pan_number,$religion,$caste,$passport_detail,$category_id,$substantive_rnk,$erss_job_profile,$erv_deployed,$offclocation,$location,$office_ord_no,$posting_remarks,$probation_to,$probation_from,$confirmation_date;
    public $rank;
    public $unit;
     
    //public static function tableName(){ return 'employee'; }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_id', 'fname', 'gender', 'dob', 'phone', 'emergency_phone','contact','joining_date','is_active','desg_id', 'created_date','effected_from','category_id','erss_job_profile','erv_deployed','offclocation'], 'required'],
            [['dob', 'joining_date', 'created_date','probation_from','probation_to','confirmation_date'], 'safe'],
            [[ 'auth1','auth2', 'grade_pay_scale' ], 'integer'],
            [['username', 'employment_type'], 'string', 'max' => 50],
            [['fname', 'lname','belt_no'], 'string', 'max' => 90],
            [['phone', 'emergency_phone'], 'string', 'max' => 20],
            [['pan_number'], 'string', 'max' => 10],
            [['employement_status'],  'string', 'max' => 90],
            [['emplevel','personal_email','contact2','emp_image', 'emp_signature','marital_status','substantive_rnk','rank1','blood_group','gender', 'is_active','address', 'city', 'state', 'zip', 'contact', 'p_address', 'p_city', 'p_state', 'p_zip', 'p_contact','citizenship','office_ord_no','remarks'], 'string', 'max' => 255],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'basic_cons_pay' => 'Basic Pay',
            'emplevel' => 'Level',
            'grade_pay_scale' => 'Grade Pay Scale',
            'effected_from' => 'Effected From',
            'dept_name' => 'Department',
            'e_id' => 'ID',
            'employee_id' => 'Employee ID *',
            'name_hindi' => 'Name Hindi',
            'username' => 'Username',
            'bank_ac' => 'Account No.',
            'dept_id' => 'Department',
            'desg_name' => 'Designation',
            'fname' => 'First Name *',
            'lname' => 'Last Name',
            'gender' => 'Gender *',
            'dob' => 'Dob *',
            'phone' => 'Phone *',
            'emergency_phone' => 'Emergency Phone',
            'address' => 'Address',
            'city' => 'City ',
            'state' => 'State ',
            'zip' => 'Zip ',
            'contact1' => 'Landline / Mobile',
            'contact2' => 'Landline / Mobile',
            'contact' => 'Contact *',
            'p_address' => 'Address ',
            'p_city' => 'City ',
            'p_state' => 'State ',
            'p_zip' => 'Zip ',
            'p_contact' => 'Contact *', 
            'joining_date' => 'Joining Date *',
            //'designation_id' => 'Designation',
            'employment_type' => 'Employment Type',
            'belt_no' => 'Belt No',
            'rank1' => 'Rank',
            'location' => 'Place Of Posting',
            'unit' => 'Employee Unit',
            'substantive_rnk' => 'Rank/Name/Bno/JP',
            'marital_status' => 'Marital Status',
            'blood_group' => 'Blood Group',
            'is_active' => 'Is Active',
            'emp_image' => 'Emp Image',
            'emp_signature' => 'Emp Signature',
            'created_date' => 'Created Date',
            'personal_email' => 'Personal Email ID',
            'authority1'=>'Reporting Authority',
            'authority2'=>'Reviewing Authority',
            'desg_id'=>'Select Designation*',
            'religion'=>'Religion',
            'caste'=>'Caste',
            'passport_detail'=>'Passport Detail',
            'category_id'=>'Category',
            'erss_job_profile'=>'ERSS Job Profile',
            'erv_deployed'=>'Is ERV Deployed',
            'offclocation'=>'Office Location',// updated by Harsh
            'citizenship'=>'Citizenship',
            'office_ord_no'=>'Office Order No', 
            'posting_remarks'=>'Remarks (If Any)',
            'probation_from' =>'Probation Date From',
            'probation_to' =>'Probation Date To',
            'confirmation_date' =>'Confirmation Date of post'

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDesignation()
    {
        return $this->hasOne(Designation::className(), ['desg_id' => 'designation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeLeaveDetails()
    {
        return $this->hasOne(EmployeeLeaveDetails::className(), ['e_id' => 'e_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeQualifications()
    {
        return $this->hasMany(EmployeeQualification::className(), ['e_id' => 'e_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacEmployees()
    {
        return $this->hasMany(RbacEmployee::className(), ['e_id' => 'e_id']);
    }
}
