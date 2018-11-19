<?php
//iTunesライブラリを読み込む
//if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
//    @$musicLib = simplexml_load_file('D:/Documents/Music Files/iTunes/iTunes Library.xml');
//}
if (@!$musicLib) {
    $musicLib = simplexml_load_file('./uploads/iTunes Library.xml');
}
if (!$musicLib) {
    $error_msg = 'iTunesライブラリファイルが見つかりません。';
    return;
}
//echo'<pre>';print_r($musicLib);echo'</pre>';exit;



//楽曲のkeyを取得して
$musicKey = $musicLib->dict->dict->key;
$musicKey = json_decode(json_encode($musicKey), true);

//楽曲データを整形
$musicAll = array();
foreach ($musicKey as $key => $val) {
    $data = $musicLib->dict->dict->dict[$key];
    $data = json_decode(json_encode($data), true);
//    $musicAll[$key] = $data;
    
    //項目のkeyを取得して
    $propKey = $data['key'];
    
    //項目データを整形
    $propInteger = @$data['integer'];
    $propString = @$data['string'];
    $propDate = @$data['date'];
//    $propTrue = @$data['true'];
    $iInteger = 0;
    $iString = 0;
    $iDate = 0;
//    $iTrue = 0;
    //id
    if (in_array('Track ID', $propKey)) {
//        $musicAll[$key]['id'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //名前
    if (in_array('Name', $propKey)) {
        $musicAll[$key]['name'] = @$propString[$iString];
        $iString++;
    }
    //アーティスト
    if (in_array('Artist', $propKey)) {
        $musicAll[$key]['artist'] = @$propString[$iString];
        $iString++;
    }
    //アルバムアーティスト
    if (in_array('Album Artist', $propKey)) {
        $musicAll[$key]['album_artist'] = @$propString[$iString];
        $iString++;
    }
    //作曲者
    if (in_array('Composer', $propKey)) {
        $musicAll[$key]['composer'] = @$propString[$iString];
        $iString++;
    }
    //アルバム
    if (in_array('Album', $propKey)) {
        $musicAll[$key]['album'] = @$propString[$iString];
        $iString++;
    }
    //グループ
    if (in_array('Grouping', $propKey)) {
//        $musicAll[$key]['group'] = @$propString[$iString];
        $iString++;
    }
    //ジャンル
    if (in_array('Genre', $propKey)) {
        $musicAll[$key]['genre'] = @$propString[$iString];
        $iString++;
    }
    //種類
    if (in_array('Kind', $propKey)) {
//        $musicAll[$key]['kind'] = @$propString[$iString];
        $iString++;
    }
    //サイズ
    if (in_array('Size', $propKey)) {
//        $musicAll[$key]['size'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //再生時間
    if (in_array('Total Time', $propKey)) {
//        $musicAll[$key]['total_time'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //開始時間
    if (in_array('Start Time', $propKey)) {
//        $musicAll[$key]['start_time'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //停止時間
    if (in_array('Stop Time', $propKey)) {
//        $musicAll[$key]['stop_time'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //ディスク番号
    if (in_array('Disc Number', $propKey)) {
        $musicAll[$key]['disc_number'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //ディスク数
    if (in_array('Disc Count', $propKey)) {
        $musicAll[$key]['disc_count'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //トラック番号
    if (in_array('Track Number', $propKey)) {
        $musicAll[$key]['track_number'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //トラック数
    if (in_array('Track Count', $propKey)) {
        $musicAll[$key]['track_count'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //年
    if (in_array('Year', $propKey)) {
        $musicAll[$key]['year'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //BPM
    if (in_array('BPM', $propKey)) {
//        $musicAll[$key]['bpm'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //変更日
    if (in_array('Date Modified', $propKey)) {
//        $musicAll[$key]['modified'] = @$propDate[$iDate];
        $iDate++;
    }
    //追加日
    if (in_array('Date Added', $propKey)) {
//        $musicAll[$key]['created'] = @$propDate[$iDate];
        $iDate++;
    }
    //ビットレート
    if (in_array('Bit Rate', $propKey)) {
//        $musicAll[$key]['bit_rate'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //サンプルレート
    if (in_array('Sample Rate', $propKey)) {
//        $musicAll[$key]['sample_rate'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //音量調整
    if (in_array('Volume Adjustment', $propKey)) {
//        $musicAll[$key]['volume'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //コメント
    if (in_array('Comments', $propKey)) {
        $musicAll[$key]['comments'] = @$propString[$iString];
        $iString++;
    }
    //再生回数
    if (in_array('Play Count', $propKey)) {
        $musicAll[$key]['play_count'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //最後に再生した日
    if (in_array('Play Date', $propKey)) {
//        $musicAll[$key]['play_date'] = @$propDate[$iDate];
        $iDate++;
    }
    //最後に再生した標準日
    if (in_array('Play Date UTC', $propKey)) {
//        $musicAll[$key]['play_utc'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //スキップ回数
    if (in_array('Skip Count', $propKey)) {
//        $musicAll[$key]['skip_count'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //最後にスキップした日
    if (in_array('Skip Date', $propKey)) {
//        $musicAll[$key]['skip_date'] = @$propDate[$iDate];
        $iDate++;
    }
    //レート
    if (in_array('Rating', $propKey)) {
        $musicAll[$key]['rate'] = @$propInteger[$iInteger];
        $iInteger++;
    }
    //Album Rating
    //Album Rating Computed
    //Compilation
    //Artwork Count
    //アルバム読み
    if (in_array('Sort Album', $propKey)) {
        $musicAll[$key]['album_kana'] = @$propString[$iString];
        $iString++;
    }
    //アルバムアーティスト読み
    if (in_array('Sort Album Artist', $propKey)) {
        $musicAll[$key]['album_artist_kana'] = @$propString[$iString];
        $iString++;
    }
    //アーティスト読み
    if (in_array('Sort Artist', $propKey)) {
        $musicAll[$key]['artist_kana'] = @$propString[$iString];
        $iString++;
    }
    //作曲者読み
    if (in_array('Sort Composer', $propKey)) {
        $musicAll[$key]['composer_kana'] = @$propString[$iString];
        $iString++;
    }
    //名前読み
    if (in_array('Sort Name', $propKey)) {
        $musicAll[$key]['kana'] = @$propString[$iString];
        $iString++;
    }
    //Persistent ID
    //再生チェック, true or falseなので
    if (in_array('Disabled', $propKey)) {
        $musicAll[$key]['disabled'] = true;
    } else {
        $musicAll[$key]['disabled'] = false;
    }
    //Track Type
    //Location
    //File Folder Count
    //Library Folder Count
}



//レートの整形
foreach ($musicAll as $key => $val) {
    //レートが設定されていないデータは除外する
    if (@!$val['rate']) {
        unset($musicAll[$key]);
    //レート：★
    } elseif ($val['rate'] <= 20) {
//        $musicAll[$key]['rate'] = 1;
        unset($musicAll[$key]);
    //レート：★★
    } elseif ($val['rate'] == 40) {
        $musicAll[$key]['rate'] = 2;
    //レート：★★★
    } elseif ($val['rate'] == 60) {
        $musicAll[$key]['rate'] = 3;
    //レート：★★★★
    } elseif ($val['rate'] == 80) {
        $musicAll[$key]['rate'] = 4;
    //レート：★★★★★
    } elseif ($val['rate'] == 100) {
        $musicAll[$key]['rate'] = 5;
    //レートの値がおかしいデータは除外する
    } else {
        unset($musicAll[$key]);
    }
}
//並び替え
$musicAll = sort_imusic($musicAll);
//楽曲の被りを削除
$tmp = [];
$musicUnique = [];
foreach ($musicAll as $key => $val) {
    //再生チェックがあれば、そのままデータを保持
    if ($val['disabled'] == false) {
        $tmp[] = $val['name'];
        $musicUnique[$key] = $val;
    //再生チェックがなければ、レートが2以下のみ考慮
    } elseif ($val['rate'] <= 2) {
        //楽曲名に被りがなければ、そのままデータを保持
        if (!in_array($val['name'], $tmp)) {
            $tmp[] = $val['name'];
            $musicUnique[$key] = $val;
        //既に同じ楽曲名が存在する場合
        } else {
            //楽曲名とアーティスト名の両方が被っていないかをチェックして
            $duplicated = false;
            foreach ($musicUnique as $key2 => $val2) {
                if ($val2['name'] == $val['name'] && $val2['artist'] == $val['artist']) {
                    $duplicated = true;
                }
            }
            //被りがなければ、データを保持
            if (!$duplicated) {
                $musicUnique[$key] = $val;
            }
        }
    }
}
$musicAll = $musicUnique;



//keyを振り直す
$musicAll = array_merge($musicAll);

//idを振っておく
$id = 1;
foreach ($musicAll as $key => $val) {
    $musicAll[$key] = array('id' => $id) + $val;
    $id++;
}



//echo'<pre>';print_r($musicAll);echo'</pre>';
