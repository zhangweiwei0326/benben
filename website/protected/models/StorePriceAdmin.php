<?php

/**
 * This is the model class for table "store_price_admin".
 *
 * The followings are the available columns in table 'store_price_admin':
 * @property integer $id
 * @property integer $person_num
 * @property string $names
 * @property string $numbers
 * @property integer $horn_num
 * @property integer $sale_num
 * @property integer $type
 * @property string $price
 * @property integer $add_date
 * @property integer $update_date
 * @property string $title
 * @property string $poster
 */
class StorePriceAdmin extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'store_price_admin';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('person_num, horn_num, sale_num, type, add_date, update_date', 'numerical', 'integerOnly'=>true),
			array('names, numbers, poster', 'length', 'max'=>255),
			array('price', 'length', 'max'=>10),
			array('title', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, person_num, names, numbers, horn_num, sale_num, type, price, add_date, update_date, title, poster', 'safe', 'on'=>'search'),
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
			'person_num' => 'Person Num',
			'names' => 'Names',
			'numbers' => 'Numbers',
			'horn_num' => 'Horn Num',
			'sale_num' => 'Sale Num',
			'type' => 'Type',
			'price' => 'Price',
			'add_date' => 'Add Date',
			'update_date' => 'Update Date',
			'title' => 'Title',
			'poster' => 'Poster',
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
		$criteria->compare('person_num',$this->person_num);
		$criteria->compare('names',$this->names,true);
		$criteria->compare('numbers',$this->numbers,true);
		$criteria->compare('horn_num',$this->horn_num);
		$criteria->compare('sale_num',$this->sale_num);
		$criteria->compare('type',$this->type);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('add_date',$this->add_date);
		$criteria->compare('update_date',$this->update_date);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('poster',$this->poster,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return StorePriceAdmin the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
