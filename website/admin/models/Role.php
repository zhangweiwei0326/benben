<?php

/**
 * This is the model class for table "role".
 *
 * The followings are the available columns in table 'role':
 * @property integer $id
 * @property string $role_name
 * @property integer $domember
 * @property integer $dobaixing
 * @property integer $doenterprise
 * @property integer $dogroup
 * @property integer $dostore
 * @property integer $docreation
 * @property integer $dorelease
 * @property integer $dofriend
 * @property integer $doshop
 * @property integer $dohappy
 * @property integer $donews
 * @property integer $dowebsite
 * @property integer $dosystem
 * @property integer $created_time
 * @property integer $doleague
 * @property integer $dofind
 * @property integer $doother
 * @property integer $doservice
 * @property integer $dopay
 * @property integer $doapplyregister
 */
class Role extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'role';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('role_name, doshop', 'required'),
			array('domember, dobaixing, doenterprise, dogroup, dostore, docreation, dorelease, dofriend, doshop, dohappy, donews, dowebsite, dosystem, created_time, doleague, dofind, doother, doservice, dopay, doapplyregister', 'numerical', 'integerOnly'=>true),
			array('role_name', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, role_name, domember, dobaixing, doenterprise, dogroup, dostore, docreation, dorelease, dofriend, doshop, dohappy, donews, dowebsite, dosystem, created_time, doleague, dofind, doother, doservice, dopay, doapplyregister', 'safe', 'on'=>'search'),
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
			'id' => 'ID
',
			'role_name' => '角色名
',
			'domember' => '用户管理权限',
			'dobaixing' => '百姓网管理权限',
			'doenterprise' => '政企通讯录管理
',
			'dogroup' => '群组通讯录管理',
			'dostore' => '号码直通车管理',
			'docreation' => '微创作管理',
			'dorelease' => '我要买管理',
			'dofriend' => '朋友圈管理',
			'doshop' => '商家管理',
			'dohappy' => '开心一刻管理',
			'donews' => '消息管理',
			'dowebsite' => '网站维护',
			'dosystem' => '系统管理权限管理',
			'created_time' => 'Created Time',
			'doleague' => 'Doleague',
			'dofind' => '发现管理',
			'doother' => '其他管理',
			'doservice' => '服务管理',
			'dopay' => '支付管理',
			'doapplyregister' => '申请审核管理',
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
		$criteria->compare('role_name',$this->role_name,true);
		$criteria->compare('domember',$this->domember);
		$criteria->compare('dobaixing',$this->dobaixing);
		$criteria->compare('doenterprise',$this->doenterprise);
		$criteria->compare('dogroup',$this->dogroup);
		$criteria->compare('dostore',$this->dostore);
		$criteria->compare('docreation',$this->docreation);
		$criteria->compare('dorelease',$this->dorelease);
		$criteria->compare('dofriend',$this->dofriend);
		$criteria->compare('doshop',$this->doshop);
		$criteria->compare('dohappy',$this->dohappy);
		$criteria->compare('donews',$this->donews);
		$criteria->compare('dowebsite',$this->dowebsite);
		$criteria->compare('dosystem',$this->dosystem);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('doleague',$this->doleague);
		$criteria->compare('dofind',$this->dofind);
		$criteria->compare('doother',$this->doother);
		$criteria->compare('doservice',$this->doservice);
		$criteria->compare('dopay',$this->dopay);
		$criteria->compare('doapplyregister',$this->doapplyregister);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Role the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
