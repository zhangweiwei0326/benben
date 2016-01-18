<?php

/**
 * This is the model class for table "member".
 *
 * The followings are the available columns in table 'member':
 * @property integer $id
 * @property integer $benben_id
 * @property string $name
 * @property string $nick_name
 * @property string $poster
 * @property string $phone
 * @property string $id_card
 * @property string $card_poster
 * @property integer $sex
 * @property string $age
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property string $cornet
 * @property integer $integral
 * @property integer $coin
 * @property integer $status
 * @property integer $created_time
 * @property string $token
 * @property string $password
 * @property string $huanxin_username
 * @property string $huanxin_uuid
 * @property integer $userinfo
 * @property string $address
 * @property string $qrcode
 * @property string $huanxin_password
 * @property string $phone_model
 * @property integer $creation_disable
 * @property integer $buy_disable
 * @property integer $enterprise_disable
 * @property integer $group_disable
 * @property integer $store_disable
 * @property integer $league_disable
 */
class Member extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $short_phone;
	public function tableName()
	{
		return 'member';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('benben_id, sex, province, city, area, street, integral, coin, status, created_time, userinfo, creation_disable, buy_disable, enterprise_disable, group_disable, store_disable, league_disable', 'numerical', 'integerOnly'=>true),
			array('name, nick_name, id_card, cornet', 'length', 'max'=>20),
			array('poster, card_poster, token, huanxin_username, huanxin_uuid, address, qrcode, huanxin_password', 'length', 'max'=>255),
			array('phone', 'length', 'max'=>11),
			array('age, password', 'length', 'max'=>40),
			array('phone_model', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, benben_id, name, nick_name, poster, phone, id_card, card_poster, sex, age, province, city, area, street, cornet, integral, coin, status, created_time, token, password, huanxin_username, huanxin_uuid, userinfo, address, qrcode, huanxin_password, phone_model, creation_disable, buy_disable, enterprise_disable, group_disable, store_disable, league_disable', 'safe', 'on'=>'search'),
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
			'benben_id' => '奔犇号',
			'name' => '姓名',
			'nick_name' => '昵称',
			'poster' => '头像',
			'phone' => '手机号',
			'id_card' => '身份证号码',
			'card_poster' => '身份证附件',
			'sex' => '性别',
			'age' => '年龄',
			'province' => '省份',
			'city' => '城市',
			'area' => '区',
			'street' => '街道',
			'cornet' => '短号',
			'integral' => '积分',
			'coin' => '犇币',
			'status' => '是否禁用',
			'created_time' => '注册时间',
			'token' => '自动登录令牌',
			'password' => '密码',
			'huanxin_username' => '环信用户名',
			'huanxin_uuid' => '环信UUID',
			'userinfo' => '会员信息完善情况, 完善直通车+1,完善百姓网+2,4,8',
			'address' => '详细地址',
			'qrcode' => '二维码',
			'huanxin_password' => '环信密码',
			'phone_model' => '手机型号',
			'creation_disable' => '微创作禁用，0为默认 , 启用、1为禁用1周、2为禁用2周、3为禁用1个月、4为禁用3个月、5为无限期',
			'buy_disable' => '我要买禁用，0为默认 , 启用、1为禁用1周、2为禁用2周、3为禁用1个月、4为禁用3个月、5为无限期',
			'enterprise_disable' => '政企禁用，0为默认 , 启用、1为禁用1周、2为禁用2周、3为禁用1个月、4为禁用3个月、5为无限期',
			'group_disable' => '群组禁用，0为默认 , 启用、1为禁用1周、2为禁用2周、3为禁用1个月、4为禁用3个月、5为无限期',
			'store_disable' => '号码直通车禁用，0为默认 , 启用、1为禁用1周、2为禁用2周、3为禁用1个月、4为禁用3个月、5为无限期',
			'league_disable' => '好友联盟禁用，0为默认 , 启用、1为禁用1周、2为禁用2周、3为禁用1个月、4为禁用3个月、5为无限期',
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
		$criteria->compare('benben_id',$this->benben_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('nick_name',$this->nick_name,true);
		$criteria->compare('poster',$this->poster,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('id_card',$this->id_card,true);
		$criteria->compare('card_poster',$this->card_poster,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('age',$this->age,true);
		$criteria->compare('province',$this->province);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('cornet',$this->cornet,true);
		$criteria->compare('integral',$this->integral);
		$criteria->compare('coin',$this->coin);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('token',$this->token,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('huanxin_username',$this->huanxin_username,true);
		$criteria->compare('huanxin_uuid',$this->huanxin_uuid,true);
		$criteria->compare('userinfo',$this->userinfo);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('qrcode',$this->qrcode,true);
		$criteria->compare('huanxin_password',$this->huanxin_password,true);
		$criteria->compare('phone_model',$this->phone_model,true);
		$criteria->compare('creation_disable',$this->creation_disable);
		$criteria->compare('buy_disable',$this->buy_disable);
		$criteria->compare('enterprise_disable',$this->enterprise_disable);
		$criteria->compare('group_disable',$this->group_disable);
		$criteria->compare('store_disable',$this->store_disable);
		$criteria->compare('league_disable',$this->league_disable);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Member the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
