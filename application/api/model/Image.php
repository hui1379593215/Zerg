<?php

namespace app\api\model;



class Image extends BaseModel
{
    //隐藏字段
    protected $hidden=['id','from','delete_time','update_time'];

    public function getUrlAttr($value,$data){

        return $this->prefixImgUrl($value,$data);
    }




}
