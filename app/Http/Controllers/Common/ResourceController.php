<?php

namespace App\Http\Controllers\Common;


use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class ResourceController
{
    /**
     * showdoc
     * @catalog 前后端接口/资源
     * @title 获取用户的资源
     * @description -
     * @method get
     * @url https://{url}/user/get-resource
     * @return {"id":26,"userId":26,"people":200,"peopleChip":0,"peopleOutput":0,"food":3000,"foodChip":0,"foodOutput":0,"wood":2000,"woodChip":0,"woodOutput":0,"stone":1000,"stoneChip":0,"stoneOutput":0,"money":1322770,"moneyChip":0,"moneyOutput":0,"area":200,"areaChip":0,"areaOutput":0,"created_at":"2018-12-22 12:45:57","updated_at":"2018-12-24 01:24:47"}
     * @number 10
     */
    public function getMeResource()
    {
        $info = Resource::where('userId', Auth::id())->first();

        return $info;
    }
}
