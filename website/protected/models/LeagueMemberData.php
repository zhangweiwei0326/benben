<?php

/**
 * This is the model class for table "league_member_data".
 *
 * The followings are the available columns in table 'league_member_data':
 * @property integer $id
 * @property integer $data_id
 * @property string $remark_name
 * @property string $name
 * @property integer $league_id
 */
class LeagueMemberData extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'league_member_data';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('data_id', 'required'),
			array('data_id, league_id', 'numerical', 'integerOnly'=>true),
			array('remark_name, name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, data_id, remark_name, name, league_id', 'safe', 'on'=>'search'),
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
			'data_id' => 'Data',
			'remark_name' => 'Remark Name',
			'name' => 'Name',
			'league_id' => 'League',
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
		$criteria->compare('data_id',$this->data_id);
		$criteria->compare('remark_name',$this->remark_name,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('league_id',$this->league_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LeagueMemberData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
