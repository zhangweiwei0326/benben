<?php

/**
 * This is the model class for table "store_order_goods".
 *
 * The followings are the available columns in table 'store_order_goods':
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property integer $created_time
 */
class StoreOrderGoods extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'store_order_goods';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id,promotion_id,product_id,goods_number,is_real,parent_id,is_gift,goods_attr_id', 'numerical', 'integerOnly'=>true),
            array('origion_price,promotion_price', 'numerical'),
            array('goods_name,goods_sn,goods_attr,extension_code', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('rec_id, order_id,promotion_id, goods_name,goods_sn,product_id,goods_number,origion_price,promotion_price,goods_attr,is_real,extension_code,parent_id,is_gift,goods_attr_id', 'safe', 'on'=>'search'),
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
            'rec_id' => 'Rec Id',
            'order_id' => 'Order Id',
            'promotion_id' => 'Promotion Id',
            'goods_name' => 'Goods Name',
            'goods_sn'=>'Goods Sn',
            'product_id'=>'Product Id',
            'goods_number'=>'Goods Number',
            'origion_price'=>'Origion Price',
            'Promotion_price'=>'Promotion Price',
            'goods_attr'=>'Goods Attr',
            'is_real'=>'Is Real',
            'extension_code'=>'Extension Code',
            'parent_id'=>'Parent Id',
            'is_gift'=>'Is Gift',
            'goods_attr_id'=>'Goods Attr Id',
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

        $criteria->compare('rec_id',$this->rec_id);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('promotion_id',$this->promotion_id);
        $criteria->compare('goods_name',$this->goods_name);
        $criteria->compare('goods_sn',$this->goods_sn);
        $criteria->compare('product_id',$this->product_id);
        $criteria->compare('goods_number',$this->goods_number);
        $criteria->compare('origion_price',$this->origion_price);
        $criteria->compare('Promotion_price',$this->Promotion_price);
        $criteria->compare('goods_attr',$this->goods_attr);
        $criteria->compare('is_real',$this->is_real);
        $criteria->compare('extension_code',$this->extension_code);
        $criteria->compare('parent_id',$this->parent_id);
        $criteria->compare('is_gift',$this->is_gift);
        $criteria->compare('goods_attr_id',$this->goods_attr_id);

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
