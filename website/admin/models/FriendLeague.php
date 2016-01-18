<?php

/**
 * This is the model class for table "friend_league".
 *
 * The followings are the available columns in table 'friend_league':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $member_id
 * @property integer $number
 * @property integer $created_time
 * @property string $poster
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property string $announcement
 */
class FriendLeague extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $mname;
	public $mbenben_id;
	public $nickname;
	public $league_disable;
	public $is_delete;
	public function tableName()
	{
		return 'friend_league';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, number, created_time, province, city, area', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			array('poster', 'length', 'max'=>255),
			array('description, announcement', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, member_id, number, created_time, poster, province, city, area, announcement', 'safe', 'on'=>'search'),
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
			'name' => '联盟名称',
			'description' => '联盟简介',
			'member_id' => '创建人',
			'number' => '联盟人数',
			'created_time' => '创建时间',
			'poster' => '头像',
			'province' => 'Province',
			'city' => 'City',
			'area' => 'Area',
			'announcement' => '公告',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('number',$this->number);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('poster',$this->poster,true);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('announcement',$this->announcement,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FriendLeague the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
