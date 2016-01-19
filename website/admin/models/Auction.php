<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/7
 * Time: 10:51
 */
class Auction extends CActiveRecord{
    public $place;//置顶区域
    public $industry;//行业
    public $start_time;//起拍时间
    public $end_time;//结束时间
    public $start_price;//起拍价
    public $price;//价格
    public $top_start_period;//置顶开始时间
    public $top_end_period;//置顶结束时间
    public $province;//省
    public $add_step;//最小加价
    public $is_close;//是否关闭
    public $is_paid;//是否付款
    //表名
    public function tableName()
    {
        return 'top_auction';
    }
    //验证规则
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('province, city, area,industry,start_time'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('province, city, area,price,industry,place,start_time,end_time,start_price,add_step,top_start_period,top_end_period,is_close,is_paid', 'safe', 'on'=>'search'),
        );
    }
    //未知
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }
    //绑定属性值
    public function attributeLabels()
    {
        return array(
            'place' =>'置顶区域',
            'start_time' =>'Start time',
            'end_time' =>'End time',
            'start_price' =>'起拍价',
            'top_start_period' =>'置顶开始时间',
            'top_end_period' =>'置顶结束时间',
            'province' => 'Province',
            'is_close' =>   'is_close',
            'is_paid'   =>'is_paid',
            'price' =>'成交价',
            'city' => 'City',
            'area' => 'Area',
            'industry' =>'Industry'
        );
    }
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria=new CDbCriteria;
        $criteria->compare('province',$this->province);
        $criteria->compare('city',$this->city);
        $criteria->compare('area',$this->area);
        $criteria->compare('industry',$this->industry);
        $criteria->compare('place',$this->place);
        $criteria->compare('start_time',$this->start_time);
        $criteria->compare('end_time',$this->end_time);
        $criteria->compare('price',$this->price);
        $criteria->compare('start_price',$this->start_price);
        $criteria->compare('top_start_period',$this->top_start_period);
        $criteria->compare('top_end_period',$this->top_end_period);
        $criteria->compare('is_close',$this->is_close);
        $criteria->compare('is_paid',$this->is_paid);
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}