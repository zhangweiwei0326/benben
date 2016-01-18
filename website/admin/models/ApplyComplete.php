<?php

/**
 * This is the model class for table "apply_complete".
 *
 * The followings are the available columns in table 'apply_complete':
 * @property integer $id
 * @property integer $apply_id
 * @property string $id_card
 * @property string $poster1
 * @property string $poster2
 * @property integer $type
 * @property integer $member_id
 * @property integer $created_time
 */
class ApplyComplete extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'apply_complete';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_card, poster1, poster2, created_time', 'required'),
			array('apply_id, type, member_id, created_time', 'numerical', 'integerOnly'=>true),
			array('id_card', 'length', 'max'=>20),
			array('poster1, poster2', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, apply_id, id_card, poster1, poster2, type, member_id, created_time', 'safe', 'on'=>'search'),
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
			'apply_id' => '申请标识',
			'id_card' => '身份证号码',
			'poster1' => '身份证正面照',
			'poster2' => '身份证反面照',
			'type' => '类型，1表示百姓网申请资料完善，2表示直通车申请资料完善',
			'member_id' => 'Member',
			'created_time' => '操作时间',
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
		$criteria->compare('apply_id',$this->apply_id);
		$criteria->compare('id_card',$this->id_card,true);
		$criteria->compare('poster1',$this->poster1,true);
		$criteria->compare('poster2',$this->poster2,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ApplyComplete the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
