<?php

/**
 * This is the model class for table "goods_gallery".
 *
 * The followings are the available columns in table 'goods_gallery':
 * @property integer $id
 * @property integer $contact_id
 * @property integer $member_id
 * @property string $short_phone
 * @property string $remark_name
 * @property integer $created_time
 * @property string $phone
 * @property string $name
 */
class GoodsGallery extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'goods_gallery';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('goods_id', 'required'),
            array('goods_id', 'numerical', 'integerOnly'=>true),
            array('img_url,img_desc', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('img_id, goods_id, img_url, img_desc', 'safe', 'on'=>'search'),
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
            'img_id' => 'Img Id',
            'goods_id' => 'Goods Id',
            'img_url' => 'Img Url',
            'img_desc' => 'Img Desc',
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

        $criteria->compare('img_id',$this->img_id);
        $criteria->compare('goods_id',$this->goods_id);
        $criteria->compare('img_url',$this->img_url);
        $criteria->compare('img_desc',$this->img_desc);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EnterpriseMember the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
