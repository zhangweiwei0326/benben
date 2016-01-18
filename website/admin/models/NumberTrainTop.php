<?php

/**
 * This is the model class for table "number_train_top".
 *
 * The followings are the available columns in table 'number_train_top':
 * @property integer $id
 * @property integer $train_id
 * @property integer $user_id
 * @property integer $created_time
 * @property integer $istop
 */
class NumberTrainTop extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $number;
	public function tableName()
	{
		return 'number_train_top';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('train_id, user_id, created_time, istop', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, train_id, user_id, created_time, istop', 'safe', 'on'=>'search'),
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
			'train_id' => 'Train',
			'user_id' => 'User',
			'created_time' => 'Created Time',
			'istop' => 'Istop',
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
		$criteria->compare('train_id',$this->train_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('istop',$this->istop);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NumberTrainTop the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
