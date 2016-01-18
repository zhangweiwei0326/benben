<?php

/**
 * This is the model class for table "collect_goods".
 *
 * The followings are the available columns in table 'collect_goods':
 * @property integer $id
 * @property integer $member_id
 * @property string $info
 * @property integer $created_time
 */
class CollectGoods extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'collect_goods';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id, add_time,promotion_id,is_attention,type', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('rec_id, member_id, promotion_id, add_time, is_attention, type', 'safe', 'on'=>'search'),
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
            'rec_id' => 'Rec Id',
            'member_id' => 'Member',
            'promotion_id' => 'Promotion Id',
            'add_time' => 'Add Time',
            'is_attention' => 'Is Attention',
            'type' => 'Type',
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

        $criteria->compare('rec_id',$this->rec_id);
        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('promotion_id',$this->promotion_id);
        $criteria->compare('add_time',$this->add_time);
        $criteria->compare('is_attention',$this->is_attention);
        $criteria->compare('type',$this->type);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Complain the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
