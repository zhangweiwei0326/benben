<?php

/**
 * This is the model class for table "top_auction".
 *
 * The followings are the available columns in table 'top_auction':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class TopAuction extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'top_auction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pid,place,industry,province,city,area,start_time,end_time,top_start_period,top_end_period,is_close,is_paid,owner_id', 'numerical', 'integerOnly' => true),
            array('start_price,add_step,guarantee', 'numerical'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('auction_id,pid,is_paid,owner_id,place,industry,province,city,area,start_time,end_time,start_price,add_step,guarantee,top_start_period,top_end_period,is_close', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'auction_id' => 'Auction Id',
            'pid' => 'Pid',
            'place' => 'Place',
            'industry' => 'Industry',
            'province' => 'province',
            'city' => 'City',
            'area' => 'Area',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'start_price' => 'Start Price',
            'add_step' => 'Add Step',
            'guarantee' => 'Guarantee',
            'top_start_period' => 'Top Start Period',
            'top_end_period' => 'Top End Period',
            'is_close' => 'Is Close',
            'is_paid' => 'Is Paid',
            'owner_id' => 'Owner Id',
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

        $criteria = new CDbCriteria;
        $criteria->compare('auction_id', $this->auction_id);
        $criteria->compare('pid', $this->pid);
        $criteria->compare('place', $this->place);
        $criteria->compare('industry', $this->industry);
        $criteria->compare('province', $this->province);
        $criteria->compare('city', $this->city);
        $criteria->compare('area', $this->area);
        $criteria->compare('start_time', $this->start_time);
        $criteria->compare('end_time', $this->end_time);
        $criteria->compare('start_price', $this->start_price);
        $criteria->compare('add_step', $this->add_step);
        $criteria->compare('guarantee', $this->guarantee);
        $criteria->compare('top_start_period', $this->top_start_period);
        $criteria->compare('top_end_period', $this->top_end_period);
        $criteria->compare('is_close', $this->is_close);
        $criteria->compare('is_paid', $this->is_paid);
        $criteria->compare('owner_id', $this->owner_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Splash the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
