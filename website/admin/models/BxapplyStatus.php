<?php

/**
 * This is the model class for table "bxapply_status".
 *
 * The followings are the available columns in table 'bxapply_status':
 * @property integer $id
 * @property integer $apply_id
 * @property string $phone
 * @property integer $bx_status
 * @property string $reason
 * @property integer $created_time
 * @property integer $user_id
 * @property integer $status
 */
class BxapplyStatus extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $rname;
	public $name;
	public function tableName()
	{
		return 'bxapply_status';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('apply_id, bx_status, created_time, user_id, status', 'numerical', 'integerOnly'=>true),
			array('phone', 'length', 'max'=>45),
			array('reason', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, apply_id, phone, bx_status, reason, created_time, user_id, status', 'safe', 'on'=>'search'),
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
			'apply_id' => '百姓网ID',
			'phone' => '手机号码',
			'bx_status' => '审核状态',
			'reason' => '反馈信息',
			'created_time' => 'Created Time',
			'user_id' => '导入人',
			'status' => '导入状态',
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
		$criteria->compare('apply_id',$this->apply_id);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('bx_status',$this->bx_status);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BxapplyStatus the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
