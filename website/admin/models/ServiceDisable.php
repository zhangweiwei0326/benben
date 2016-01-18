<?php

/**
 * This is the model class for table "service_disable".
 *
 * The followings are the available columns in table 'service_disable':
 * @property integer $id
 * @property integer $member_id
 * @property integer $status
 * @property integer $user_id
 * @property string $reason
 * @property integer $type
 * @property integer $created_time
 */
class ServiceDisable extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $uname;
	public $fname;
	public function tableName()
	{
		return 'service_disable';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, status, user_id, type, created_time', 'numerical', 'integerOnly'=>true),
			array('reason', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, status, user_id, reason, type, created_time', 'safe', 'on'=>'search'),
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
			'member_id' => '用户ID',
			'status' => '0为启用、1为禁用1周、2为禁用2周、3为禁用1个月、4为禁用3个月、5为无限期',
			'user_id' => '操作人',
			'reason' => '操作原因',
			'type' => '类型,  1微创作禁用、2我要买禁用、3政企禁用、4群组禁用、5号码直通车禁用、6好友联盟禁用',
			'created_time' => '操作时间',
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
		$criteria->compare('status',$this->status);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ServiceDisable the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
