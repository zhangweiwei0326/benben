<?php

/**
 * This is the model class for table "broadcasting_log".
 *
 * The followings are the available columns in table 'broadcasting_log':
 * @property integer $id
 * @property integer $member_id
 * @property integer $league_id
 * @property string $friend_id
 * @property integer $receive_count
 * @property string $description
 * @property integer $created_time
 * @property string $content
 * @property integer $is_del
 */
class BroadcastingAttachment extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'broadcasting_attachment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('broadcast_id, type', 'numerical', 'integerOnly'=>true),
            array('attachment', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, broadcast_id, type, attachment', 'safe', 'on'=>'search'),
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
            'broadcast_id' => 'Broadcast Id',
            'type' => 'Type',
            'attachment' => 'Attachment',
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
        $criteria->compare('broadcast_id',$this->broadcast_id);
        $criteria->compare('type',$this->type);
        $criteria->compare('attachment',$this->attachment,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BroadcastingLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
