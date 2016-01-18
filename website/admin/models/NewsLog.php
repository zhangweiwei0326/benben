<?php

/**
 * This is the model class for table "news_log".
 *
 * The followings are the available columns in table 'news_log':
 * @property integer $id
 * @property integer $sender
 * @property string $content
 * @property integer $number
 * @property string $unit
 * @property integer $created_time
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property string $member_id
 * @property integer $type
 * @property string $condition
 */
class NewsLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'news_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sender, number, created_time, province, city, area, street, type', 'numerical', 'integerOnly'=>true),
			array('unit, condition', 'length', 'max'=>255),
			array('content, member_id', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sender, content, number, unit, created_time, province, city, area, street, member_id, type, condition', 'safe', 'on'=>'search'),
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
			'content' => '发送内容',
			'number' => '推送人数',
			'unit' => '推送单位',
			'created_time' => 'Created Time',
			'province' => '推送地区',
			'city' => 'City',
			'area' => 'Area',
			'street' => 'Street',
			'member_id' => '接受会员',
			'type' => '消息类型，1定向消息，2系统消息',
			'condition' => '发送条件',
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
		$criteria->compare('content',$this->content,true);
		$criteria->compare('number',$this->number);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('member_id',$this->member_id,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('condition',$this->condition,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NewsLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
