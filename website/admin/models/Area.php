<?php

/**
 * This is the model class for table "area".
 *
 * The followings are the available columns in table 'area':
 * @property string $pid
 * @property integer $bid
 * @property integer $parent_bid
 * @property string $area_name
 * @property string $pinyin
 * @property integer $level
 * @property integer $last
 * @property string $description
 * @property string $create_time
 * @property string $update_time
 * @property integer $del_flag
 */
class Area extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'area';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, bid, parent_bid, area_name, level, last', 'required'),
			array('bid, parent_bid, level, last, del_flag', 'numerical', 'integerOnly'=>true),
			array('pid', 'length', 'max'=>40),
			array('area_name, pinyin', 'length', 'max'=>200),
			array('description', 'length', 'max'=>2000),
			array('create_time, update_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pid, bid, parent_bid, area_name, pinyin, level, last, description, create_time, update_time, del_flag', 'safe', 'on'=>'search'),
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
			'pid' => '主键标识',
			'bid' => '业务主键（地区编码）',
			'parent_bid' => '上级地区编码',
			'area_name' => '地区名称',
			'pinyin' => '地区拼音（用于Suggest快速搜索）',
			'level' => '地区级别',
			'last' => '是否末级（1：是、0：否）',
			'description' => '地区简介',
			'create_time' => '创建时间',
			'update_time' => '修改时间',
			'del_flag' => '软删除标记（1：删除、0：不删除）',
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

		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('bid',$this->bid);
		$criteria->compare('parent_bid',$this->parent_bid);
		$criteria->compare('area_name',$this->area_name,true);
		$criteria->compare('pinyin',$this->pinyin,true);
		$criteria->compare('level',$this->level);
		$criteria->compare('last',$this->last);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('del_flag',$this->del_flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Area the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
