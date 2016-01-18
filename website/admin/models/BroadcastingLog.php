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
 * @property integer $type
 */
class BroadcastingLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $member;
	public $counts;
	public $m_id;
	public $m_benben_id;
	public $m_phone;
	public $m_p;
	public $m_c;
	public $m_a;
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
			array('member_id, league_id, receive_count, created_time, is_del, type', 'numerical', 'integerOnly'=>true),
			array('friend_id, description, content', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, league_id, friend_id, receive_count, description, created_time, content, is_del, type', 'safe', 'on'=>'search'),
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
			'id' => '发送小喇叭历史记录',
			'member_id' => '发送人',
			'league_id' => '好友联盟ID',
			'friend_id' => 'Friend',
			'receive_count' => '有多少我接受',
			'description' => '描述',
			'created_time' => 'Created Time',
			'content' => '发送内容',
			'is_del' => 'Is Del',
			'type' => '小喇叭类型，1为直通车，否则为普通消息',
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
		$criteria->compare('type',$this->type);

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
