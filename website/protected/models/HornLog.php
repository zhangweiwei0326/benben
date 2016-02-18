<?php

/**
 * This is the model class for table "horn_log".
 *
 * The followings are the available columns in table 'horn_log':
 * @property integer $id
 * @property integer $store_id
 * @property integer $member_id
 * @property integer $buy_type
 * @property integer $horn_type
 * @property integer $action_type
 * @property integer $num
 * @property integer $add_date
 * @property integer $update_date
 */
class HornLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'horn_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, member_id, buy_type, horn_type, action_type, num, add_date, update_date', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, store_id, member_id, buy_type, horn_type, action_type, num, add_date, update_date', 'safe', 'on'=>'search'),
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
			'store_id' => 'Store',
			'member_id' => 'Member',
			'buy_type' => 'Buy Type',
			'horn_type' => 'Horn Type',
			'action_type' => 'Action Type',
			'num' => 'Num',
			'add_date' => 'Add Date',
			'update_date' => 'Update Date',
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
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('buy_type',$this->buy_type);
		$criteria->compare('horn_type',$this->horn_type);
		$criteria->compare('action_type',$this->action_type);
		$criteria->compare('num',$this->num);
		$criteria->compare('add_date',$this->add_date);
		$criteria->compare('update_date',$this->update_date);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HornLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
