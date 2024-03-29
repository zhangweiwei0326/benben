<?php

/**
 * This is the model class for table "enterprise_notice".
 *
 * The followings are the available columns in table 'enterprise_notice':
 * @property integer $id
 * @property integer $enterprise_id
 * @property integer $member_id
 * @property string $content
 * @property integer $sort
 * @property integer $update_time
 * @property integer $created_time
 * @property integer $apply_id
 */
class EnterpriseNotice extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $sname;
	public $sremark_name;
	public $bname;
	
	public function tableName()
	{
		return 'enterprise_notice';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('enterprise_id', 'required'),
			array('enterprise_id, member_id, sort, update_time, created_time, apply_id', 'numerical', 'integerOnly'=>true),
			array('content', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, enterprise_id, member_id, content, sort, update_time, created_time, apply_id', 'safe', 'on'=>'search'),
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
			'enterprise_id' => 'Enterprise',
			'member_id' => 'Member',
			'content' => 'Content',
			'sort' => 'Sort',
			'update_time' => 'Update Time',
			'created_time' => 'Created Time',
			'apply_id' => 'Apply',
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
		$criteria->compare('enterprise_id',$this->enterprise_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('apply_id',$this->apply_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EnterpriseNotice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
