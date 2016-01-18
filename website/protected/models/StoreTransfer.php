<?php

/**
 * This is the model class for table "store_transfer".
 *
 * The followings are the available columns in table 'store_transfer':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class StoreTransfer extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'store_transfer';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('apply_id,receive_id,store_id,status,created_time,deal_time', 'numerical', 'integerOnly'=>true),
            array('memo', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, apply_id,receive_id, store_id,status,created_time,deal_time,memo', 'safe', 'on'=>'search'),
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
            'shipping_id' => 'Shipping Id',
            'shipping_name'=>'Shipping Name',
            'shipping_code'=>'Shipping Code',
            'is_del'=>'Is Del',
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

        $criteria->compare('shipping_id',$this->shipping_id);
        $criteria->compare('shipping_name',$this->shipping_name);
        $criteria->compare('shipping_code',$this->shipping_code);
        $criteria->compare('is_del',$this->is_del);
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
