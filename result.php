<?php
//本番環境と開発環境での切り替えがある場合
if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    $dir_url = '';
} else {
    ini_set('display_errors', 0);
    $dir_url = '/imusic';
}
$home_url = $dir_url . '/';
//関数の読み込み
require_once dirname(__FILE__) . '/php/function.php';



//getデータを取得
if (@$_GET['mode']) {
    $mode = $_GET['mode'];
} else {
    $mode = 'count';
}
//sessionデータを取得
session_start();
if (@$_SESSION['musicAll']) {
    $musicAll = $_SESSION['musicAll'];

//sessionデータに楽曲データがなければ、iTunes Libraryから読み込む
} else {
    require_once dirname(__FILE__) . '/php/imusic_lib_import.php';

    //エラー処理
    if (@!$musicAll) {
        $error_msg = '楽曲が登録されていません。';
    }
    if (@$error_msg) {
        echo $error_msg;
        exit;
    }

    //取得した楽曲データはsessionに渡しておく
    $_SESSION['musicAll'] = $musicAll;
}



if ($mode == 'rate5') {
    $musicData = search_word_imusic($musicAll, array('rate' => 5));
} elseif ($mode == 'rate4') {
    $musicData = search_word_imusic($musicAll, array('rate' => 4), array('rate' => 5));
} else {
    $musicData = $musicAll;
}
//全楽曲データのページなのか判定するため
$allData = true;
//getデータに基いて楽曲データを検索
if (@$_GET['genre']) {
    $genre = $_GET['genre'];
    $musicData = search_word_imusic($musicData, array('genre' => $genre));
    $allData = false;
}
if (@$_GET['year']) {
    $year = $_GET['year'];
    $musicData = search_word_imusic($musicData, array('year' => $year));
    $allData = false;
}
if (@$_GET['artist']) {
    $artist = $_GET['artist'];
    $musicData = search_word_imusic($musicData, array('artist' => $artist));
    $allData = false;
}
if (@$_GET['composer']) {
    $composer = $_GET['composer'];
    $musicData = search_word_imusic($musicData, array('composer' => $composer));
    $allData = false;
}



//全体レポート
$musicAllCount = count($musicData);

//ジャンルレポート
if (@!$genre) {
    $musicGenre = search_prop_imusic($musicData, 'genre');
    //ジャンルの固定順に並び替える
    $musicGenre = sort_imusic_genre($musicGenre);
}

//年レポート
if (@!$year) {
    $musicYear = search_prop_imusic($musicData, 'year');
    //年の降順に並び替える
    foreach ($musicYear as $key => $val) {
        if (@!$val[0]['year']) {
            $val[0]['year'] = 0;
        }
        $sort3[$key] = $val[0]['year'];
    }
    array_multisort($sort3, SORT_DESC, $musicYear);
}

//アーティストレポート
if ($mode == 'rate5' || $mode == 'rate4') {
    if ($allData) {
        $max = 100;
        $min = 5;
    } else {
        $max = 10;
        $min = false;
    }
} elseif ($mode == 'play_count') {
    if ($allData) {
        $max = 100;
        $min = 20;
    } else {
        $max = 10;
        $min = 5;
    }
} else {
    if ($allData) {
        $max = 100;
        $min = 20;
    } else {
        $max = 10;
        $min = 5;
    }
}
if (@!$artist) {
    $musicArtist = search_prop_imusic($musicData, 'artist', $max, $min, $mode);
    if (@$mode == 'rate') { //レートの降順に並び替える
        $musicArtist = sort_imusic_rate($musicArtist);
    } elseif (@$mode == 'play_count') { //再生数の降順に並び替える
        $musicArtist = sort_imusic_play_count($musicArtist);
    }
}

//作曲者レポート
if ($mode == 'rate5' || $mode == 'rate4') {
    if ($allData) {
        $max = 100;
        $min = 5;
    } else {
        $max = 10;
        $min = false;
    }
} elseif ($mode == 'play_count') {
    if ($allData) {
        $max = 100;
        $min = 10;
    } else {
        $max = 10;
        $min = 5;
    }
} else {
    if ($allData) {
        $max = 100;
        $min = 10;
    } else {
        $max = 10;
        $min = 5;
    }
}
if (@!$composer) {
    $musicComposer = search_prop_imusic($musicData, 'composer', $max, $min, $mode);
    if (@$mode == 'rate') { //レートの降順に並び替える
        $musicComposer = sort_imusic_rate($musicComposer);
    } elseif (@$mode == 'play_count') { //再生数の降順に並び替える
        $musicComposer = sort_imusic_play_count($musicComposer);
    }
}

