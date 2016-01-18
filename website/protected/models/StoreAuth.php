<?php

/**
 * This is the model class for table "store_auth".
 *
 * The followings are the available columns in table 'store_auth':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class StoreAuth extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'store_auth';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id,first_uptime,time,status,type', 'numerical', 'integerOnly'=>true),
            array('poster_front,poster_back,poster_licence,id_card,real_name,company', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, real_name,id_card, poster_front,poster_back,poster_licence,member_id,first_uptime,status, time,type,company', 'safe', 'on'=>'search'),
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
            'real_name' => 'Real Name',
            'id_card' => 'Id Card',
            'poster_front' => 'Poster Front',
            'poster_back'=>'Poster Back',
            'poster_licence'=>'Poster Licence',
            'member_id'=>'Member Id',
            'first_uptime'=>'First Uptime',
            'status'=>'Status',
            'time'=>'Time',
            'type'=>'Type',
            'company'=>'Company'
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
        $criteria->compare('real_name',$this->real_name,true);
        $criteria->compare('id_card',$this->id_card);
        $criteria->compare('poster_front',$this->poster_front);
        $criteria->compare('poster_back',$this->poster_back);
        $criteria->compare('poster_licence',$this->poster_licence);
        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('first_uptime',$this->first_uptime);
        $criteria->compare('status',$this->status);
        $criteria->compare('time',$this->time);
        $criteria->compare('type',$this->type);
        $criteria->compare('company',$this->company);

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
