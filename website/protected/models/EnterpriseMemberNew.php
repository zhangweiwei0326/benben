<?php

/**
 * This is the model class for table "enterprise_member".
 *
 * The followings are the available columns in table 'enterprise_member':
 * @property integer $id
 * @property integer $contact_id
 * @property integer $member_id
 * @property string $short_phone
 * @property string $remark_name
 * @property integer $created_time
 * @property string $phone
 * @property string $name
 */
class EnterpriseMemberNew extends EnterpriseMember
{
	
	public $access_level;
	public $benben_id;
	public $broadcast_per_month;	//每月小喇叭数量
	public $broadcast_available_month;		//每月小喇叭剩余数量
	public $is_manage;		//是否是管理员
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'enterprise_member';
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
			array('contact_id, member_id, created_time', 'numerical', 'integerOnly'=>true),
			array('short_phone, phone', 'length', 'max'=>20),
			array('remark_name', 'length', 'max'=>255),
			array('name', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, contact_id, member_id, short_phone, remark_name, created_time, phone, name', 'safe', 'on'=>'search'),
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
			'short_phone' => 'Short Phone',
			'remark_name' => 'Remark Name',
			'created_time' => 'Created Time',
			'phone' => 'Phone',
			'name' => 'Name',
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
		$criteria->compare('short_phone',$this->short_phone,true);
		$criteria->compare('remark_name',$this->remark_name,true);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EnterpriseMember the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
