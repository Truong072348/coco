<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Districts extends Model
{
    protected $fillable = ['ProvinceName', 'ProvinceID', 'ProvinceID', 'DistrictName', 'DistrictCode', 'DistrictCode', 'DistrictID','WardName','Code','WardCode'];

    //Table name
    protected $table = 'districts';

    //Primary key
    protected $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;
}
