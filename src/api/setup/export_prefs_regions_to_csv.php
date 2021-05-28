<?php
function create_formatted_array($stores) {
    $prefs_temp = ['北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県', '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県', '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'];

    $region_regex = '/(東京都|北海道|(?:京都|大阪)府|.{6,9}県)?((?:四日市|廿日市|野々市|臼杵|かすみがうら|つくばみらい|いちき串木野)市|(?:杵島郡大町|余市郡余市|高市郡高取)町|.{3,12}市.{3,12}区|.{3,9}区|.{3,15}市(?=.*市)|.{3,15}市|.{6,27}町(?=.*町)|.{6,27}町|.{9,24}村(?=.*村)|.{9,24}村)(.*)/';

    $new_stores = array(['category', 'company_name', 'store_name', 'postal_code', 'prefectures', 'address', 'tel', 'pref_code', 'region_code']);
    $prefs = array(['pref_code', 'pref_name']);
    $pref_code = null;
    $regions = array(['region_code', 'pref_code', 'region_name']);
    $region_code = 0;
    $incorrect_prefs = array();
    $incorrect_regions = array();

    function replace_br_to_space($value) {
        $text = str_replace(["\r", "\n"], ' ', $value);
        return $text;
    }

    $count = 0;
    foreach ($stores as $_ => $array) {
        $new_stores[] = array_map('replace_br_to_space', $array);
        $count++;
        foreach ($array as $key => $value) {

            if ($key === '都道府県') {
                $pref_code = array_search($value, $prefs_temp) > -1
                    ? array_search($value, $prefs_temp) + 1
                    : null;
                if ($pref_code === null) {
                    $incorrect_prefs[] =  $value . ' line on ' . (string)($count + 1);
                    continue;
                }

                // 元データにpref_codeを追加
                $new_stores[$count]['pref_code'] = $pref_code;

                if (!in_array(array($pref_code, $value), $prefs)) {
                    $prefs[] = [$pref_code, $value];
                }
            }
            if ($key === '住所' && $pref_code) {
                // 半角と全角を空文字に変換
                $address = str_replace(array(' ', '　'), '', $value);
                preg_match($region_regex, $address, $matches);
                $pref = $matches[1];
                $region = $matches[2];

                if (!$pref || !$region) {
                    $incorrect_regions[] = $value . ' line on ' . (string)($count + 1);
                }

                if (in_array($region, array_column($regions, 2))) {
                    $deprecated_region_code = array_search($region, array_column($regions, 2));
                    $new_stores[$count]['region_code'] = $deprecated_region_code;
                } else {
                    $region_code++;
                    $regions[] = [$region_code, $pref_code, $region];
                    // 元データにregion_codeを追加
                    $new_stores[$count]['region_code'] = $region_code;
                }
            }
        }
    }
    // sort by pref_code
    usort($prefs, function($a, $b) {
        return $a[0] <=> $b[0];
    });

    // var_dump($regions);

    foreach ($incorrect_prefs as $incorrect_pref) {
        echo 'Warning: exists incorrect prefectures that is ' . $incorrect_pref . '.' . PHP_EOL;
    }
    foreach ($incorrect_regions as $incorrect_region) {
        echo 'Warning: exists incorrect region that is ' . $incorrect_region . '.' . PHP_EOL;
    }

    return array(
        'new_stores' => $new_stores,
        'prefs' => $prefs,
        'regions' => $regions
    );
}

// php function to convert csv to json format
function export_pref_csv($file_name, $output_dir) {
    // open csv file
    if (!($fp = fopen($file_name, 'r'))) {
        die("Can't open file...");
    }

    $max_char_length = "1024";

    //read csv headers
    $key = fgetcsv($fp, $max_char_length, ",");

    // parse csv rows into array
    $stores = array();
        while ($row = fgetcsv($fp, $max_char_length, ",")) {
        $stores[] = array_combine($key, $row);
    }

    $result = create_formatted_array($stores);

    fclose($fp);

    // export new stores to csv file
    $fp = fopen($output_dir . '/new_stores.csv', 'w');
    foreach ($result['new_stores'] as $index => $fields) {
        if ($index && !array_key_exists('pref_code', $fields)) {
            echo "\nWarning: not exists pref_code key in this array on line " . (string)($index + 1) . '.' . PHP_EOL;
            var_dump($fields);
        }
        if ($index && !array_key_exists('region_code', $fields)) {
            echo "\nWarning: not exists region_code key in this array on line " . (string)($index + 1) . '.' . PHP_EOL;
            var_dump($fields);
        }
        fputcsv($fp, $fields);
    }
    fclose($fp);

    // export prefs to csv file
    $fp = fopen($output_dir . '/prefs.csv', 'w');
    foreach ($result['prefs'] as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);

    // export regions to csv file
    $fp = fopen($output_dir . '/regions.csv', 'w');
    foreach ($result['regions'] as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
}

if($argc != 2){
    echo 'ファイル名を指定してください';
    exit();
}

$file_name = $_SERVER['argv'][1];
$output_dir = dirname($file_name);
export_pref_csv($file_name, $output_dir);
