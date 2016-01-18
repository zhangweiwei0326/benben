<?php

/**
 * This is the model class for table "store_order_info".
 *
 * The followings are the available columns in table 'store_order_info':
 * @property string $order_id
 * @property string $order_sn
 * @property string $member_id
 * @property integer $order_status
 * @property integer $shipping_status
 * @property integer $pay_status
 * @property string $consignee
 * @property integer $country
 * @property integer $province
 * @property string $city
 * @property string $area
 * @property string $street
 * @property string $address
 * @property string $zipcode
 * @property string $tel
 * @property string $mobile
 * @property string $email
 * @property string $best_time
 * @property string $postscript
 * @property integer $shipping_id
 * @property string $shipping_sn
 * @property string $shipping_name
 * @property integer $pay_id
 * @property string $pay_name
 * @property string $inv_payee
 * @property string $inv_content
 * @property string $goods_amount
 * @property string $shipping_fee
 * @property string $insure_fee
 * @property string $pay_fee
 * @property string $money_paid
 * @property string $bonus
 * @property string $order_amount
 * @property string $add_time
 * @property string $confirm_time
 * @property string $pay_time
 * @property string $shipping_time
 * @property integer $extend_shipping_time
 * @property integer $bonus_id
 * @property string $invoice_no
 * @property integer $extension_code
 * @property integer $extension_id
 * @property string $to_buyer
 * @property string $pay_note
 * @property string $inv_type
 * @property string $tax
 * @property integer $parent_id
 * @property string $discount
 * @property string $qrcode
 * @property integer $store_comment_status
 */
