<?php

/**
 * This is the model class for table "group_member".
 *
 * The followings are the available columns in table 'group_member':
 * @property integer $id
 * @property integer $contact_id
 * @property integer $member_id
 * @property integer $role
 * @property integer $created_time
 * @property integer $status
 * @property string $nick_name
 * @property integer $invite_member
 */
class GroupMember extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'group_member';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_time', 'required'),
			array('contact_id, member_id, role, created_time, status, invite_member, free_mode', 'numerical', 'integerOnly'=>true),
			array('nick_name', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, contact_id, member_id, role, created_time, status, nick_name, invite_member, free_mode', 'safe', 'on'=>'search'),
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
			'contact_id' => 'Contact',
			'member_id' => 'Member',
			'role' => 'Role',
			'created_time' => 'Created Time',
			'status' => 'Status',
			'nick_name' => 'Nick Name',
			'invite_member' => 'Invite Member',
			'free_mode' => 'Free Mode',
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
		$criteria->compare('contact_id',$this->contact_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('role',$this->role);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('status',$this->status);
		$criteria->compare('nick_name',$this->nick_name,true);
		$criteria->compare('invite_member',$this->invite_member);
		$criteria->compare('free_mode',$this->free_mode);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GroupMember the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