//echo'<pre>';print_r($musicGenre);echo'</pre>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>楽曲データ</title>
<link rel="stylesheet" type="text/css" href="imusic.css">
</head>

<body>
<div id="container">
<h1>楽曲データ</h1>

<div><a href="<?php echo $home_url; ?>">ページTOP</a>へ戻る</div>
<?php if (!$allData): ?>
<div><a href="<?php echo $home_url . 'result.php?mode=' . $mode; ?>">解析結果TOP</a>へ戻る</div>
<?php else: ?>
<select name="mode" onchange="selectMode(this);">
  <option value="rate" <?php echo ($mode == 'rate')? 'selected' : ''; ?>>レート</option>
  <option value="count" <?php echo ($mode == 'count')? 'selected' : ''; ?>>曲数</option>
  <option value="rate5" <?php echo ($mode == 'rate5')? 'selected' : ''; ?>>レート5のみ</option>
  <option value="rate4" <?php echo ($mode == 'rate4')? 'selected' : ''; ?>>レート4以上</option>
</select>
<script>
    function selectMode(obj) {
        var selected = obj.selectedIndex;
        var value = obj.options[selected].value;
        location.href = '<?php echo $dir_url; ?>/result.php?mode=' + value;
    }
</script>
<?php endif; ?>

<p>楽曲の登録数：<?php echo $musicAllCount; ?> 曲</p>

<?php if (@$musicGenre): ?>
<h2>ジャンルレポート</h2>
<table class="imusic-table">
  <tr>
    <th>ジャンル</th><th>再生回数</th><th>曲数</th><th>レート平均</th>
  </tr>
  <?php foreach ($musicGenre as $key => $val): ?>
  <tr>
    <td><?php echo $key; ?></td>
    <td><?php echo $val['play_count']; ?></td>
    <td><?php echo $val['count']; ?></td>
    <td><?php echo $val['rate']; ?></td>
    <?php if ($allData): ?>
    <td class="cell-link"><a href="<?php echo $dir_url; ?>/result.php?mode=<?php echo $mode; ?>&genre=<?php echo $key; ?>">>></a></td>
    <?php endif; ?>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if (@$musicYear): ?>
<h2>年レポート</h2>
<table class="imusic-table">
  <tr>
    <th>年</th><th>再生回数</th><th>曲数</th><th>レート平均</th>
  </tr>
  <?php foreach ($musicYear as $val): ?>
  <tr>
    <td><?php echo $val[0]['year']; ?></td>
    <td><?php echo $val['play_count']; ?></td>
    <td><?php echo $val['count']; ?></td>
    <td><?php echo $val['rate']; ?></td>
    <?php if ($allData): ?>
    <td class="cell-link"><a href="<?php echo $dir_url; ?>/result.php?mode=<?php echo $mode; ?>&year=<?php echo $val[0]['year']; ?>">>></a></td>
    <?php endif; ?>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if (@$musicArtist): ?>
<h2>アーティストレポート</h2>
<table class="imusic-table">
  <tr>
    <th>アーティスト</th><th>再生回数</th><th>曲数</th><th>レート平均</th>
  </tr>
  <?php foreach ($musicArtist as $key => $val): ?>
  <tr>
    <td><?php echo $key; ?></td>
    <td><?php echo $val['play_count']; ?></td>
    <td><?php echo $val['count']; ?></td>
    <td><?php echo $val['rate']; ?></td>
    <?php if ($allData): ?>
    <td class="cell-link"><a href="<?php echo $dir_url; ?>/result.php?mode=<?php echo $mode; ?>&artist=<?php echo $key; ?>">>></a></td>
    <?php endif; ?>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if (@$musicComposer): ?>
<h2>作曲者レポート</h2>
<table class="imusic-table">
  <tr>
    <th>作曲者</th><th>再生回数</th><th>曲数</th><th>レート平均</th>
  </tr>
  <?php foreach ($musicComposer as $key => $val): ?>
  <tr>
    <td><?php echo $key; ?></td>
    <td><?php echo $val['play_count']; ?></td>
    <td><?php echo $val['count']; ?></td>
    <td><?php echo $val['rate']; ?></td>
    <?php if ($allData): ?>
    <td class="cell-link"><a href="<?php echo $dir_url; ?>/result.php?mode=<?php echo $mode; ?>&composer=<?php echo $key; ?>">>></a></td>
    <?php endif; ?>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div><!-- /#container -->
</body>
</html>