class StoreOrderInfo extends CActiveRecord
{
	public $goods_number;
	public $type;
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
			array('order_status, shipping_status, pay_status, country, province, shipping_id, pay_id, extend_shipping_time, bonus_id, extension_code, extension_id, parent_id, store_comment_status', 'numerical', 'integerOnly'=>true),
			array('order_sn, address, postscript, invoice_no, to_buyer, pay_note, qrcode', 'length', 'max'=>255),
			array('member_id, city, area, street', 'length', 'max'=>11),
			array('consignee, zipcode, tel, mobile, email, inv_type', 'length', 'max'=>60),
			array('best_time, shipping_name, pay_name, inv_payee, inv_content', 'length', 'max'=>120),
			array('shipping_sn', 'length', 'max'=>100),
			array('goods_amount, shipping_fee, insure_fee, pay_fee, money_paid, bonus, order_amount, add_time, confirm_time, pay_time, shipping_time, tax, discount', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_id, order_sn, member_id, order_status, shipping_status, pay_status, consignee, country, province, city, area, street, address, zipcode, tel, mobile, email, best_time, postscript, shipping_id, shipping_sn, shipping_name, pay_id, pay_name, inv_payee, inv_content, goods_amount, shipping_fee, insure_fee, pay_fee, money_paid, bonus, order_amount, add_time, confirm_time, pay_time, shipping_time, extend_shipping_time, bonus_id, invoice_no, extension_code, extension_id, to_buyer, pay_note, inv_type, tax, parent_id, discount, qrcode, store_comment_status', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'order_sn' => '订单号,唯一',
			'member_id' => '用户id',
			'order_status' => '订单的状态;0未确认,1确认,2已取消,3无效,4退货,5.待评价,6.已评价',
			'shipping_status' => '商品配送情况;0未发货,1已发货,2已收货,4退货',
			'pay_status' => '支付状态;0未付款;1付款中;2已付款',
			'consignee' => '收货人的姓名,用户页面填写,默认取值表user_address',
			'country' => '收货人的国家,用户页面填写,默认取值于表user_address,其id对应的值在region',
			'province' => '收货人的省份,用户页面填写,默认取值于表user_address, 其id对应的值在region',
			'city' => '收货人的城市,用户页面填写,默认取值于表user_address,其id对应的值在region',
			'area' => '收货人的地区,用户页面填写,默认取值于表user_address,其id对应的值在region',
			'street' => '收货人的街道,用户页面填写,默认取值于表user_address,其id对应的值在region',
			'address' => '收货人的详细地址,用户页面填写,默认取值于表user_address',
			'zipcode' => '收货人的邮编,用户页面填写,默认取值于表user_address',
			'tel' => '收货人的电话,用户页面填写,默认取值于表user_address',
			'mobile' => '收货人的手机,用户页面填写,默认取值于表user_address',
			'email' => '收货人的Email, 用户页面填写,默认取值于表user_address',
			'best_time' => '收货人的最佳送货时间,用户页面填写,默认取值于表user_addr ',
			'postscript' => '订单附言,由用户提交订单前填写',
			'shipping_id' => '用户选择的配送方式id,取值表shipping',
			'shipping_sn' => '运单号',
			'shipping_name' => '用户选择的配送方式的名称,取值表shipping',
			'pay_id' => '用户选择的支付方式的id,取值表payment',
			'pay_name' => '用户选择的支付方式名称,取值表payment',
			'inv_payee' => '发票抬头,用户页面填写',
			'inv_content' => '发票内容,用户页面选择',
			'goods_amount' => '商品的总金额',
			'shipping_fee' => '配送费用',
			'insure_fee' => 'Insure Fee',
			'pay_fee' => '支付费用,跟支付方式的配置相关,取值表payment',
			'money_paid' => '已付款金额',
			'bonus' => 'Bonus',
			'order_amount' => '应付款金额',
			'add_time' => '订单生成时间',
			'confirm_time' => '订单确认时间',
			'pay_time' => '订单支付时间',
			'shipping_time' => '订单配送时间',
			'extend_shipping_time' => '延长收货时间，默认为7天，延长1次就是3天',
			'bonus_id' => 'Bonus',
			'invoice_no' => 'Invoice No',
			'extension_code' => '活动类型，0促销，1团购，2.我要买，4.会员充值',
			'extension_id' => '通过活动购买的物品id,取值good_activity;如果是正常普通商品,该处为0',
			'to_buyer' => '商家给客户的留言,当该字段值时可以在订单查询看到',
			'pay_note' => '付款备注, 在订单管理编辑修改',
			'inv_type' => '发票类型,用户页面选择',
			'tax' => '发票税额',
			'parent_id' => '折扣金额',
			'discount' => 'Discount',
			'qrcode' => '二维码图片地址',
			'store_comment_status' => '商家评价状态，0.未评价，1已评价',
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

		$criteria->compare('order_id',$this->order_id,true);
		$criteria->compare('order_sn',$this->order_sn,true);
		$criteria->compare('member_id',$this->member_id,true);
		$criteria->compare('order_status',$this->order_status);
		$criteria->compare('shipping_status',$this->shipping_status);
		$criteria->compare('pay_status',$this->pay_status);
		$criteria->compare('consignee',$this->consignee,true);
		$criteria->compare('country',$this->country);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('street',$this->street,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('zipcode',$this->zipcode,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('best_time',$this->best_time,true);
		$criteria->compare('postscript',$this->postscript,true);
		$criteria->compare('shipping_id',$this->shipping_id);
		$criteria->compare('shipping_sn',$this->shipping_sn,true);
		$criteria->compare('shipping_name',$this->shipping_name,true);
		$criteria->compare('pay_id',$this->pay_id);
		$criteria->compare('pay_name',$this->pay_name,true);
		$criteria->compare('inv_payee',$this->inv_payee,true);
		$criteria->compare('inv_content',$this->inv_content,true);
		$criteria->compare('goods_amount',$this->goods_amount,true);
		$criteria->compare('shipping_fee',$this->shipping_fee,true);
		$criteria->compare('insure_fee',$this->insure_fee,true);
		$criteria->compare('pay_fee',$this->pay_fee,true);
		$criteria->compare('money_paid',$this->money_paid,true);
		$criteria->compare('bonus',$this->bonus,true);
		$criteria->compare('order_amount',$this->order_amount,true);
		$criteria->compare('add_time',$this->add_time,true);
		$criteria->compare('confirm_time',$this->confirm_time,true);
		$criteria->compare('pay_time',$this->pay_time,true);
		$criteria->compare('shipping_time',$this->shipping_time,true);
		$criteria->compare('extend_shipping_time',$this->extend_shipping_time);
		$criteria->compare('bonus_id',$this->bonus_id);
		$criteria->compare('invoice_no',$this->invoice_no,true);
		$criteria->compare('extension_code',$this->extension_code);
		$criteria->compare('extension_id',$this->extension_id);
		$criteria->compare('to_buyer',$this->to_buyer,true);
		$criteria->compare('pay_note',$this->pay_note,true);
		$criteria->compare('inv_type',$this->inv_type,true);
		$criteria->compare('tax',$this->tax,true);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('qrcode',$this->qrcode,true);
		$criteria->compare('store_comment_status',$this->store_comment_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return StoreOrderInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
