<?php

/**
 * This is the model class for table "league_member".
 *
 * The followings are the available columns in table 'league_member':
 * @property integer $id
 * @property integer $league_id
 * @property integer $member_id
 * @property integer $created_time
 * @property integer $type
 * @property integer $remark_content
 * @property integer $status
 * @property string $nick_name
 */
class LeagueMember extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'league_member';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('league_id, member_id, created_time, type, remark_content, status', 'numerical', 'integerOnly'=>true),
            array('nick_name', 'length', 'max'=>45),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, league_id, member_id, created_time, type, remark_content, status, nick_name', 'safe', 'on'=>'search'),
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
            'league_id' => 'League',
            'member_id' => 'Member',
            'created_time' => 'Created Time',
            'type' => 'Type',
            'remark_content' => 'Remark Content',
            'status' => 'Status',
            'nick_name' => 'Nick Name',
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
        $criteria->compare('league_id',$this->league_id);
        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('created_time',$this->created_time);
        $criteria->compare('type',$this->type);
        $criteria->compare('remark_content',$this->remark_content);
        $criteria->compare('status',$this->status);
        $criteria->compare('nick_name',$this->nick_name,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return LeagueMember the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}