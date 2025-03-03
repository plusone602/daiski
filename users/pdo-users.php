<?php
require_once('../daiski/pdo_connect.php');

$sqlALL = "SELECT * FROM users WHERE valid=1";
$stmtALL = $db_host->prepare($sqlALL);
$stmtALL->execute();
$userCount = $stmtALL->rowCount();


if (isset($_GET["q"])) {
    $q = $_GET["q"];
    $sql = "SELECT * FROM users WHERE name LIKE '%$q%'";
} else if (isset($_GET["p"]) && isset($_GET["order"])) {
    $p = $_GET["p"];
    $order = $_GET["order"];
    $orderClause = "";
    switch ($order) {
        case 1;
            $orderClause = "ORDER BY id ASC";
            break;
        case 2;
            $orderClause = "ORDER BY id DESC";
            break;
        case 3;
            $orderClause = "ORDER BY account ASC";
            break;
        case 4;
            $orderClause = "ORDER BY account DESC";
            break;
    }

    $perPage = 25;
    $startItem = ($p - 1) * $perPage;
    $totalPage = ceil($userCount / $perPage);
    $sql = "SELECT * FROM users WHERE valid=1
     $orderClause
     LIMIT $startItem,$perPage";
} else {
    header("location:pdo-users.php?p=1&order=1");
    // $sql = "SELECT * FROM users WHERE valid=1";
}



$stmt = $db_host->prepare($sql);

try {
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (isset($_GET["q"])) {
        $userCount = $stmt->rowCount();
    }
    // echo "<pre>"; 會將結果展開，自動換行
    // print_r($rows);會將結果用陣列的方式顯現出來
    // echo "</pre>";
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $db_host = NULL;
    exit;
}

$db_host = NULL;
?>

<!doctype html>
<html lang="en">

<head>
    <title>pdo-users</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <?php include("./css.php") ?>
    <style>
        body {
			color: white !important;
			background-color: #07192F;

			table {
				color: white !important;
			}
		}
    </style>
</head>

