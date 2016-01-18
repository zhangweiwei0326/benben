<?php

/**
 * This is the model class for table "promotion".
 *
 * The followings are the available columns in table 'promotion':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class Promotion extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'promotion';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pm_id,valid_left,valid_right,is_close,vip_time,is_del,sellcount,type', 'numerical', 'integerOnly'=>true),
            array('name,poster_st,poster_nd,poster_rd,pay_ids,goods_sn,mustknow,model','length','max'=>255),
            array('description', 'safe'),
            array('origion_price,promotion_price,shipping_fee','numerical'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id,pm_id,mustknow,model,sellcount,is_del,valid_left,type, valid_right,name,poster_st,poster_nd,poster_rd,description,origion_price,promotion_price,is_close,vip_time,shipping_fee,pay_ids,goods_sn', 'safe', 'on'=>'search'),
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
            'pm_id' => 'Pm Id',
            'valid_left' => 'Valid Left',
            'valid_right' => 'Valid Right',
            'name'=>'Name',
            'poster_st'=>'Poster St',
            'poster_nd'=>'Poster Nd',
            'poster_rd'=>'Poster Rd',
            'description'=>'Description',
            'origion_price'=>'Origion Price',
            'promotion_price'=>'Promotion Price',
            'is_close'=>'Is Close',
            'vip_time'=>'VIP Time',
            'goods_sn'=>'Goods Sn',
            'pay_ids'=>'Pay Ids',
            'shipping_fee'=>'Shipping Fee',
            'mustknow'=>'Mustknow',
            'is_del'=>'Is Del',
            'sellcount'=>'Sellcount',
            'model'=>'Model',
            'type'=>'Type',
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
        $criteria->compare('pm_id',$this->pm_id);
        $criteria->compare('valid_left',$this->valid_left);
        $criteria->compare('valid_right',$this->valid_right);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('poster_st',$this->poster_st);
        $criteria->compare('poster_nd',$this->poster_nd);
        $criteria->compare('poster_rd',$this->poster_rd);
        $criteria->compare('description',$this->description);
        $criteria->compare('origion_price',$this->origion_price);
        $criteria->compare('promotion_price',$this->promotion_price);
        $criteria->compare('is_close',$this->is_close);
        $criteria->compare('vip_time',$this->vip_time);
        $criteria->compare('goods_sn',$this->goods_sn);
        $criteria->compare('pay_ids',$this->pay_ids);
        $criteria->compare('shipping_fee',$this->shipping_fee);
        $criteria->compare('mustknow',$this->mustknow);
        $criteria->compare('is_del',$this->is_del);
        $criteria->compare('sellcount',$this->sellcount);
        $criteria->compare('model',$this->model);
        $criteria->compare('type',$this->type);

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
