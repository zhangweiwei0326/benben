<?php

/**
 * This is the model class for table "complain".
 *
 * The followings are the available columns in table 'complain':
 * @property integer $id
 * @property integer $member_id
 * @property string $info
 * @property integer $created_time
 * @property integer $apply_id
 */
class Complain extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $benben_id;
	public $sname;
	public $phone;
	public $bphone;
	public $bname;
	public $sex;
	public $area;
	public function tableName()
	{
		return 'complain';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('info, created_time', 'required'),
			array('member_id, created_time, apply_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, info, created_time, apply_id', 'safe', 'on'=>'search'),
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
			'info' => 'Info',
			'created_time' => 'Created Time',
			'apply_id' => 'Apply',
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
		$criteria->compare('info',$this->info,true);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('apply_id',$this->apply_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Complain the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
