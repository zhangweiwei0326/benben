<?php

/**
 * This is the model class for table "enterprise_member_manage".
 *
 * The followings are the available columns in table 'enterprise_member_manage':
 * @property integer $id
 * @property integer $member_id
 * @property integer $group_id
 * @property integer $access_level
 * @property integer $is_manage
 * @property integer $broadcast_per_month
 * @property integer $broadcast_available_month
 * @property string $manage_role
 * @property integer $created_time
 */
class EnterpriseMemberManage extends CActiveRecord
{
    public $manage_name;
    public $available;
    public $smember_id;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'enterprise_member_manage';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, group_id, access_level, is_manage, broadcast_per_month, broadcast_available_month, created_time', 'numerical', 'integerOnly'=>true),
			array('manage_role', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, group_id, access_level, is_manage, broadcast_per_month, broadcast_available_month, manage_role, created_time', 'safe', 'on'=>'search'),
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
			'member_id' => 'Member',
			'group_id' => 'Group',
			'access_level' => 'Access Level',
			'is_manage' => 'Is Manage',
			'broadcast_per_month' => 'Broadcast Per Month',
			'broadcast_available_month' => 'Broadcast Available Month',
			'manage_role' => 'Manage Role',
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
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('access_level',$this->access_level);
		$criteria->compare('is_manage',$this->is_manage);
		$criteria->compare('broadcast_per_month',$this->broadcast_per_month);
		$criteria->compare('broadcast_available_month',$this->broadcast_available_month);
		$criteria->compare('manage_role',$this->manage_role,true);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EnterpriseMemberManage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
