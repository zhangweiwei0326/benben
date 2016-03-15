<?php

/**
 * This is the model class for table "apply_fix_enterprise".
 *
 * The followings are the available columns in table 'apply_fix_enterprise':
 * @property integer $id
 * @property integer $apply_id
 * @property string $enterprise_name
 * @property string $identity_num
 * @property string $identity_attachment
 * @property integer $apply_type
 * @property integer $apply_status
 * @property integer $created_time
 * @property integer $review_time
 */
class ApplyFixEnterprise extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'apply_fix_enterprise';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('apply_id, apply_type, apply_status, created_time, review_time', 'numerical', 'integerOnly'=>true),
			array('enterprise_name', 'length', 'max'=>45),
			array('identity_num, identity_attachment', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, apply_id, enterprise_name, identity_num, identity_attachment, apply_type, apply_status, created_time, review_time', 'safe', 'on'=>'search'),
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
			'apply_id' => 'Apply',
			'enterprise_name' => 'Enterprise Name',
			'identity_num' => 'Identity Num',
			'identity_attachment' => 'Identity Attachment',
			'apply_type' => 'Apply Type',
			'apply_status' => 'Apply Status',
			'created_time' => 'Created Time',
			'review_time' => 'Review Time',
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
		$criteria->compare('enterprise_name',$this->enterprise_name,true);
		$criteria->compare('identity_num',$this->identity_num,true);
		$criteria->compare('identity_attachment',$this->identity_attachment,true);
		$criteria->compare('apply_type',$this->apply_type);
		$criteria->compare('apply_status',$this->apply_status);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('review_time',$this->review_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ApplyFixEnterprise the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
