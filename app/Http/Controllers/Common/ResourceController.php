<?php

namespace App\Http\Controllers\Common;


use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class ResourceController
{
    /**
     * showdoc
     * @catalog 前后端接口/资源
     * @title [获取]用户的资源
     * @description -
     * @method get
     * @url https://{url}/user/get-resource
     * @return {"id":26,"userId":26,"people":200,"peopleChip":0,"peopleOutput":0,"food":3000,"foodChip":0,"foodOutput":0,"wood":2000,"woodChip":0,"woodOutput":0,"stone":1000,"stoneChip":0,"stoneOutput":0,"money":1322770,"moneyChip":0,"moneyOutput":0,"area":200,"areaChip":0,"areaOutput":0,"created_at":"2018-12-22 12:45:57","updated_at":"2018-12-24 01:24:47"}
     * @return_param id int 资源ID
     * @return_param userId int 用户ID
     * @return_param people int 人口数量（整数）
     * @return_param peopleChip int 人口数量（小数）
     * @return_param peopleOutput int 人口增长（小时）
     * @return_param wood int 木材数量（整数）
     * @return_param woodChip int 木材数量（小数）
     * @return_param woodOutput int 木材增长（小时）
     * @return_param stone int 石料数量（整数）
     * @return_param stoneChip int 石料数量（小数）
     * @return_param stoneOutput int 石料增长（小时）
     * @return_param money int 货币数量（整数）
     * @return_param moneyChip int 货币数量（小数）
     * @return_param moneyOutput int 货币增长（小时）
     * @return_param area int 土地面积（整数）
     * @return_param areaChip int 土地面积（小数）
     * @return_param areaOutput int 土地面积增长（永远为0）
     * @return_param created_at int 创建时间
     * @return_param updated_at int 更新时间
     * @number 10
     */
    public function getMeResource()
    {
        $info = Resource::where('userId', Auth::id())->first();

        return $info;
    }
}
