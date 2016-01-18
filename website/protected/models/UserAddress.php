<?php

/**
 * This is the model class for table "user_address".
 *
 * The followings are the available columns in table 'user_address':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class UserAddress extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user_address';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id,country,province,city,area,street,is_default', 'numerical', 'integerOnly'=>true),
            array('address_name,consignee,email,address,zipcode,tel,mobile,best_time', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('address_id, address_name,member_id, consignee,email,country,province,city,area,street, address,zipcode,tel,mobile,best_time,is_default', 'safe', 'on'=>'search'),
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
            'address_id' => 'Address Id',
            'address_name' => 'Address Name',
            'member_id' => 'Member Id',
            'consignee' => 'Consignee',
            'email'=>'Email',
            'country'=>'Country',
            'province'=>'Province',
            'city'=>'City',
            'area'=>'Area',
            'street'=>'Street',
            'address'=>'Address',
            'zipcode'=>'Zipcode',
            'tel'=>'Tel',
            'mobile'=>'Mobile',
            'best_time'=>'Best Time',
            'is_default'=>'Is Default',
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
        $criteria->compare('address_id',$this->address_id);
        $criteria->compare('address_name',$this->address_name,true);
        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('consignee',$this->consignee);
        $criteria->compare('email',$this->email);
        $criteria->compare('country',$this->country);
        $criteria->compare('province',$this->province);
        $criteria->compare('city',$this->city);
        $criteria->compare('area',$this->area);
        $criteria->compare('street',$this->street);
        $criteria->compare('address',$this->address);
        $criteria->compare('zipcode',$this->zipcode);
        $criteria->compare('mobile',$this->mobile);
        $criteria->compare('tel',$this->tel);
        $criteria->compare('best_time',$this->best_time);
        $criteria->compare('is_default',$this->is_default);

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
