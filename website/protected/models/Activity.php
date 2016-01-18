<?php

/**
 * This is the model class for table "activity".
 *
 * The followings are the available columns in table 'activity':
 * @property integer $id
 * @property string $title
 * @property string $amount
 * @property string $description
 * @property integer $member_id
 * @property integer $status
 * @property integer $deadline
 * @property integer $quoted_number
 * @property integer $created_time
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property integer $is_close
 * @property integer $is_accept
 */
class Activity extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'activity';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id, time, poster_num, is_close', 'numerical', 'integerOnly'=>true),
            array('poster_cover', 'length', 'max'=>255),
            array('title', 'length', 'max'=>54),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, title, member_id, time, poster_num, poster_cover, is_close', 'safe', 'on'=>'search'),
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
            'title' => 'Title',
            'member_id' => 'Member Id',
            'time' => 'Time',
            'poster_num' => 'Poster Num',
            'poster_cover' => 'Poster Cover',
            'is_close' => 'Is Close',
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
        $criteria->compare('title',$this->title,true);
        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('time',$this->time);
        $criteria->compare('poster_num',$this->poster_num);
        $criteria->compare('poster_cover',$this->poster_cover);
        $criteria->compare('is_close',$this->is_close);

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
