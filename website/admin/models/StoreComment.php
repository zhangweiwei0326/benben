<?php

/**
 * This is the model class for table "store_comment".
 *
 * The followings are the available columns in table 'store_comment':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class StoreComment extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'store_comment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('comment_type,promotion_id,order_id,comment_rank,add_time,status,parent_id,member_id,is_seller', 'numerical', 'integerOnly'=>true),
            array('huanxin_username,user_name,content,ip_address', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('comment_id, comment_type,promotion_id, order_id,huanxin_username,user_name,content,comment_rank,add_time,ip_address,status,parent_id,member_id,is_seller', 'safe', 'on'=>'search'),
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
            'comment_id' => 'Comment Id',
            'comment_type' => 'Comment Type',
            'promotion_id' => 'Promotion Id',
            'huanxin_username' => 'Huanxin Username',
            'user_name'=>'User Name',
            'content'=>'Content',
            'comment_rank'=>'Comment Rank',
            'add_time'=>'Add Time',
            'ip_address'=>'Ip Address',
            'status'=>'Status',
            'parent_id'=>'Parent Id',
            'member_id'=>'Member Id',
            'order_id'=>'Order Id',
            'is_seller'=>'is Seller',
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

        $criteria->compare('comment_id',$this->comment_id);
        $criteria->compare('comment_type',$this->comment_type);
        $criteria->compare('promotion_id',$this->promotion_id);
        $criteria->compare('huanxin_username',$this->huanxin_username);
        $criteria->compare('user_name',$this->user_name);
        $criteria->compare('content',$this->content);
        $criteria->compare('comment_rank',$this->comment_rank);
        $criteria->compare('add_time',$this->add_time);
        $criteria->compare('ip_address',$this->ip_address);
        $criteria->compare('status',$this->status);
        $criteria->compare('parent_id',$this->parent_id);
        $criteria->compare('member_id',$this->member_id);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('is_seller',$this->is_seller);
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
