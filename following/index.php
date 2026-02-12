<?php

chdir("../");
require_once "common.php";
$db = new SQLite3("database.db");
$user = getUser();
$maxPosts = $_GET["max"] ?? 10;

$privateQuery = "";

if ($user != false) {
    $privateQuery = <<<SQL
        OR ("visibility" = "private" AND "user_id" = '{$user["id"]}')
    SQL;
}

?>

<html>
    <head>
        <title>
            Following | A Totally Secure Social Platform
        </title>
        <base href="../">
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            button {
                background-color: #0af;
                color: #fff;
                border-radius: 1rem;
            }
        </style>
    </head>
    <body>
        <div style="
            display: grid;
            grid-template-columns: max-content 1fr;
            min-height: 100%;">
            <?= renderNavigation() ?>
            <div style="
                background-color: #eef;">
                <div>
                    <?php
                        $query = <<<SQL
                            SELECT * FROM "posts"
                            WHERE "visibility" = 'public'
                            {$privateQuery}
                            ORDER BY "time" DESC
                        SQL;

                        $posts = $db->query($query);
                        $count = 0;

                        while ($post = $posts->fetchArray(SQLITE3_ASSOC)) {
                            if ($count >= $maxPosts) {
                                break;
                            }
                            
                            $query = <<<SQL
                                SELECT * FROM "users"
                                WHERE "id" = '{$post["user_id"]}'
                            SQL;

                            $author = $db->query($query)->fetchArray(SQLITE3_ASSOC);

                            $query = <<<SQL
                                SELECT * FROM "follows"
                                WHERE "user_id" = '{$user["id"]}'
                                AND "target_id" = '{$author["id"]}'
                            SQL;

                            $follow = $db->query($query)->fetchArray(SQLITE3_ASSOC);

                            if ($follow == false) {
                                continue;
                            }

                            echo renderPost($post);
                            $count++;
                        }
                    ?>
                </div>
                <?php
                    if ($count >= $maxPosts) {
                        echo <<<HTML
                            <div style="
                                padding: 1rem;
                                text-align: center;">
                                <button id="btnMore">
                                    Load more
                                </button>
                            </div>
                        HTML;
                    }
                ?>
            </div>
        </div>
        <script src="script.js"></script>
        <script>
            const btnMore = document.getElementById("btnMore");

            btnMore.onclick = () => {
                const query = new URLSearchParams(location.search);
                query.set("max", Number(query.get("max")) + 5);
                location.search = query.toString();
            }
        </script>
    </body>
</html>