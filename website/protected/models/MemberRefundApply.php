<?php

/**
 * This is the model class for table "member_refund_apply".
 *
 * The followings are the available columns in table 'member_refund_apply':
 * @property integer $id
 * @property integer $member_id
 * @property string $fee
 * @property integer $is_delete
 * @property integer $refund_type
 * @property integer $add_date
 * @property integer $update_date
 * @property integer $handle
 */
class MemberRefundApply extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'member_refund_apply';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, is_delete, refund_type, add_date, update_date, handle', 'numerical', 'integerOnly'=>true),
			array('fee', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, fee, is_delete, refund_type, add_date, update_date, handle', 'safe', 'on'=>'search'),
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
			'fee' => 'Fee',
			'is_delete' => 'Is Delete',
			'refund_type' => 'Refund Type',
			'add_date' => 'Add Date',
			'update_date' => 'Update Date',
			'handle' => 'Handle',
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
		$criteria->compare('fee',$this->fee,true);
		$criteria->compare('is_delete',$this->is_delete);
		$criteria->compare('refund_type',$this->refund_type);
		$criteria->compare('add_date',$this->add_date);
		$criteria->compare('update_date',$this->update_date);
		$criteria->compare('handle',$this->handle);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MemberRefundApply the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
