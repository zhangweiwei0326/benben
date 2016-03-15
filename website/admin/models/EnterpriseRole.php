<?php

/**
 * This is the model class for table "enterprise_role".
 *
 * The followings are the available columns in table 'enterprise_role':
 * @property integer $id
 * @property integer $enterprise_id
 * @property integer $enterprise_apply
 * @property integer $member_add
 * @property integer $access_level
 * @property integer $member_limit
 * @property integer $broadcast_num
 * @property integer $broadcast_available
 * @property integer $group_level
 * @property integer $manage_num
 * @property integer $created_time
 * @property integer $access_level_set
 */
class EnterpriseRole extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'enterprise_role';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('enterprise_id, enterprise_apply, member_add, access_level, member_limit, broadcast_num, broadcast_available, group_level, manage_num, created_time, access_level_set', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, enterprise_id, enterprise_apply, member_add, access_level, member_limit, broadcast_num, broadcast_available, group_level, manage_num, created_time, access_level_set', 'safe', 'on'=>'search'),
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
			'id' => '自增id',
			'enterprise_id' => '政企id',
			'enterprise_apply' => '手机端加入政企方式：1允许其他用户自由加入政企（系统默认1）2允许其他用户申请加入政企 3禁止其他用户申请加入政企',
			'member_add' => '手机端添加成员方式：1允许政企成员使用“添加成员”和“我添加的联系人”（系统默认1）2允许管理员使用“添加成员”和“我添加的联系人”',
			'access_level' => '查询权限等级设置：1~10级（默认1）',
			'member_limit' => '人数上限（企业/个人：500，学校：50000）',
			'broadcast_num' => '大喇叭数量 （以月为单位，企业/个人：10，学校：200）',
			'broadcast_available' => '大喇叭剩余数量',
			'group_level' => '分层权限:0.不可分,1~4.1~4层（企业/个人：0，学校：4）',
			'manage_num' => '管理员数量：0不可分 1~5，1～5个（企业/个人：1，学校：5）',
			'created_time' => '创建时间',
			'access_level_set' => '查阅权限：1可分 0不可分（企业/个人：0，学校：1）',
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
		$criteria->compare('enterprise_id',$this->enterprise_id);
		$criteria->compare('enterprise_apply',$this->enterprise_apply);
		$criteria->compare('member_add',$this->member_add);
		$criteria->compare('access_level',$this->access_level);
		$criteria->compare('member_limit',$this->member_limit);
		$criteria->compare('broadcast_num',$this->broadcast_num);
		$criteria->compare('broadcast_available',$this->broadcast_available);
		$criteria->compare('group_level',$this->group_level);
		$criteria->compare('manage_num',$this->manage_num);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('access_level_set',$this->access_level_set);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EnterpriseRole the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
