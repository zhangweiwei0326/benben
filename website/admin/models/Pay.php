<?php

/**
 * This is the model class for table "pay".
 *
 * The followings are the available columns in table 'pay':
 * @property integer $id
 * @property integer $member_id
 * @property float $fee
 * @property integer $time
 * @property string $memo
 * @property integer $status
 * @property string $reason
 * @property integer $type
 * @property integer $payinfo_id
 * @property string $account
 * @property string $pay_name
 * @property string $pay_time
 */
class Pay extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pay';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id, time, status, type, payinfo_id', 'numerical', 'integerOnly'=>true),
            array('fee', 'numerical'),
            array('pay_time', 'length', 'max'=>50),
            array('memo, reason, account, pay_name', 'length', 'max'=>255),
            array('content', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, member_id, time, status, type, payinfo_id, fee, pay_time, memo, reason, account, pay_name, content', 'safe', 'on'=>'search'),
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
            'member_id' => 'Member Id',
            'fee' => 'Fee',
            'time' => 'Time',
            'memo' => 'Memo',
            'status' => 'Status',
            'reason' => 'Reason',
            'type' => 'Type',
            'payinfo_id' => 'Payinfo Id',
            'account' => 'Account',
            'pay_name' => 'Pay Name',
            'pay_time' => 'Pay Time',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('fee',$this->fee);
        $criteria->compare('time',$this->time);
        $criteria->compare('memo',$this->memo,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('reason',$this->reason,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('payinfo_id',$this->payinfo_id);
        $criteria->compare('account',$this->account,true);
        $criteria->compare('pay_name',$this->pay_name,true);
        $criteria->compare('pay_time',$this->pay_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return News the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
