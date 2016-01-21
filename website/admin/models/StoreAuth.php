<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/7
 * Time: 10:51
 */
class StoreAuth extends CActiveRecord{
    public $store_no;

    //表名
    public function tableName()
    {
        return 'store_auth';
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
            array('status,type,store_no', 'safe', 'on'=>'search'),
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
            'id' =>'ID',
            'store_no' =>'店铺号',
            'real_name' =>'真实姓名',
            'id_card' =>'身份证号',
            'poster_front' =>'身份证正面',
            'poster_back' =>'身份证反面',
            'poster_licence' =>'营业执照',
            'company' =>'公司名称',
            'member_id' =>'上传用户',
            'first_uptime' =>'第一次上传审核时间',
            'status' =>'认证状态，0.待审核，1.审核未通过，2.审核通过',
            'time' =>'提交时间',
            'type' =>'认证类型 1个人，2商家',

        );
    }
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria=new CDbCriteria;
        $criteria->compare('status',$this->status);
        $criteria->compare('type',$this->type);
        $criteria->compare('store_no',$this->store_no);


        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}