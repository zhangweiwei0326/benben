<?php

/**
 * This is the model class for table "payment".
 *
 * The followings are the available columns in table 'payment':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class Payment extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'payment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pay_order,enabled', 'numerical', 'integerOnly'=>true),
            array('pay_code ,pay_name,pay_fee,pay_desc,pay_config', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('pay_order, enabled,pay_code, pay_name,pay_fee,pay_desc,pay_config', 'safe', 'on'=>'search'),
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
            'pay_id' => 'Pay Id',
            'pay_code' => 'Pay Code',
            'pay_name' => 'Pay Name',
            'pay_fee' => 'Pay Fee',
            'pay_desc'=>'Pay Desc',
            'pay_order'=>'Pay Order',
            'pay_config'=>'Pay Config',
            'enabled'=>'Enabled',
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

        $criteria->compare('pay_id',$this->pay_id);
        $criteria->compare('pay_code',$this->pay_code);
        $criteria->compare('pay_name',$this->pay_name);
        $criteria->compare('pay_fee',$this->pay_fee);
        $criteria->compare('pay_desc',$this->pay_desc);
        $criteria->compare('pay_order',$this->pay_order);
        $criteria->compare('pay_config',$this->pay_config);
        $criteria->compare('enabled',$this->enabled);

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
