<?php

/**
 * This is the model class for table "friend_disable".
 *
 * The followings are the available columns in table 'friend_disable':
 * @property integer $id
 * @property integer $circle_id
 * @property integer $status
 * @property integer $user_id
 * @property string $reason
 * @property integer $created_time
 */
class FriendDisable extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $uname;
	public $fname;
	public function tableName()
	{
		return 'friend_disable';
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
			array('circle_id, status, user_id, created_time', 'numerical', 'integerOnly'=>true),
			array('reason', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, circle_id, status, user_id, reason, created_time', 'safe', 'on'=>'search'),
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
			'id' => 'id',
			'circle_id' => '朋友圈ID',
			'status' => '0为启用、1为禁用',
			'user_id' => '操作人',
			'reason' => '操作原因',
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
		$criteria->compare('circle_id',$this->circle_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FriendDisable the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
