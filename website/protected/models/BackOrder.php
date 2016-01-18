<?php

/**
 * This is the model class for table "back_order".
 *
 * The followings are the available columns in table 'back_order':
 * @property integer $id
 * @property string $title
 * @property string $amount
 */
class BackOrder extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'back_order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, status, apply_id, train_id,apply_time,deal_time', 'numerical', 'integerOnly'=>true),
            array('apply_reason,refuse_reason','length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('back_id, order_id, status, apply_reason, refuse_reason, apply_id, train_id,apply_time,deal_time', 'safe', 'on'=>'search'),
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
            'back_id' => 'Back Id',
            'order_id' => 'Order Id',
            'status' => 'Status',
            'apply_reason' => 'Apply Reason',
            'refuse_reason' => 'Refuse Reason',
            'apply_id' => 'Apply Id',
            'train_id' => 'Train Id',
            'apply_time' => 'Apply Time',
            'deal_time' => 'Deal_Time',
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

        $criteria->compare('back_id',$this->back_id);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('status',$this->status);
        $criteria->compare('apply_reason',$this->apply_reason);
        $criteria->compare('refuse_reason',$this->refuse_reason);
        $criteria->compare('apply_id',$this->apply_id);
        $criteria->compare('train_id',$this->train_id);
        $criteria->compare('apply_time',$this->apply_time);
        $criteria->compare('deal_time',$this->deal_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Buy the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
