<?php

/**
 * This is the model class for table "enterprise".
 *
 * The followings are the available columns in table 'enterprise':
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property string $description
 * @property integer $member_id
 * @property integer $number
 * @property integer $status
 * @property integer $created_time
 * @property integer $short_length
 * @property integer $origin
 */
class Enterprise extends CActiveRecord
{
	public $mname;
	public $nick_name;
	public $enterprise_disable;
	public $a_e;
	/**
	 * @return string the associated database table name
	 */	
	public function tableName()
	{
		return 'enterprise';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, created_time', 'required'),
			array('type, province, city, area, street, member_id, number, status, created_time, short_length, origin', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, type, province, city, area, street, description, member_id, number, status, created_time, short_length, origin', 'safe', 'on'=>'search'),
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
			'name' => '通讯录名称',
			'type' => '类型',
			'province' => '省份',
			'city' => '城市',
			'area' => '区',
			'street' => '街道',
			'description' => '简介',
			'member_id' => '创建人ID',
			'number' => '加入人数',
			'status' => '是否禁用',
			'created_time' => '注册时间',
			'short_length' => 'Short Length',
			'origin' => '创建方式，1客户端2后台',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('number',$this->number);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('short_length',$this->short_length);
		$criteria->compare('origin',$this->origin);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Enterprise the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
