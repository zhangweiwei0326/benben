<?php

/**
 * This is the model class for table "friend".
 *
 * The followings are the available columns in table 'friend':
 * @property integer $id
 * @property string $description
 * @property integer $member_id
 * @property integer $status
 * @property integer $type
 * @property integer $created_time
 * @property integer $views
 * @property integer $goods
 * @property integer $is_delete
 */
class FriendApplyLog extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'friend_apply_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('group_id,created_time', 'numerical', 'integerOnly'=>true),
            array('from_huanxin,to_huanxin,name', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, group_id, from_huanxin, to_huanxin, name, created_time', 'safe', 'on'=>'search'),
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
            'group_id' => 'Group Id',
            'from_huanxin' => 'From Huanxin',
            'to_huanxin' => 'To Huanxin',
            'name' => 'Name',
            'created_time'=>'Created Time',
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
        $criteria->compare('group_id',$this->group_id,true);
        $criteria->compare('from_huanxin',$this->from_huanxin);
        $criteria->compare('to_huanxin',$this->to_huanxin);
        $criteria->compare('name',$this->name);
        $criteria->compare('created_time',$this->created_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Friend the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
