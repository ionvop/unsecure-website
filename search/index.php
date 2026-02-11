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
            Search | A Totally Secure Social Platform
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
                            WHERE "content" LIKE '%{$_GET["q"]}%'
                            AND ("visibility" = 'public'
                            {$privateQuery})
                            ORDER BY "time" DESC
                            LIMIT $maxPosts
                        SQL;

                        $posts = $db->query($query);

                        while ($post = $posts->fetchArray(SQLITE3_ASSOC)) {
                            echo renderPost($post);
                        }
                    ?>
                </div>
                <?php
                    $query = <<<SQL
                        SELECT COUNT(*) FROM "posts"
                        WHERE "content" LIKE '%{$_GET["q"]}%'
                        AND ("visibility" = 'public'
                        {$privateQuery})
                        ORDER BY "time" DESC
                    SQL;

                    $posts = $db->query($query);
                    $postCount = $posts->fetchArray(SQLITE3_NUM)[0];

                    if ($postCount > $maxPosts) {
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
            g_inputSearch.value = "<?= $_GET["q"] ?>";
        </script>
    </body>
</html>