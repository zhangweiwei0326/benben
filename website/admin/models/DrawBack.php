<?php 
class DrawBack extends CActiveRecord{
	public $order_id;//订单号
    public $status;//状态
    public $apply_reason;//申请原因
    public $refuse_reason;//拒绝原因
    public $name;//申请人
    public $shop_name;//商家ID
    public $apply_time;//申请时间
    public $deal_time;//处理时间
    public $phone;//手机
    public $account;//支付宝

    //表名
    public function tableName()
    {
        return 'back_order';
    }
    //验证规则
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        	array('order_id,status,apply_reason,refuse_reason,name,shop_name,apply_time,deal_time,phone,account', 'safe', 'on'=>'search'),
        );
    }
    //未知
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }
    //绑定属性值
    public function attributeLabels()
    {
        return array(
        	'order_id'=>'订单号',
        	'status'=>'状态',
        	'apply_reason'=>'申请原因',
        	'refuse_reason'=>'拒绝原因',
        	'name'=>'申请人',
        	'shop_name'=>'商家',
        	'apply_time'=>'申请时间',
        	'deal_time'=>'处理时间',
        	'phone'=>'手机',
        	'account'=>'支付宝',
        );
    }
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria=new CDbCriteria;
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('status',$this->status);
        $criteria->compare('apply_reason',$this->apply_reason);
        $criteria->compare('refuse_reason',$this->refuse_reason);
        $criteria->compare('name',$this->apply_id);
        $criteria->compare('shop_name',$this->train_id);
        $criteria->compare('apply_time',$this->apply_time);
        $criteria->compare('deal_time',$this->deal_time);
        $criteria->compare('phone',$this->phone);
        $criteria->compare('account',$this->account);
        
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
           
        ));
    }
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}