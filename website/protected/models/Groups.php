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
 * @property integer $level
 * @property string $huanxin_groupid
 * @property integer $show_id
 * @property integer $is_delete
 */
class Groups extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
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
		return array(
			array('name, created_time', 'required'),
			array('province, city, area, street, member_id, number, status, created_time, level, show_id, is_delete', 'numerical', 'integerOnly'=>true),
			array('poster, name', 'length', 'max'=>255),
			array('huanxin_groupid', 'length', 'max'=>45),
			array('description, bulletin', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, poster, name, province, city, area, street, description, bulletin, member_id, number, status, created_time, level, huanxin_groupid, show_id, is_delete', 'safe', 'on'=>'search'),
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
			'poster' => 'Poster',
			'name' => 'Name',
			'province' => 'Province',
			'city' => 'City',
			'area' => 'Area',
			'street' => 'Street',
			'description' => 'Description',
			'bulletin' => 'Bulletin',
			'member_id' => 'Member',
			'number' => 'Number',
			'status' => 'Status',
			'created_time' => 'Created Time',
			'level' => 'Level',
			'huanxin_groupid' => 'Huanxin Groupid',
			'show_id' => 'Show',
			'is_delete' => 'Is Delete',
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
		$criteria->compare('level',$this->level);
		$criteria->compare('huanxin_groupid',$this->huanxin_groupid,true);
		$criteria->compare('show_id',$this->show_id);
		$criteria->compare('is_delete',$this->is_delete);

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
