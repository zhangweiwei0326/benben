<?php

/**
 * This is the model class for table "promotion_manage".
 *
 * The followings are the available columns in table 'promotion_manage':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class PromotionManage extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'promotion_manage';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id,store_id,time,offline_restrict,online_restrict,is_close,vip_time,vip_type,store_type', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, member_id,store_id, time,offline_restrict,online_restrict,is_close,vip_time,vip_type,store_type', 'safe', 'on'=>'search'),
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
            'store_id' => 'Store Id',
            'offline_restrict' => 'Offline Restrict',
            'online_restrict' => 'Online Restrict',
            'member_id'=>'Member Id',
            'is_close'=>'Is Close',
            'time'=>'Time',
            'vip_time'=>'Vip Time',
            'vip_type'=>'Vip Type',
            'store_type'=>'Store Type',
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
        $criteria->compare('store_id',$this->store_id);
        $criteria->compare('offline_restrict',$this->offline_restrict);
        $criteria->compare('online_restrict',$this->online_restrict);
        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('is_close',$this->is_close);
        $criteria->compare('time',$this->time);
        $criteria->compare('vip_time',$this->vip_time);
        $criteria->compare('vip_type',$this->vip_type);
        $criteria->compare('store_type',$this->store_type);

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
