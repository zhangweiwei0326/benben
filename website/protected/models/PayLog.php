<?php

/**
 * This is the model class for table "pay_log".
 *
 * The followings are the available columns in table 'pay_log':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class PayLog extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pay_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id,is_paid,order_type', 'numerical', 'integerOnly'=>true),
            array('order_amount','numerical'),
            array('trade_no,trade_status,seller_email,buyer_email','length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('log_id, order_id,trade_no,order_amount, is_paid,order_type,trade_status,seller_email,buyer_email', 'safe', 'on'=>'search'),
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
            'log_id' => 'Log Id',
            'order_id' => 'Order Id',
            'trade_no' => 'Trade No',
            'order_amount' => 'Order Amount',
            'order_type' => 'Order Type',
            'is_paid'=>'Is Paid',
            'trade_status'=>'Trade Status',
            'seller_email'=>'Seller Email',
            'buyer_email'=>'Buyer Email',
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

        $criteria->compare('log_id',$this->log_id);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('trade_no',$this->trade_no);
        $criteria->compare('order_amount',$this->order_amount);
        $criteria->compare('order_type',$this->order_type);
        $criteria->compare('is_paid',$this->is_paid);
        $criteria->compare('trade_status',$this->trade_status);
        $criteria->compare('seller_email',$this->seller_email);
        $criteria->compare('buyer_email',$this->buyer_email);

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
