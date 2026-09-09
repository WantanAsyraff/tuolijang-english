<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * System-owned Malaysian regions used by the existing address cascaders.
 *
 * User-entered street addresses and stored historical addresses deliberately do
 * not appear here and must never be translated or normalized.
 */
final class MalaysiaAddressCatalog
{
    private const COUNTRY_ID = 900000000;

    /**
     * Return a stable, three-level hierarchy compatible with system_city and
     * the area_cascade dictionary: Malaysia -> state/territory -> city/town.
     *
     * @return array<int, array{city_id: int, parent_city_id: int, level: int, name: string}>
     */
    public static function rows(): array
    {
        $states = [
            'Johor'           => ['Johor Bahru', 'Batu Pahat', 'Muar', 'Kluang'],
            'Kedah'           => ['Alor Setar', 'Sungai Petani', 'Kulim', 'Langkawi'],
            'Kelantan'        => ['Kota Bharu', 'Pasir Mas', 'Gua Musang'],
            'Melaka'          => ['Melaka City', 'Alor Gajah', 'Jasin'],
            'Negeri Sembilan' => ['Seremban', 'Port Dickson', 'Kuala Pilah'],
            'Pahang'          => ['Kuantan', 'Temerloh', 'Bentong'],
            'Penang'          => ['George Town', 'Butterworth', 'Bayan Lepas'],
            'Perak'           => ['Ipoh', 'Taiping', 'Kuala Kangsar'],
            'Perlis'          => ['Kangar'],
            'Sabah'           => ['Kota Kinabalu', 'Sandakan', 'Tawau', 'Lahad Datu'],
            'Sarawak'         => ['Kuching', 'Sibu', 'Miri', 'Bintulu', 'Sri Aman'],
            'Selangor'        => ['Shah Alam', 'Petaling Jaya', 'Klang', 'Kajang'],
            'Terengganu'      => ['Kuala Terengganu', 'Dungun', 'Kemaman'],
            'Kuala Lumpur'    => ['Kuala Lumpur'],
            'Labuan'          => ['Labuan'],
            'Putrajaya'       => ['Putrajaya'],
        ];

        $rows = [[
            'city_id'        => self::COUNTRY_ID,
            'parent_city_id' => 0,
            'level'          => 0,
            'name'           => 'Malaysia',
        ]];

        $stateId = self::COUNTRY_ID + 1;
        $townId  = self::COUNTRY_ID + 101;
        foreach ($states as $state => $towns) {
            $currentStateId = $stateId++;
            $rows[]         = [
                'city_id'        => $currentStateId,
                'parent_city_id' => self::COUNTRY_ID,
                'level'          => 1,
                'name'           => $state,
            ];
            foreach ($towns as $town) {
                $rows[] = [
                    'city_id'        => $townId++,
                    'parent_city_id' => $currentStateId,
                    'level'          => 2,
                    'name'           => $town,
                ];
            }
        }

        return $rows;
    }

    /**
     * Add the catalog to the existing region and dictionary sources without
     * overwriting historical China data, custom geography, or user addresses.
     */
    public static function sync(): void
    {
        $rows    = self::rows();
        $cityIds = [];

        foreach ($rows as $row) {
            $cityIds[$row['city_id']] = DB::table('system_city')
                ->where('city_id', $row['city_id'])
                ->value('id');

            if (! $cityIds[$row['city_id']]) {
                $cityIds[$row['city_id']] = DB::table('system_city')->insertGetId([
                    'city_id'     => $row['city_id'],
                    'level'       => $row['level'],
                    'parent_id'   => 0,
                    'area_code'   => '',
                    'name'        => $row['name'],
                    'merger_name' => $row['name'],
                    'lng'         => '0',
                    'lat'         => '0',
                    'is_show'     => 1,
                ]);
            }
        }

        foreach ($rows as $row) {
            $parentId = $row['parent_city_id'] ? $cityIds[$row['parent_city_id']] : 0;
            DB::table('system_city')
                ->where('city_id', $row['city_id'])
                ->update(['parent_id' => $parentId, 'is_show' => 1]);
        }

        $typeId = DB::table('dict_type')->where('ident', 'area_cascade')->value('id');
        if (! $typeId) {
            $typeId = DB::table('dict_type')->insertGetId([
                'name'       => '地区级联',
                'ident'      => 'area_cascade',
                'link_type'  => 'custom',
                'level'      => 4,
                'status'     => 1,
                'is_default' => 1,
                'mark'       => '',
                'crud_id'    => 0,
                'field_id'   => 0,
            ]);
        }

        foreach ($rows as $row) {
            $value = (string) $cityIds[$row['city_id']];
            if (DB::table('dict_data')->where('type_name', 'area_cascade')->where('value', $value)->exists()) {
                continue;
            }
            DB::table('dict_data')->insert([
                'name'       => $row['name'],
                'value'      => $value,
                'pid'        => (string) ($row['parent_city_id'] ? $cityIds[$row['parent_city_id']] : 0),
                'type_id'    => $typeId,
                'type_name'  => 'area_cascade',
                'level'      => $row['level'] + 1,
                'sort'       => 0,
                'color'      => '',
                'status'     => 1,
                'is_default' => 1,
                'mark'       => '',
            ]);
        }
    }
}