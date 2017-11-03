<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/22
 * Time: 下午5:08
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionModel extends Model
{

    protected $table = 'region';

    public static function getRegionNameByRegionId($regionId)
    {
        return '浙江省建安市';
    }

    public static function getRegions()
    {
        return self
            ::where('parent_id', '>', 0)
            ->select(['region_id', 'region_id as value', 'region_name as label', 'parent_id'])
            ->get()
            ->toArray();
    }

    public static function getFormatRegions()
    {
        $regions = self::getRegions();

        $_regions = [];

        // 第一步
        foreach ($regions as $region) {
            $_regions[$region['parent_id']][] = $region;
        }

        unset($region);

        // 第二步
        foreach($_regions[1] as &$region) {
            $region['children'] = $_regions[$region['region_id']];
        }

        unset($region);

        function getRegion(&$childrenRegion, $_regions) {
            foreach ($childrenRegion as $key => $children) {
                $regionId = $children['region_id'];
                if (isset($_regions[$regionId]))
                    $childrenRegion[$key]['children'] = $_regions[$regionId];
            }
        }

        // 第三步
        foreach($_regions[1] as &$region) {
            getRegion($region['children'], $_regions);
        }

        unset($region);
        $region = $_regions[1];

        unset($regions, $_regions);

        return $region;
    }


}