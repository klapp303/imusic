<?php

/**
 * iTunes Library.xml から統計データを作る
 * iTunes 11.3.0.54 にて動作確認済み
 */

function get_imusic_report($musicData)
{
    $report = search_prop_imusic($musicData, 'genre');
    
    return $report;
}



/**
 * 任意の楽曲データのみ検索して出力する関数
 * $musicData：楽曲データ、$condition1,2,3：検索条件 array('項目' => '値')
 * 各conditions内は∧、conditions間は∨
 */

function search_word_imusic($musicData = false, $conditions = array(), $conditions2 = array(), $conditions3 = array())
{
    //条件がなければそのままreturn
    if (!$conditions) {
        return $musicData;
    }
    
    //楽曲データを保持しておく
    $musicAll = $musicData;
    
    //第一条件
    foreach ($conditions as $prop => $value) {
        foreach ($musicData as $key => $music) {
            //条件に合致しないデータは除外
            if (@$music[$prop] != $value) {
                unset($musicData[$key]);
            }
        }
    }
    $musicData1 = $musicData;
    
    //第二条件
    $musicData = $musicAll;
    if ($conditions2) {
        foreach ($conditions2 as $prop => $value) {
            foreach ($musicData as $key => $music) {
                //条件に合致しないデータは除外
                if (@$music[$prop] != $value) {
                    unset($musicData[$key]);
                }
            }
        }
        $musicData2 = $musicData;
    } else {
        $musicData2 = array();
    }
    
    //第三条件
    $musicData = $musicAll;
    if ($conditions3) {
        foreach ($conditions3 as $prop => $value) {
            foreach ($musicData as $key => $music) {
                //条件に合致しないデータは除外
                if (@$music[$prop] != $value) {
                    unset($musicData[$key]);
                }
            }
        }
        $musicData3 = $musicData;
    } else {
        $musicData3 = array();
    }
    
    //それぞれの検索結果を加算
    $musicData = $musicData1 + $musicData2 + $musicData3;
    //keyを振り直す
    $musicData = array_merge($musicData);
    
//    echo'<pre>';print_r($musicData);echo'</pre>';exit;
    return $musicData;
}



/**
 * 楽曲データを項目毎に並び替えて出力する関数
 * $musicData：楽曲データ、$prop：項目、$max：対象とする最大順位、$min：対象とする最小曲数、$sortProp : ソートする数値
 * 返り値は array(['項目1'] => $musicData1, ['項目2'] => $musicData2...)、デフォルトソートは楽曲数の降順
 */

function search_prop_imusic($musicData = false, $prop = false, $max = false, $min = false, $sortProp = 'count')
{
    //項目がなければそのままreturn
    if (!$prop) {
        return $musicData;
    }
    
    //楽曲データを保持しておく
    $musicAll = $musicData;
    
    //項目毎に並び替える
    $musicDataProp = array();
    while ($musicData) {
        $countData = count($musicData);
        $i = 0;
        foreach ($musicData as $key => $val) {
            //項目を見つけて
            if (@$val[$prop]) {
                $value = $val[$prop];
                //その項目と同じ楽曲データを抽出
                $musicDataPropAdd = search_word_imusic($musicData, array($prop => $value));
                //countを取得
                $count = count($musicDataPropAdd);
                //rate平均、再生回数を取得
                $rate = 0;
		$play_count = 0;
                foreach ($musicDataPropAdd as $key2 => $val2) {
                    $rate += $val2['rate'];
		    $play_count += @$val2['play_count'];
                }
                $rate = round($rate / $count, 3);
                $musicDataPropAdd = array('count' => $count) + array('rate' => $rate) + array('play_count' => $play_count) + $musicDataPropAdd;
                $musicDataProp = $musicDataProp + array($value => $musicDataPropAdd);
                //抽出したものは元の楽曲データから除外
                foreach ($musicData as $key2 => $val2) {
                    if (@$val2[$prop] == $value) {
                        unset($musicData[$key2]);
                    }
                }
                //keyを振り直す
                $musicData = array_merge($musicData);
                break;
                
            } else {
                $i++;
            }
            
            //項目が見つからなければ残りの楽曲データをまとめてbreak
            if ($i >= $countData) {
                //countを取得
                $count = count($musicData);
                //rate平均、再生回数を取得
                $rate = 0;
		$play_count = 0;
                foreach ($musicData as $key2 => $val2) {
                    $rate += $val2['rate'];
		    $play_count += @$val2['play_count'];
                }
                $rate = round($rate / $count, 3);
                $musicData = array('count2' => $count) + array('rate' => $rate) + array('play_count' => $play_count) + $musicData; //count2は楽曲数順で並び替えるため
                $musicDataProp = $musicDataProp + array('不明' => $musicData);
                $musicData = array();
                break;
            }
        }
    }
    
    //ソートする数値の降順にする
    if ($sortProp == 'play_count') {
        foreach ($musicDataProp as $key => $val) {
            if (@!$val['play_count']) {
                $val['play_count'] = 0;
            }
            $sort[$key] = $val['play_count'];
        }
        array_multisort($sort, SORT_DESC, $musicDataProp);
    } else {
        foreach ($musicDataProp as $key => $val) {
            if (@!$val['count']) {
                $val['count'] = 0;
            }
            $sort[$key] = $val['count'];
        }
        array_multisort($sort, SORT_DESC, $musicDataProp);
    }
    //まとめた残りの楽曲データのcountを揃えておく
    foreach ($musicDataProp as $key => $val) {
        if (@$val['count2']) {
            $musicDataProp[$key] = array('count' => $val['count2']) + $val;
            unset($musicDataProp[$key]['count2']);
        }
    }
    
    //項目毎の楽曲数の下限がある場合
    if ($min) {
        foreach ($musicDataProp as $key => $val) {
            if ($val['count'] < $min && $val['play_count'] < $min) { //再生数が少ない曲のみの制限も加えておく
                unset($musicDataProp[$key]);
            }
        }
    }
    
    //項目数の上限がある場合
    if ($max) {
        $count = count($musicDataProp);
        $i = 0;
        foreach ($musicDataProp as $key => $val) {
            if ($i >= $max || $i >= $count) {
                unset($musicDataProp[$key]);
            }
            $i++;
        }
    }
    
    return $musicDataProp;
}



