<?php

/**
 * This is the model class for table "promotion_manage_attach".
 *
 * The followings are the available columns in table 'promotion_manage_attach':
 * @property integer $id
 * @property integer $manage_id
 * @property integer $store_id
 * @property integer $member_id
 * @property integer $pro_num
 * @property integer $small_horn_num
 * @property integer $big_horn_num
 * @property integer $is_activity
 * @property integer $is_pro
 * @property integer $is_computer
 * @property integer $is_group_buying
 * @property integer $is_computers
 * @property integer $store_num
 * @property integer $is_member_ico
 * @property integer $sale_consultant_num
 * @property integer $person_num
 * @property integer $overdue_date
 * @property integer $add_date
 * @property integer $update_date
 * @property integer $service_type
 * @property integer $group_num
 * @property integer $store_used_num
 * @property integer $person_used_num
 */
class PromotionManageAttach extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'promotion_manage_attach';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('manage_id, store_id, member_id, pro_num, small_horn_num, big_horn_num, is_activity, is_pro, is_computer, is_group_buying, is_computers, store_num, is_member_ico, sale_consultant_num, person_num, overdue_date, add_date, update_date, service_type, group_num, store_used_num, person_used_num', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, manage_id, store_id, member_id, pro_num, small_horn_num, big_horn_num, is_activity, is_pro, is_computer, is_group_buying, is_computers, store_num, is_member_ico, sale_consultant_num, person_num, overdue_date, add_date, update_date, service_type, group_num, store_used_num, person_used_num', 'safe', 'on'=>'search'),
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
			'manage_id' => 'Manage',
			'store_id' => 'Store',
			'member_id' => 'Member',
			'pro_num' => 'Pro Num',
			'small_horn_num' => 'Small Horn Num',
			'big_horn_num' => 'Big Horn Num',
			'is_activity' => 'Is Activity',
			'is_pro' => 'Is Pro',
			'is_computer' => 'Is Computer',
			'is_group_buying' => 'Is Group Buying',
			'is_computers' => 'Is Computers',
			'store_num' => 'Store Num',
			'is_member_ico' => 'Is Member Ico',
			'sale_consultant_num' => 'Sale Consultant Num',
			'person_num' => 'Person Num',
			'overdue_date' => 'Overdue Date',
			'add_date' => 'Add Date',
			'update_date' => 'Update Date',
			'service_type' => 'Service Type',
			'group_num' => 'Group Num',
			'store_used_num' => 'Store Used Num',
			'person_used_num' => 'Person Used Num',
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
		$criteria->compare('manage_id',$this->manage_id);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('pro_num',$this->pro_num);
		$criteria->compare('small_horn_num',$this->small_horn_num);
		$criteria->compare('big_horn_num',$this->big_horn_num);
		$criteria->compare('is_activity',$this->is_activity);
		$criteria->compare('is_pro',$this->is_pro);
		$criteria->compare('is_computer',$this->is_computer);
		$criteria->compare('is_group_buying',$this->is_group_buying);
		$criteria->compare('is_computers',$this->is_computers);
		$criteria->compare('store_num',$this->store_num);
		$criteria->compare('is_member_ico',$this->is_member_ico);
		$criteria->compare('sale_consultant_num',$this->sale_consultant_num);
		$criteria->compare('person_num',$this->person_num);
		$criteria->compare('overdue_date',$this->overdue_date);
		$criteria->compare('add_date',$this->add_date);
		$criteria->compare('update_date',$this->update_date);
		$criteria->compare('service_type',$this->service_type);
		$criteria->compare('group_num',$this->group_num);
		$criteria->compare('store_used_num',$this->store_used_num);
		$criteria->compare('person_used_num',$this->person_used_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PromotionManageAttach the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
