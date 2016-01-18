<?php

/**
 * This is the model class for table "buy".
 *
 * The followings are the available columns in table 'buy':
 * @property integer $id
 * @property string $title
 * @property string $amount
 * @property string $description
 * @property integer $member_id
 * @property integer $status
 * @property integer $deadline
 * @property integer $quoted_number
 * @property integer $created_time
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property integer $is_close
 * @property integer $is_accept
 */
class Buy extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'buy';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, amount, created_time', 'required'),
			array('member_id, status, deadline, quoted_number, created_time, province, city, area, street, is_close, is_accept', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('amount', 'length', 'max'=>10),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, amount, description, member_id, status, deadline, quoted_number, created_time, province, city, area, street, is_close, is_accept', 'safe', 'on'=>'search'),
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
			'title' => 'Title',
			'amount' => 'Amount',
			'description' => 'Description',
			'member_id' => 'Member',
			'status' => 'Status',
			'deadline' => 'Deadline',
			'quoted_number' => 'Quoted Number',
			'created_time' => 'Created Time',
			'province' => 'Province',
			'city' => 'City',
			'area' => 'Area',
			'street' => 'Street',
			'is_close' => 'Is Close',
			'is_accept' => 'Is Accept',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('deadline',$this->deadline);
		$criteria->compare('quoted_number',$this->quoted_number);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('is_close',$this->is_close);
		$criteria->compare('is_accept',$this->is_accept);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Buy the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
