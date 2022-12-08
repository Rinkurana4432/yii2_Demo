<?php

namespace app\models;
use Yii;
class Item extends \yii\db\ActiveRecord
{
    public $dept_id;
    public $dept_name;
    public $group;
    public $category;
    public $cost_centre;
    public $e_id;
    public $item;
    public $item_type;
    public $qty_required;
    public $units;
    public $remarks;
    public $purpose;
    public $itm_id;
    public $CLASSIFICATION_CODE;
    public $ITEM_CAT_CODE;
    public $ITEM_CODE;
    public $item_name;
    public $Item_type1;
    public $Quantity;
    public $Measuring_Unit;
    public $item_store_id;
    public $rack_id;
    public $is_item_defactive;    
	
    public static function tableName()
    {
        return 'store_item_master';
    }

    public function rules()
    {
        return [
            [['dept_id','dept_name','group','category','e_id','item','qty_required','units','remarks','purpose','item_name'], 'required'],
	        [['qty_required'], 'integer'],
            [['dept_name','is_item_defactive'], 'string', 'max' => 50],
            [['remarks','item_store_id','rack_id'], 'string', 'max' => 255],
            [['dept_id','item_safe_count'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dept_id' => 'Department',
            'dept_name' => 'Department',
            'e_id' => 'Employee',
            'remarks' => 'Remarks',
            'remarks' => 'Remarks',
            'is_item_defactive'=>'Is Item Defative',
            'item_store_id'=>'Store Location',
            'rack_id'=>'Rack Location'
           
            //'is_active' => 'Is Active', 
        ];
    }
}
