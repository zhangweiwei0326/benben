<?php

/**
 * This is the model class for table "enterprise_role".
 *
 * The followings are the available columns in table 'enterprise':
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $province
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property string $description
 * @property integer $member_id
 * @property integer $number
 * @property integer $status
 * @property integer $created_time
 * @property integer $short_length
 * @property integer $origin
 */
class EnterpriseRole extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'enterprise_role';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('enterprise_id, enterprise_apply, member_add, access_level, member_limit, broadcast_num, broadcast_available, group_level, manage_num, created_time, access_level_set', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, enterprise_id, enterprise_apply, member_add, access_level, member_limit, broadcast_num, broadcast_available, group_level, manage_num, created_time, access_level_set', 'safe', 'on'=>'search'),
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
            'enterprise_id' => 'Enterprise Id',
            'enterprise_apply' => 'Enterprise Apply',
            'member_add' => 'Member Add',
            'access_level' => 'Access Level',
            'member_limit' => 'Member Limit',
            'broadcast_num' => 'Broadcast Num',
            'broadcast_available' => 'Broadcast Available',
            'group_level' => 'Group Level',
            'manage_num' => 'Manage Num',
            'created_time' => 'Created Time',
            'access_level_set' => 'Access Level Set',
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
        $criteria->compare('enterprise_id',$this->enterprise_id);
        $criteria->compare('enterprise_apply',$this->enterprise_apply);
        $criteria->compare('member_add',$this->member_add);
        $criteria->compare('access_level',$this->access_level);
        $criteria->compare('member_limit',$this->member_limit);
        $criteria->compare('broadcast_num',$this->broadcast_num);
        $criteria->compare('broadcast_available',$this->broadcast_available);
        $criteria->compare('group_level',$this->group_level);
        $criteria->compare('manage_num',$this->manage_num);
        $criteria->compare('created_time',$this->created_time);
        $criteria->compare('access_level_set',$this->access_level_set);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Enterprise the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
