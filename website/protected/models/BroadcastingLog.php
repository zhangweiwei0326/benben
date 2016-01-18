<?php

/**
 * This is the model class for table "broadcasting_log".
 *
 * The followings are the available columns in table 'broadcasting_log':
 * @property integer $id
 * @property integer $member_id
 * @property integer $league_id
 * @property string $friend_id
 * @property integer $receive_count
 * @property string $description
 * @property integer $created_time
 * @property string $content
 * @property integer $is_del
 */
class BroadcastingLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'broadcasting_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, league_id, receive_count, created_time, is_del', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('friend_id, content', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, league_id, friend_id, receive_count, description, created_time, content, is_del', 'safe', 'on'=>'search'),
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
			'member_id' => 'Member',
			'league_id' => 'League',
			'friend_id' => 'Friend',
			'receive_count' => 'Receive Count',
			'description' => 'Description',
			'created_time' => 'Created Time',
			'content' => 'Content',
			'is_del' => 'Is Del',
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
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('league_id',$this->league_id);
		$criteria->compare('friend_id',$this->friend_id,true);
		$criteria->compare('receive_count',$this->receive_count);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('is_del',$this->is_del);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BroadcastingLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