/**
 * 項目毎の楽曲データをレート順に並び替える関数
 */

function sort_imusic_rate($musicData = false)
{
    foreach ($musicData as $key => $val) {
        if (@!$val['rate']) {
            $val['rate'] = 0;
        }
        $sort[$key] = $val['rate'];
    }
    array_multisort($sort, SORT_DESC, $musicData);
    
    return $musicData;
}



/**
 * 項目毎の楽曲データを再生数順に並び替える関数
 */

function sort_imusic_play_count($musicData = false)
{
    foreach ($musicData as $key => $val) {
        if (@!$val['play_count']) {
            $val['play_count'] = 0;
        }
        $sort[$key] = $val['play_count'];
    }
    array_multisort($sort, SORT_DESC, $musicData);
    
    return $musicData;
}



/**
 * 項目毎の楽曲データをジャンル順に並び替える関数
 */

function sort_imusic_genre($musicData = false, $sort = array('アニメ', 'ゲーム', '声優', '同人', 'その他'))
{
    foreach ($sort as $genre) {
        $sortData[$genre] = @$musicData[$genre];
        unset($musicData[$genre]);
	if (!$sortData[$genre]) {
            unset($sortData[$genre]);
        }
    }
    $musicData = $sortData + $musicData;
    
    return $musicData;
}



/**
 * 楽曲データの並び替え関数
 * アルバムアーティスト名 > アーティスト名 > 年 > アルバム名 > トラック番号 > ディスク番号の昇順
 */

function sort_imusic($musicData = false)
{
    //ディスク番号
    foreach ($musicData as $key => $val) {
        if (@!$val['disc_number']) {
            $val['disc_number'] = 0;
        }
        $sort[$key] = $val['disc_number'];
    }
    array_multisort($sort, SORT_ASC, $musicData);
    //トラック番号
    foreach ($musicData as $key => $val) {
        if (@!$val['track_number']) {
            $val['track_number'] = 0;
        }
        $sort[$key] = $val['track_number'];
    }
    array_multisort($sort, SORT_ASC, $musicData);
    //アルバム名
    foreach ($musicData as $key => $val) {
        if (@!$val['album_kana']) {
            $val['album_kana'] = '';
        }
        $sort[$key] = $val['album_kana'];
    }
    //年
    foreach ($musicData as $key => $val) {
        if (@!$val['year']) {
            $val['year'] = '';
        }
        $sort[$key] = $val['year'];
    }
    array_multisort($sort, SORT_ASC, $musicData);
    //アーティスト名
    foreach ($musicData as $key => $val) {
        if (@!$val['artist_kana']) {
            $val['artist_kana'] = '';
        }
        $sort[$key] = $val['artist_kana'];
    }
    array_multisort($sort, SORT_ASC, $musicData);
    //アルバムアーティスト名
    foreach ($musicData as $key => $val) {
        if (@!$val['album_artist_kana']) {
            if (@$val['artist_kana']) {
                $val['album_artist_kana'] = $val['artist_kana'];
            } else {
                $val['album_artist_kana'] = '';
            }
        }
        $sort[$key] = $val['album_artist_kana'];
    }
    array_multisort($sort, SORT_ASC, $musicData);
    
    return $musicData;
}
