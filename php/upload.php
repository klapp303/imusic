<?php
//本番環境と開発環境での切り替えがある場合
if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    $dir_url = '';
} else {
    $dir_url = '/imusic';
}
//エラーメッセージは消しておく
$error_msg = false;



//モードを取得
if (@$_POST['mode']) {
    $mode = $_POST['mode'];
}



//ファイルを受け取ってresult.phpに渡す
if (@$_FILES) {
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        if ($_FILES['file']['type'] == 'text/xml') {
            $file_pass = '../uploads/iTunes Library.xml';
            //既にファイルがあれば削除して
            if (file_exists($file_pass)) {
                unlink($file_pass);
            }
            if (move_uploaded_file($_FILES['file']['tmp_name'], $file_pass)) {
                if ($mode == 'rate') {
                    header('Location: ' . $dir_url . '/result.php?mode=rate');
		} elseif ($mode == 'play_count') {
                    header('Location: ' . $dir_url . '/result.php?mode=play_count');
                } elseif ($mode == 'count') {
                    header('Location: ' . $dir_url . '/result.php?mode=count');
                } elseif ($mode == 'rate5') {
                    header('Location: ' . $dir_url . '/result.php?mode=rate5');
                } elseif ($mode == 'rate4') {
                    header('Location: ' . $dir_url . '/result.php?mode=rate4');
                } else {
                    header('Location: ' . $dir_url . '/');
                }
                exit;
                
            //ファイルの保存に失敗した場合
            } else {
                $error_msg = 'ファイルを読み込めませんでした';
            }
        //ファイル形式がxmlでない場合
        } else {
            $error_msg = 'xmlファイルを選択してください';
        }
    //ファイルを正規の手段で受け取っていない場合
    } else {
        $error_msg = 'ファイルを読み込めませんでした';
    }
//php設定を超えるファイルの場合
} else {
    $error_msg = 'ファイルサイズが大きすぎます';
}

//sessionにエラーメッセージを渡しておく
session_start();
if (!$error_msg) {
    $error_msg = 'ファイルを読み込めませんでした';
}
$_SESSION['error_msg'] = $error_msg;

header('Location: ' . $dir_url . '/');
exit;
