<?php
class PrizeSetting extends CActiveRecord
{
	public $id;//奖品id
	public $prize_name;//奖品名
	public $prize;//奖项
	public $frequency;//中奖次数
	public $last_time;//最近一次中奖
	public $statues;//状态
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'prize_setting';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id,frequency,last_time,statues','numerical','integerOnly'=>true),
			array('id,prize_name, prize,frequency,last_time,statues','safe','on'=>'search'),

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
			'prize_name' => '奖品名',
			'prize' => '奖项',
			'frequency' => '中奖次数',
			'last_time' => '最近一次中奖',
			'statues' => '状态',
			'image' => ' 奖品图片',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('prize_name',$this->prize_name);
		$criteria->compare('prize',$this->prize);
		$criteria->compare('frequency',$this->frequency);
		$criteria->compare('last_time',$this->last_time);
		$criteria->compare('statues',$this->statues);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LoginLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
