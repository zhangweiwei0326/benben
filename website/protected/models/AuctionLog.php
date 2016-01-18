<?php

/**
 * This is the model class for table "auction_log".
 *
 * The followings are the available columns in table 'auction_log':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class AuctionLog extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'auction_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id,auction_id,time,top', 'numerical', 'integerOnly'=>true),
            array('price,guarantee', 'numerical'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id,member_id,auction_id,price,guarantee,time,top', 'safe', 'on'=>'search'),
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
            'id' => 'Id',
            'member_id' => 'Member Id',
            'auction_id' => 'Auction Id',
            'price' => 'Price',
            'guarantee'=>'Guarantee',
            'time'=>'Time',
            'top'=>'Top',
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
        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('auction_id',$this->auction_id);
        $criteria->compare('price',$this->price);
        $criteria->compare('guarantee',$this->guarantee);
        $criteria->compare('time',$this->time);
        $criteria->compare('top',$this->top);

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
