<?php

/**
 * This is the model class for table "quote".
 *
 * The followings are the available columns in table 'quote':
 * @property integer $id
 * @property integer $item_id
 * @property integer $store_id
 * @property string $price
 * @property string $description
 * @property integer $accept
 * @property integer $pay_id
 * @property float $shipping_fee
 * @property integer $created_time
 */
class Quote extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'quote';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('price, created_time', 'required'),
			array('item_id, pay_id, store_id, accept, created_time', 'numerical', 'integerOnly'=>true),
			array('shipping_fee', 'numerical'),
			array('price', 'length', 'max'=>20),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, item_id, store_id, pay_id, shipping_fee, price, description, accept, created_time', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'item_id' => 'Item',
			'store_id' => 'Store',
			'price' => 'Price',
			'description' => 'Description',
			'accept' => 'Accept',
			'pay_id' => 'Pay Id',
			'shipping_fee' => 'Shipping Fee',
			'created_time' => 'Created Time',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('item_id',$this->item_id);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('accept',$this->accept);
		$criteria->compare('pay_id',$this->pay_id);
		$criteria->compare('shipping_fee',$this->shipping_fee);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Quote the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
