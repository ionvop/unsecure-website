<?php

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
            Home | A Totally Secure Social Platform
        </title>
        <base href="./">
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

            select {
                padding: 0.5rem;
                font-size: 0.7rem;
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
                    if ($user != false) {
                        echo <<<HTML
                            <div style="
                                padding: 1rem;">
                                <form style="
                                    display: grid;
                                    grid-template-columns: 1fr max-content;
                                    padding: 1rem;
                                    background-color: #fff;
                                    border-radius: 1rem;"
                                    action="server.php"
                                    method="post"
                                    enctype="multipart/form-data">
                                    <div style="
                                        padding: 1rem;">
                                        <textarea name="content"
                                            placeholder="What's on your mind?"
                                            required></textarea>
                                    </div>
                                    <div style="
                                        display: grid;
                                        grid-template-rows: repeat(3, max-content) 1fr max-content;">
                                        <div style="
                                            padding: 1rem;">
                                            <select name="visibility">
                                                <option value="public">
                                                    Public
                                                </option>
                                                <option value="private">
                                                    Private
                                                </option>
                                            </select>
                                        </div>
                                        <div style="
                                            padding: 1rem;
                                            padding-top: 0rem;
                                            text-align: center;">
                                            <svg style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#aaaaaa" id="btnImage"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm80-160h400q12 0 18-11t-2-21L586-459q-6-8-16-8t-16 8L450-320l-74-99q-6-8-16-8t-16 8l-80 107q-8 10-2 21t18 11Z"/></svg>
                                            <input style="
                                                display: none;"
                                                type="file"
                                                name="image"
                                                accept="image/*"
                                                id="inputImage">
                                        </div>
                                        <div style="
                                            padding: 1rem;
                                            padding-top: 0rem;
                                            text-align: center;">
                                            <img style="
                                                visibility: hidden;
                                                width: 5rem;
                                                height: 5rem;
                                                border-radius: 1rem;
                                                object-fit: cover;"
                                                src=""
                                                id="imgPreview">
                                        </div>
                                        <div></div>
                                        <div style="
                                            padding: 1rem;
                                            text-align: center;">
                                            <button name="method"
                                                value="post">
                                                Post
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        HTML;
                    }
                ?>
                <div>
                    <?php
                        $query = <<<SQL
                            SELECT * FROM "posts"
                            WHERE "visibility" = 'public'
                            {$privateQuery}
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
                        WHERE "visibility" = 'public'
                        {$privateQuery}
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
            const btnImage = document.getElementById("btnImage");
            const imgPreview = document.getElementById("imgPreview");
            const inputImage = document.getElementById("inputImage");
            const btnMore = document.getElementById("btnMore");

            btnImage.onclick = () => {
                inputImage.click();
            }

            inputImage.onchange = () => {
                if (inputImage.files.length > 0) {
                    btnImage.setAttribute("fill", "#00aaff");
                    imgPreview.setAttribute("src", URL.createObjectURL(inputImage.files[0]));
                    imgPreview.style.visibility = "visible";
                } else {
                    btnImage.setAttribute("fill", "#aaaaaa");
                    imgPreview.setAttribute("src", "");
                    imgPreview.style.visibility = "hidden";
                }
            }
            
            btnMore.onclick = () => {
                const query = new URLSearchParams(location.search);
                query.set("max", Number(query.get("max")) + 5);
                location.search = query.toString();
            }
        </script>
    </body>
</html>