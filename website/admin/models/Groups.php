<?php

/**
 * This is the model class for table "groups".
 *
 * The followings are the available columns in table 'groups':
 * @property integer $id
 * @property string $poster
 * @property string $name
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property string $description
 * @property string $bulletin
 * @property integer $member_id
 * @property integer $number
 * @property integer $status
 * @property integer $created_time
 */
class Groups extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $mname;
	public $nick_name;
	public $group_disable;
	public $is_delete;
	public function tableName()
	{
		return 'groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		//array('name, created_time', 'required'),
		return array(
			
			array('province, city, area, street, member_id, number, status, created_time', 'numerical', 'integerOnly'=>true),
			array('poster, name', 'length', 'max'=>255),
			array('description, bulletin', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, poster, name, province, city, area, street, description, bulletin, member_id, number, status, created_time', 'safe', 'on'=>'search'),
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
			'id' => '群组号',
			'poster' => '头像',
			'name' => '组群名称',
			'province' => '省份',
			'city' => '城市',
			'area' => '区',
			'street' => '街道',
			'description' => '简介',
			'bulletin' => '公告',
			'member_id' => '创建人',
			'member_phone'=>'创建人手机号码',	
			'number' => '加入人数',
			'status' => '是否禁用',
			'created_time' => '注册时间',
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
		$criteria->compare('poster',$this->poster,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('bulletin',$this->bulletin,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('number',$this->number);
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
	 * @return Groups the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
