<?php

/**
 * This is the model class for table "bxapply".
 *
 * The followings are the available columns in table 'bxapply':
 * @property integer $id
 * @property integer $member_id
 * @property integer $enterprise_id
 * @property string $phone
 * @property string $name
 * @property string $short_phone
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property integer $status
 * @property integer $created_time
 */
class Bxapply extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $card;
	public $poster1;
	public $poster2;
	public $mname;
	public $nick_name;
	public $mphone;
	public $benben_id;
	public $id_enable;
	public function tableName()
	{
		return 'bxapply';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		//array('phone, name, created_time', 'required'),
		return array(
			array('member_id, enterprise_id, province, city, area, street, status, created_time', 'numerical', 'integerOnly'=>true),
			array('phone', 'length', 'max'=>11),
			array('name, short_phone', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id,enterprise_id, phone, name, short_phone, province, city, area, street, status, created_time', 'safe', 'on'=>'search'),
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
			'member_id' => '提交人',
			'enterprise_id' => '百姓网段',
			'phone' => '手机号码',
			'name' => '姓名',
			'short_phone' => '百姓网号',
			'province' => '省份',
			'city' => '城市',
			'area' => '区',
			'street' => '街道',
			'status' => '状态',
			'created_time' => '申请时间',
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
		$criteria->compare('enterprise_id',$this->enterprise_id);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('short_phone',$this->short_phone,true);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Bxapply the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
