<?php

chdir("../");
require_once "common.php";
$db = new SQLite3("database.db");
$user = getUser();
$maxComments = $_GET["max"] ?? 10;

?>

<html>
    <head>
        <title>
            Post | A Totally Secure Social Platform
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

            textarea {
                background-color: #eee;
                border-radius: 1rem;
                height: 100%;
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
                <?php
                    $query = <<<SQL
                        SELECT *
                        FROM "posts"
                        WHERE "id" = '{$_GET["id"]}'
                    SQL;

                    $post = $db->query($query)->fetchArray(SQLITE3_ASSOC);
                    echo renderPost($post);
                ?>
                <div style="
                    padding: 1rem;">
                    <div style="
                        background-color: #fff;
                        border-radius: 1rem;">
                        <div style="
                            padding: 1rem;
                            text-align: center;
                            font-size: 2rem;
                            font-weight: bold;">
                            Comments
                        </div>
                        <?php
                            if ($user != false) {
                                echo <<<HTML
                                    <form style="
                                        display: grid;
                                        grid-template-columns: 1fr max-content;"
                                        action="server.php"
                                        method="post"
                                        enctype="multipart/form-data">
                                        <div style="
                                            padding: 1rem;">
                                            <textarea name="content"
                                                placeholder="Write a comment"
                                                required></textarea>
                                        </div>
                                        <div style="
                                            display: grid;
                                            grid-template-rows: 1fr max-content">
                                            <div></div>
                                            <div style="
                                                padding: 1rem;">
                                                <button name="method"
                                                    value="comment">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M176-183q-20 8-38-3.5T120-220v-180l320-80-320-80v-180q0-22 18-33.5t38-3.5l616 260q25 11 25 37t-25 37L176-183Z"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="post_id" value="{$_GET['id']}"/>
                                    </form>
                                HTML;
                            }
                        ?>
                        <div>
                            <?php
                                $query = <<<SQL
                                    SELECT * FROM "comments"
                                    WHERE "post_id" = '{$_GET["id"]}'
                                    ORDER BY "time" DESC
                                    LIMIT $maxComments
                                SQL;

                                $comments = $db->query($query);

                                while ($comment = $comments->fetchArray(SQLITE3_ASSOC)) {
                                    echo renderComment($comment);
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="script.js"></script>
        <script>

        </script>
    </body>
</html>