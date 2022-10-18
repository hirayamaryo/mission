<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>

    <?php
    $name_value = "";
    $comm_value = "";
    $hens_value = "";
    $pwsa_value = "";
    $valu_value = "追加";
    
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    // DBを作成
    $sql = "CREATE TABLE IF NOT EXISTS tbtest2"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "time TEXT,"
    . "pw TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    // フォームの内容をDBに追加
    if(!empty($_POST['name']) && !empty($_POST['pw']) && empty($_POST['henshu'])){
        $sql = $pdo -> prepare("INSERT INTO tbtest2 (name, comment, time, pw) VALUES (:name, :comment, :time, :pw)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':time', $time, PDO::PARAM_STR);
        $sql -> bindParam(':pw', $pw, PDO::PARAM_STR);
        // フォームの内容を取ってくる
        $name = $_POST['name'];
        $comment = $_POST['comment'];
        $time = new DateTime();
        $time = $time->format('Y-m-d H:i:s');
        $pw = $_POST['pw'];
        $sql -> execute();
        
    }
    
    // パスワード認証/本人確認
    if(!empty($_POST['num']) || !empty($_POST['numhe'])){
        if(!empty($_POST['num'])){
            $id = $_POST['num']; // idがこの値のデータだけを抽出したい、とする
        }else{
            $id = $_POST['numhe'];
        }
        $sql = 'SELECT pw FROM tbtest2 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll();
        
        foreach($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            if($_POST['pwsak']==$row['pw'] || $_POST['pwhen']==$row['pw']){
                $pwkaku = $row['pw'];
            }else{
                // echo "pw不一致<br>";
                $alert = "<script type='text/javascript'>alert('番号またはPWが異なります。再度入力して下さい');</script>";
                echo $alert;
                $pwkaku="";
            }
            
        }
    }
    
    // 削除
    if(!empty($_POST['num']) && !empty($_POST['pwsak'] && $_POST['pwsak']==$pwkaku)){
        $id = $_POST['num'];
        $sql = 'delete from tbtest2 where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    // 編集準備-編集内容を登録の欄に反映
    if(!empty($_POST['numhe']) && !empty($_POST['pwhen']) && $_POST['pwhen']==$pwkaku){
        $id = $_POST['numhe'] ; // idがこの値のデータだけを抽出したい、とする
        $sql = 'SELECT * FROM tbtest2 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll();
        
        foreach($results as $row){
            print_r($row);
            //$rowの中にはテーブルのカラム名が入る
            $hens_value = $row['id'];
            $name_value = $row['name'];
            $comm_value = $row['comment'];
            $pwsa_value = $row['pw'];
            $valu_value = "編集";
        }
    }
    ?>
    <form method="POST", action="">
        <!--新規投稿フォーム-->
        <!--編集モードでは、編集している番号を表示-->
        <h3>掲示板</h3>
        <li><?= $valu_value ?></li>
        <input type='hidden', name='henshu', placeholder="編集したい番号が表示される", value=<?= $hens_value?>><br>
        <input type='text', name='name', placeholder="名前を入力", value=<?= $name_value?>><br>
        <input type='text', name='comment', placeholder="コメントを入力", value=<?= $comm_value?>>
        <input type='text', name='pw', placeholder="PWを入力", value=<?= $pwsa_value?>>
        <input type='submit', name='submit', value=<?= $valu_value?>><br><br>
        <!--削除フォーム-->
        <li>削除フォーム</li>
        <input type='number', name='num', placeholder="削除する投稿番号を入力">
        <input tpye='text', name='pwsak', placeholder="PWを入力">
        <input type='submit', name='submit', value="削除"><br><br>
        <!--編集フォーム-->
        <li>編集フォーム</li>
        <input type='number', name='numhe', placeholder="編集する投稿番号を入力">
        <input tpye='text', name='pwhen', placeholder="PWを入力">
        <input type='submit', name='submit', value="編集">
        
    </form>
<?php
    if(!empty($_POST['name']) && !empty($_POST['henshu'])){
        $id = $_POST['henshu'];
        $name = $_POST['name'];
        $comment = $_POST['comment'];
        $pw = $_POST['pw'];
        $sql = 'UPDATE tbtest2 SET name=:name, comment=:comment, time=:time, pw=:pw WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $time = new DateTime();
        $time = $time->format('Y-m-d H:i:s');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':pw', $pw, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    // 表示
    $sql = 'SELECT * FROM tbtest2';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['time'].',';
        echo $row['pw'].'<br>';
        // echo "<hr>";
    }
    ?>
    
    <!--var_dump($row);-->

</body>