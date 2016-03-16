<?php
class LotteryLog extends CActiveRecord
{
	public $benben_id;//奔犇id
	public $lottery_num;//中奖号
	public $lottery_time;//抽奖时间
	public $is_send;//是否发奖
	public $phone;//手机
	public $name;//姓名
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lottery_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('benben_id, lottery_num, lottery_time,is_send','numerical','integerOnly'=>true),
			array('id,benben_id, lottery_num, lottery_time,is_send','safe','on'=>'search'),
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
			'benben_id' => '奔犇Id',
			'lottery_num' => '中奖号',
			'lottery_time' => '抽奖时间',
			'is_send' => '是否发奖',
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
		$criteria->compare('benben_id',$this->benben_id);
		$criteria->compare('lottery_num',$this->lottery_num);
		$criteria->compare('lottery_time',$this->lottery_time);
		$criteria->compare('is_send',$this->is_send);


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
