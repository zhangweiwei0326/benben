<?php

/**
 * This is the model class for table "splash".
 *
 * The followings are the available columns in table 'splash':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class Splash extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'splash';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, image', 'required'),
			array('created_time', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			array('image', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, image, created_time', 'safe', 'on'=>'search'),
			
			array('image',  
                    'file',    //定义为file类型  
                    'allowEmpty'=>true,   
                    'types'=>'jpg,png,gif,jpeg',   //上传文件的类型  
                    'maxSize'=>1024*1024*100,    //上传大小限制，注意不是php.ini中的上传文件大小  
                    'tooLarge'=>'文件大于100M，上传失败！请上传小于100M的文件！'  
                ),  
			
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
			'name' => '图片名称',
			'image' => '图片路径',
			'created_time' => '添加时间',
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
		$criteria->compare('image',$this->image,true);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Splash the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
