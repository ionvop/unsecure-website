<?php

chdir("../");
require_once "common.php";
$db = new SQLite3("database.db");
$user = getUser();
$maxPosts = $_GET["max"] ?? 10;

$query = <<<SQL
    SELECT * FROM "users"
    WHERE "id" = '{$_GET["id"]}';
SQL;

$target = $db->query($query)->fetchArray(SQLITE3_ASSOC);

if ($target == false) {
    alert("User not found.");
}

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
            User | A Totally Secure Social Platform
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
        <div>
            <div style="
                display: grid;
                grid-template-columns: max-content 1fr;
                min-height: 100%;">
                <?= renderNavigation() ?>
                <div style="
                    background-color: #eef;">
                    <div style="
                        padding: 1rem;">
                        <div style="
                            display: grid;
                            grid-template-columns: max-content 1fr max-content;
                            background-color: #fff;
                            border-radius: 1rem;">
                            <div style="
                                padding: 1rem;">
                                <img style="
                                    width: 10rem;
                                    height: 10rem;
                                    border-radius: 50%;
                                    object-fit: cover;"
                                    src="uploads/<?= $target["avatar"] ?>">
                            </div>
                            <div>
                                <div style="
                                    padding: 1rem;
                                    font-size: 2rem;
                                    font-weight: bold;">
                                    <?= $target["username"] ?>
                                </div>
                                <div style="
                                    padding: 1rem;">
                                    <div style="
                                        padding: 1rem;
                                        background-color: #eee;
                                        border-radius: 1rem;">
                                        <?= $target["description"] ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <?php
                                    if ($user != false) {
                                        if ($target["id"] == $user["id"]) {
                                            echo <<<HTML
                                                <a style="
                                                    display: block;
                                                    padding: 1rem;"
                                                    href="user/edit/">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M433-80q-27 0-46.5-18T363-142l-9-66q-13-5-24.5-12T307-235l-62 26q-25 11-50 2t-39-32l-47-82q-14-23-8-49t27-43l53-40q-1-7-1-13.5v-27q0-6.5 1-13.5l-53-40q-21-17-27-43t8-49l47-82q14-23 39-32t50 2l62 26q11-8 23-15t24-12l9-66q4-26 23.5-44t46.5-18h94q27 0 46.5 18t23.5 44l9 66q13 5 24.5 12t22.5 15l62-26q25-11 50-2t39 32l47 82q14 23 8 49t-27 43l-53 40q1 7 1 13.5v27q0 6.5-2 13.5l53 40q21 17 27 43t-8 49l-48 82q-14 23-39 32t-50-2l-60-26q-11 8-23 15t-24 12l-9 66q-4 26-23.5 44T527-80h-94Zm49-260q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Z"/></svg>
                                                </a>
                                            HTML;
                                        } else {
                                            $query = <<<SQL
                                                SELECT * FROM "follows"
                                                WHERE "user_id" = '{$user["id"]}'
                                                AND "target_id" = '{$target["id"]}'
                                            SQL;

                                            $follow = $db->query($query)->fetchArray(SQLITE3_ASSOC);

                                            if ($follow == false) {
                                                echo <<<HTML
                                                    <form action="server.php"
                                                        method="post"
                                                        enctype="multipart/form-data">
                                                        <button style="
                                                            background-color: transparent;"
                                                            name="method"
                                                            value="follow">
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M451.5-152q-14.5-5-25.5-16l-69-63q-106-97-191.5-192.5T80-634q0-94 63-157t157-63q53 0 100 22.5t80 61.5q33-39 80-61.5T660-854q94 0 157 63t63 157q0 115-85 211T602-230l-68 62q-11 11-25.5 16t-28.5 5q-14 0-28.5-5Z"/></svg>
                                                        </button>
                                                        <input type="hidden" name="target_id" value="{$target['id']}">
                                                    </form>
                                                HTML;
                                            } else {
                                                echo <<<HTML
                                                    <form action="server.php"
                                                        method="post"
                                                        enctype="multipart/form-data">
                                                        <button style="
                                                            background-color: transparent;"
                                                            name="method"
                                                            value="unfollow"
                                                            onclick="return confirm('Are you sure you want to unfollow this user?')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e31f1f"><path d="M451.5-152q-14.5-5-25.5-16l-69-63q-106-97-191.5-192.5T80-634q0-94 63-157t157-63q53 0 100 22.5t80 61.5q33-39 80-61.5T660-854q94 0 157 63t63 157q0 115-85 211T602-230l-68 62q-11 11-25.5 16t-28.5 5q-14 0-28.5-5Z"/></svg>
                                                        </button>
                                                        <input type="hidden" name="target_id" value="{$target['id']}">
                                                    </form>
                                                HTML;
                                            }
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <?php
                            $query = <<<SQL
                                SELECT * FROM "posts"
                                WHERE "user_id" = '{$target["id"]}'
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
                            SELECT COUNT(*) FROM 'posts'
                            WHERE "user_id" = '{$target["id"]}'
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