<?php

/**
 * This is the model class for table "push_log".
 *
 * The followings are the available columns in table 'push_log':
 * @property integer $id
 * @property integer $sender
 * @property integer $number
 * @property integer $created_time
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property integer $industry
 * @property string $member_id
 * @property integer $buyid
 */
class PushLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $bprovince;
	public $bcity;
	public $barea;
	public $benben_id;
	public $name;
	public $nick_name;
	public $title;
	public function tableName()
	{
		return 'push_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sender, number, created_time, province, city, area, street, industry, buyid', 'numerical', 'integerOnly'=>true),
			array('member_id', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sender, number, created_time, province, city, area, street, industry, member_id, buyid', 'safe', 'on'=>'search'),
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
			'sender' => '发送者',
			'number' => '推送人数',
			'created_time' => 'Created Time',
			'province' => '地区',
			'city' => 'City',
			'area' => 'Area',
			'street' => 'Street',
			'industry' => '行业',
			'member_id' => '接受会员',
			'buyid' => '我要买ID',
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
		$criteria->compare('sender',$this->sender);
		$criteria->compare('number',$this->number);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('industry',$this->industry);
		$criteria->compare('member_id',$this->member_id,true);
		$criteria->compare('buyid',$this->buyid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PushLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