<body>
    <!-- Loading 畫面 -->
 <div id="loadingOverlay">
    <div class="spinner"></div>
  </div>
    <div class="d-flex flex-column " id="mainContent">
        <?php include("./new_head_mod.php"); ?>
        <div class="d-flex flex-row w-100 myPage">
            <?php include("./new_side_mod.php"); ?>
            <div class="container ">
                <div class="py-2">
                    <a class="btn btn-primary" href="create-user.php"><i class="fa-solid fa-user-plus fa-fw"></i> Add User</a>
                </div>
                <div class="py-2 row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="hstack gap-2 align-items-center">
                            <?php if (isset($_GET["q"])): ?>
                                <a href="pdo-users.php" class="btn btn-primary"><i class="fa-solid fa-left-long fa-fw"></i></a>
                            <?php endif; ?>
                            <div>共 <?= $userCount ?> 位使用者
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form action="" method="get">
                            <div class="input-group">
                                <input type="search" class="form-control" name="q"
                                    <?php
                                    // $q=""; if (isset($_GET["q"])) {
                                    // $q = $_GET["q"];
                                    // }
                                    // $q=(isset($_GET["q"]))?
                                    // $_GET["q"] : "";
                                    $q = $_GET["q"] ?? "";
                                    ?>
                                    value="<?= $q ?>">
                                <button class="btn btn-primary">
                                    <i class="fa-solid fa-magnifying-glass fa-fw" type="submit"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="py-2 text-end">
                    <div class="btn-group">
                        <a class="btn btn-primary <?php if ($order == 1) echo "active" ?>" href="pdo-users.php?p=<?= $p ?>&order=1"><i class="fa-solid fa-arrow-down-1-9 fa-fw"></i></a>
                        <a class="btn btn-primary <?php if ($order == 2) echo "active" ?>" href="pdo-users.php?p=<?= $p ?>&order=2"><i class="fa-solid fa-arrow-down-9-1 fa-fw"></i></a>
                        <a class="btn btn-primary <?php if ($order == 3) echo "active" ?>" href="pdo-users.php?p=<?= $p ?>&order=3"><i class="fa-solid fa-arrow-down-a-z fa-fw"></i></a>
                        <a class="btn btn-primary <?php if ($order == 4) echo "active" ?>" href="pdo-users.php?p=<?= $p ?>&order=4"><i class="fa-solid fa-arrow-down-a-z fa-fw"></i></a>
                    </div>
                </div>
                <?php if ($userCount > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>name</th>
                                <th>account</th>
                                <th>phone</th>
                                <th>birthday</th>
                                <th>email</th>
                                <th>createdtime</th>
                                <th>isCoach</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?= $row["id"] ?></td>
                                    <td><?= $row["name"] ?></td>
                                    <td><?= $row["account"] ?></td>
                                    <td><?= $row["phone"] ?></td>
                                    <td><?= $row["birthday"] ?></td>
                                    <td><?= $row["email"] ?></td>
                                    <td><?= $row["createdtime"] ?></td>
                                    <td><?= $row["isCoach"] ?></td>
                                    <td>
                                        <a class="btn btn-primary" href="pdo-user.php?id=<?= $row["id"] ?>"><i class="fa-solid fa-eye fa-fw"></i></a>
                                        <a class="btn btn-primary" href="user-edit.php?id=<?= $row["id"] ?>"><i class="fa-solid fa-pen-to-square fa-fw"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (isset($_GET["p"])): ?>
                        <div>
                            <nav aria-label="">
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                                        <?php
                                        $active = ($i == $_GET["p"]) ?
                                            "active" : "";
                                        ?>
                                        <li class="page-item <?= $active ?>"><a class="page-link" href="pdo-users.php?p=<?= $i ?>&order=<?= $order ?>"><?= $i ?></a></li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php include("../daiski/js.php") ?>

        <script>
            let users = <?= json_encode($rows) ?>;
            console.log(users);
        </script>
        <script>
            VANTA.BIRDS({
                el: ".sidebar", // 指定作用的 HTML 元素 ID
                mouseControls: true, // 啟用滑鼠控制，使動畫會跟隨滑鼠移動
                touchControls: true, // 啟用觸控控制，使動畫可以隨觸控移動
                gyroControls: false, // 禁用陀螺儀控制（手機旋轉時不影響動畫）
                minHeight: 50.00, // 設定最小高度，確保畫面不會小於 200px
                minWidth: 50.00, // 設定最小寬度，確保畫面不會小於 200px
                scale: 1.00, // 設定一般裝置上的縮放比例
                scaleMobile: 2.0, // 在手機上放大 2 倍，以提升可視度
                separation: 500.00, // 調整鳥群之間的間隔，數值越大，距離越大
                color1: 0xffffff,
                birdSize: 0.50,
                // backgroundColor:0x4e73df
            });
            VANTA.BIRDS({
                el: ".myPage", // 指定作用的 HTML 元素 ID
                mouseControls: true, // 啟用滑鼠控制，使動畫會跟隨滑鼠移動
                touchControls: true, // 啟用觸控控制，使動畫可以隨觸控移動
                gyroControls: false, // 禁用陀螺儀控制（手機旋轉時不影響動畫）
                minHeight: 50.00, // 設定最小高度，確保畫面不會小於 200px
                minWidth: 50.00, // 設定最小寬度，確保畫面不會小於 200px
                scale: 1.00, // 設定一般裝置上的縮放比例
                scaleMobile: 2.0, // 在手機上放大 2 倍，以提升可視度
                separation: 500.00, // 調整鳥群之間的間隔，數值越大，距離越大
                color1: 0xffffff,
                birdSize: 0.50,
                // backgroundColor:0x4e73df
            });
            VANTA.BIRDS({
                el: ".head", // 指定作用的 HTML 元素 ID
                mouseControls: true, // 啟用滑鼠控制，使動畫會跟隨滑鼠移動
                touchControls: true, // 啟用觸控控制，使動畫可以隨觸控移動
                gyroControls: false, // 禁用陀螺儀控制（手機旋轉時不影響動畫）
                minHeight: 50.00, // 設定最小高度，確保畫面不會小於 200px
                minWidth: 50.00, // 設定最小寬度，確保畫面不會小於 200px
                scale: 1.00, // 設定一般裝置上的縮放比例
                scaleMobile: 2.0, // 在手機上放大 2 倍，以提升可視度
                separation: 500.00, // 調整鳥群之間的間隔，數值越大，距離越大
                color1: 0xffffff,
                birdSize: 0.50,
                // backgroundColor:0x4e73df
            });

            //VANTA.WAVES({ //目前註解掉
            //  el: "body", //綁在body上會使該網頁的modal跳出來時有問題 最好綁在你需要的class上
            //  mouseControls: true,
            //  touchControls: true,
            //  gyroControls: false,
            //  minHeight: 200.00,
            //  minWidth: 200.00,
            //  scale: 1.00,
            //  scaleMobile: 1.00,
            //  color:0xb2e2ff
            // })
        </script>
    </div>
</body>

</html>