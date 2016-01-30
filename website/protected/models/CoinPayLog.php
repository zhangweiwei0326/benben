<?php

/**
 * This is the model class for table "coin_pay_log".
 *
 * The followings are the available columns in table 'coin_pay_log':
 * @property integer $id
 * @property integer $order_id
 * @property string $pay_time
 * @property string $total_money
 * @property string $money_paid
 * @property integer $use_coin
 */
class CoinPayLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'coin_pay_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, use_coin', 'numerical', 'integerOnly'=>true),
			array('pay_time', 'length', 'max'=>100),
			array('total_money, money_paid', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, pay_time, total_money, money_paid, use_coin', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'pay_time' => 'Pay Time',
			'total_money' => 'Total Money',
			'money_paid' => 'Money Paid',
			'use_coin' => 'Use Coin',
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
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('pay_time',$this->pay_time,true);
		$criteria->compare('total_money',$this->total_money,true);
		$criteria->compare('money_paid',$this->money_paid,true);
		$criteria->compare('use_coin',$this->use_coin);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CoinPayLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
