<?php

/**
 * This is the model class for table "apply_register".
 *
 * The followings are the available columns in table 'apply_register':
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $identity_num
 * @property string $identity_attachment
 * @property string $identity_attachment_more
 * @property string $enterprise_name
 * @property integer $enterprise_type
 * @property string $login_name
 * @property string $login_password
 * @property string $email
 * @property integer $apply_type
 * @property integer $status
 * @property string $review_name
 * @property integer $review_time
 * @property integer $created_time
 * @property integer $enterprise_id
 */
class ApplyRegister extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'apply_register';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('enterprise_type, apply_type, status, review_time, created_time, enterprise_id', 'numerical', 'integerOnly'=>true),
			array('name, identity_num, identity_attachment, identity_attachment_more, enterprise_name', 'length', 'max'=>255),
			array('phone', 'length', 'max'=>20),
			array('login_name, login_password, email, review_name', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, phone, identity_num, identity_attachment, identity_attachment_more, enterprise_name, enterprise_type, login_name, login_password, email, apply_type, status, review_name, review_time, created_time, enterprise_id', 'safe', 'on'=>'search'),
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
			'name' => '申请名称（姓名/企业/组织全程/学校）',
			'phone' => '手机号码',
			'identity_num' => '身份证号码/营业执照注册号/组织机构代码/办学许可证代码',
			'identity_attachment' => '（身份证号码/营业执照注册号/组织机构代码/办学许可证代码）附件',
			'identity_attachment_more' => '个人申请：身份证反面信息',
			'enterprise_name' => '政企通讯录名称',
			'enterprise_type' => '政企通讯录类型：1 企业政企 2 虚拟网政企',
			'login_name' => '登录名',
			'login_password' => '登录密码',
			'email' => '密保邮箱',
			'apply_type' => '申请类型：1个人 2 企业/组织 3 学校',
			'status' => '审核状态：0.待审核 1.同意 2.拒绝',
			'review_name' => '审核人',
			'review_time' => '审核时间',
			'created_time' => '申请时间',
			'enterprise_id' => '政企通讯录id',
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
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('identity_num',$this->identity_num,true);
		$criteria->compare('identity_attachment',$this->identity_attachment,true);
		$criteria->compare('identity_attachment_more',$this->identity_attachment_more,true);
		$criteria->compare('enterprise_name',$this->enterprise_name,true);
		$criteria->compare('enterprise_type',$this->enterprise_type);
		$criteria->compare('login_name',$this->login_name,true);
		$criteria->compare('login_password',$this->login_password,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('apply_type',$this->apply_type);
		$criteria->compare('status',$this->status);
		$criteria->compare('review_name',$this->review_name,true);
		$criteria->compare('review_time',$this->review_time);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('enterprise_id',$this->enterprise_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ApplyRegister the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
