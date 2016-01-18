<?php

/**
 * This is the model class for table "creation".
 *
 * The followings are the available columns in table 'creation':
 * @property integer $id
 * @property string $description
 * @property integer $member_id
 * @property integer $status
 * @property integer $type
 * @property integer $created_time
 * @property integer $views
 * @property integer $goods
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property integer $is_delete
 */
class Creation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $mname;
	public $benben_id;
	public $status1;
	public $nick_name;
	public function tableName()
	{
		return 'creation';
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
			array('member_id, status, type, created_time, views, goods, province, city, area, street, is_delete', 'numerical', 'integerOnly'=>true),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, description, member_id, status, type, created_time, views, goods, province, city, area, street, is_delete', 'safe', 'on'=>'search'),
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
			'description' => '简介',
			'member_id' => '发帖人',
			'member_phone' => '手机号码',
			'status' => '状态',
			'type' => '类型',
			'created_time' => '发布时间',
			'views' => '浏览量',
			'goods' => '点赞数',
			'province' => 'Province',
			'city' => 'City',
			'area' => 'Area',
			'street' => 'Street',
			'is_delete' => '是否删除',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('views',$this->views);
		$criteria->compare('goods',$this->goods);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('is_delete',$this->is_delete);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Creation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
