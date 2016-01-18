<?php

/**
 * This is the model class for table "number_train".
 *
 * The followings are the available columns in table 'number_train':
 * @property integer $id
 * @property string $name
 * @property string $short_name
 * @property string $poster
 * @property string $phone
 * @property string $telephone
 * @property integer $industry
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property double $lat
 * @property double $lng
 * @property string $address
 * @property string $tag
 * @property string $description
 * @property integer $member_id
 * @property integer $istop
 * @property integer $status
 * @property integer $created_time
 * @property integer $views
 * @property integer $is_close
 */
class NumberTrain extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'number_train';
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
			array('industry, auth_grade, province, city, area, street, member_id, istop, status, created_time, views, is_close,score', 'numerical', 'integerOnly'=>true),
			array('lat, lng', 'numerical'),
			array('name, short_name, poster', 'length', 'max'=>255),
			array('phone', 'length', 'max'=>11),
			array('telephone', 'length', 'max'=>13),
			array('address, tag, description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, short_name,auth_grade, poster, phone, telephone, industry, province, city, area, street, lat, lng, address, tag, description, member_id, istop, status, created_time, views, is_close,score', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'short_name' => 'Short Name',
			'poster' => 'Poster',
			'phone' => 'Phone',
			'telephone' => 'Telephone',
			'industry' => 'Industry',
			'province' => 'Province',
			'city' => 'City',
			'area' => 'Area',
			'street' => 'Street',
			'lat' => 'Lat',
			'lng' => 'Lng',
			'address' => 'Address',
			'tag' => 'Tag',
			'description' => 'Description',
			'member_id' => 'Member',
			'istop' => 'Istop',
			'status' => 'Status',
			'created_time' => 'Created Time',
			'views' => 'Views',
			'is_close' => 'Is Close',
			'score'=>'Score',
			'auth_grade'=>'Auth Grade',
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
		$criteria->compare('short_name',$this->short_name,true);
		$criteria->compare('poster',$this->poster,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('industry',$this->industry);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('lng',$this->lng);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('istop',$this->istop);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('views',$this->views);
		$criteria->compare('is_close',$this->is_close);
		$criteria->compare('score',$this->score);
		$criteria->compare('auth_grade',$this->auth_grade);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NumberTrain the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
