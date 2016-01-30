<?php

/**
 * This is the model class for table "store_rights".
 *
 * The followings are the available columns in table 'store_rights':
 * @property integer $id
 * @property integer $store_id
 * @property integer $member_id
 * @property integer $overdue_date
 * @property integer $is_gove_computer_ad
 * @property integer $is_member_ad
 * @property integer $add_date
 * @property integer $update_date
 * @property integer $person_num
 * @property integer $service_type
 * @property integer $person_used_num
 * @property integer $small_horn_num
 */
class StoreRights extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'store_rights';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, member_id, overdue_date, is_gove_computer_ad, is_member_ad, add_date, update_date, person_num, service_type, person_used_num, small_horn_num', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, store_id, member_id, overdue_date, is_gove_computer_ad, is_member_ad, add_date, update_date, person_num, service_type, person_used_num, small_horn_num', 'safe', 'on'=>'search'),
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
			'store_id' => 'Store',
			'member_id' => 'Member',
			'overdue_date' => 'Overdue Date',
			'is_gove_computer_ad' => 'Is Gove Computer Ad',
			'is_member_ad' => 'Is Member Ad',
			'add_date' => 'Add Date',
			'update_date' => 'Update Date',
			'person_num' => 'Person Num',
			'service_type' => 'Service Type',
			'person_used_num' => 'Person Used Num',
			'small_horn_num' => 'Small Horn Num',
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
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('overdue_date',$this->overdue_date);
		$criteria->compare('is_gove_computer_ad',$this->is_gove_computer_ad);
		$criteria->compare('is_member_ad',$this->is_member_ad);
		$criteria->compare('add_date',$this->add_date);
		$criteria->compare('update_date',$this->update_date);
		$criteria->compare('person_num',$this->person_num);
		$criteria->compare('service_type',$this->service_type);
		$criteria->compare('person_used_num',$this->person_used_num);
		$criteria->compare('small_horn_num',$this->small_horn_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return StoreRights the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
