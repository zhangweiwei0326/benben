<?php

/**
 * This is the model class for table "store_order_info".
 *
 * The followings are the available columns in table 'store_order_info':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class StoreOrderInfo extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'store_order_info';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id,order_status,shipping_status,pay_status,country,province,city,area,street,shipping_id,pay_id,add_time,confirm_time,pay_time,shipping_time,extend_shipping_time,bonus_id,extension_code,extension_id,parent_id,store_comment_status', 'numerical', 'integerOnly'=>true),
            array('order_sn,consignee,address,zipcode,tel,mobile,email,best_time,postscript,shipping_name,pay_name,inv_payee,inv_content,invoice_no,to_buyer,pay_note,inv_type,qrcode,shipping_sn', 'length', 'max'=>255),
            array('goods_amount,shipping_fee,insure_fee,pay_fee,money_paid,bonus,order_amount,tax,discount','numerical'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('member_id,order_status,shipping_status,pay_status,country,province,city,area,street,shipping_id,shipping_sn,pay_id,add_time,confirm_time,pay_time,shipping_time,extend_shipping_time,qrcode,bonus_id,extension_code,extension_id,is_separate,parent_id,order_sn,consignee,address,zipcode,tel,mobile,email,best_time,postscript,shipping_name,pay_name,inv_payee,inv_content,invoice_no,to_buyer,pay_note,inv_type,goods_amount,shipping_fee,insure_fee,pay_fee,money_paid,bonus,order_amount,tax,discount,store_comment_status', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            "member_id"=>'Member Id',
            "order_status"=>"Order Status",
            "shipping_status"=>"Shipping Status",
            "pay_status"=>"Pay Status",
            "country"=>"Country",
            "province"=>"Province",
            "city"=>"City",
            "area"=>"Area",
            "street"=>"Street",
            "shipping_id"=>"Shipping Id",
            "shipping_sn"=>"Shipping Sn",
            "pay_id"=>"Pay Id",
            "add_time"=>"Add Time",
            "confirm_time"=>"Confirm Time",
            "pay_time"=>"Pay Time",
            "shipping_time"=>"Shipping Time",
            "extend_shipping_time"=>"Extend Shipping Time",
            "bonus_id"=>'Bonus Id',
            'extension_code'=>'Extension Code',
            'extension_id'=>'Extension Id',
            'parent_id'=>'Parent Id',
            'order_sn'=>'Order Sn',
            'consignee'=>'Consignee',
            'address'=>'Address',
            'zipcode'=>'Zipcode',
            'tel'=>'Tel',
            'mobile'=>'Mobile',
            'email'=>'Email',
            'best_time'=>'Best Time',
            'postscript'=>'Postscripy',
            'shipping_name'=>'Shipping Name',
            'pay_name'=>'Pay Name',
            'inv_payee'=>'Inv Payee',
            'inv_content'=>'Inv Content',
            'invoice_no'=>'Invoice No',
            'to_buyer'=>'To Buyer',
            'pay_note'=>'Pay Note',
            'inv_type'=>'Inv Type',
            'goods_amount'=>'Goods Amount',
            'shipping_fee'=>'Shipping Fee',
            'insure_fee'=>'Insure Fee',
            'pay_fee'=>'Pay Fee',
            'money_paid'=>'Money Paid',
            'bonus'=>'Bonus',
            'order_amount'=>'Order Amount',
            'tax'=>'Tax',
            'discount'=>'Discount',
            'qrcode'=>'Qrcode',
            'store_comment_status'=>'Store Comment Status',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('order_status',$this->order_status,true);
        $criteria->compare('shipping_status',$this->shipping_status);
        $criteria->compare('pay_status',$this->pay_status);
        $criteria->compare('country',$this->country);
        $criteria->compare('province',$this->province);
        $criteria->compare('city',$this->city);
        $criteria->compare('area',$this->area);
        $criteria->compare('street',$this->street);
        $criteria->compare('shipping_id',$this->shipping_id);
        $criteria->compare('shipping_sn',$this->shipping_sn);
        $criteria->compare('pay_id',$this->pay_id);
        $criteria->compare('add_time',$this->add_time);
        $criteria->compare('confirm_time',$this->confirm_time);
        $criteria->compare('pay_time',$this->pay_time);
        $criteria->compare('shipping_time',$this->shipping_time);
        $criteria->compare('extend_shipping_time',$this->extend_shipping_time);
        $criteria->compare('bonus_id',$this->bonus_id);
        $criteria->compare('extension_code',$this->extension_code);
        $criteria->compare('extension_id',$this->extension_id);
        $criteria->compare('parent_id',$this->parent_id);
        $criteria->compare('order_sn',$this->order_sn);
        $criteria->compare('consignee',$this->consignee);
        $criteria->compare('address',$this->address);
        $criteria->compare('zipcode',$this->zipcode);
        $criteria->compare('tel',$this->tel);
        $criteria->compare('mobile',$this->mobile);
        $criteria->compare('email',$this->email);
        $criteria->compare('best_time',$this->best_time);
        $criteria->compare('sign_building',$this->sign_building);
        $criteria->compare('postscript',$this->postscript);
        $criteria->compare('shipping_name',$this->shipping_name);
        $criteria->compare('pay_name',$this->pay_name);
        $criteria->compare('inv_payee',$this->inv_payee);
        $criteria->compare('inv_content',$this->inv_content);
        $criteria->compare('invoice_no',$this->invoice_no);
        $criteria->compare('to_buyer',$this->to_buyer);
        $criteria->compare('pay_note',$this->pay_note);
        $criteria->compare('inv_type',$this->inv_type);
        $criteria->compare('goods_amount',$this->goods_amount);
        $criteria->compare('shipping_fee',$this->shipping_fee);
        $criteria->compare('insure_fee',$this->insure_fee);
        $criteria->compare('pay_fee',$this->pay_fee);
        $criteria->compare('money_paid',$this->money_paid);
        $criteria->compare('order_amount',$this->order_amount);
        $criteria->compare('bonus',$this->bonus);
        $criteria->compare('tax',$this->tax);
        $criteria->compare('discount',$this->discount);
        $criteria->compare('qrcode',$this->qrcode);
        $criteria->compare('store_comment_status',$this->store_comment_status);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Splash the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
