<?php

/**
 * Prints the given message and exits the script.
 *
 * @param mixed $message The message to be printed.
 * @return void
 */
function breakpoint($message) {
    header("Content-type: application/json");
    print_r($message);
    exit;
}

/**
 * Prints the given message as an alert and redirects the user.
 *
 * @param string $message The message to be displayed.
 * @param string $redirect The URL to redirect the user to. If empty, the user will be redirected back.
 * @return void
 */
function alert($message, $redirect = "") {
    $message = json_encode($message);

    $redirectScript = <<<JS
        window.history.back();
    JS;
    
    if ($redirect != "") {
        $redirect = json_encode($redirect);

        $redirectScript = <<<JS
            location.href = {$redirect};
        JS;
    }

    echo <<<HTML
        <script>
            alert({$message});
            {$redirectScript}
        </script>
    HTML;

    exit;
}

function getUser() {
    $db = new SQLite3("database.db");

    if (isset($_COOKIE["userId"]) == false) {
        return false;
    }

    $query = <<<SQL
        SELECT * FROM "users"
        WHERE "id" = '{$_COOKIE["userId"]}'
    SQL;

    return $db->query($query)->fetchArray(SQLITE3_ASSOC);
}

function renderNavigation() {
    $user = getUser();
    $profileRender = "";
    $loginRender = <<<HTML
        <a style="
            display: grid;
            grid-template-columns: max-content 1fr;
            border-bottom: 1px solid #05a;
            cursor: pointer;"
            href="login/">
            <div style="
                display: flex;
                align-items: center;
                padding: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M520-120q-17 0-28.5-11.5T480-160q0-17 11.5-28.5T520-200h240v-560H520q-17 0-28.5-11.5T480-800q0-17 11.5-28.5T520-840h240q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H520Zm-73-320H160q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520h287l-75-75q-11-11-11-27t11-28q11-12 28-12.5t29 11.5l143 143q12 12 12 28t-12 28L429-309q-12 12-28.5 11.5T372-310q-11-12-10.5-28.5T373-366l74-74Z"/></svg>
            </div>
            <div style="
                display: flex;
                align-items: center;
                padding: 1rem;
                padding-left: 0rem;
                color: #fff;">
                Login / Register
            </div>
        </a>
    HTML;

    if ($user != false) {
        $profileRender = <<<HTML
            <a style="
                display: grid;
                grid-template-columns: max-content 1fr;
                border-bottom: 1px solid #05a;
                cursor: pointer;"
                href="user/?id={$user['id']}">
                <div style="
                    display: flex;
                    align-items: center;
                    padding: 1rem;">
                    <img style="
                        width: 2rem;
                        height: 2rem;
                        border-radius: 50%;
                        object-fit: cover;"
                        src="uploads/{$user['avatar']}">
                </div>
                <div style="
                    display: flex;
                    align-items: center;
                    padding: 1rem;
                    padding-left: 0rem;
                    color: #fff;">
                    <div style="
                        display: -webkit-box;
                        -webkit-line-clamp: 1;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        text-overflow: ellipsis;">
                        {$user['username']}
                    </div>
                </div>
            </a>
        HTML;

        $loginRender = <<<HTML
            <form style="
                border-bottom: 1px solid #05a;"
                action="server.php"
                method="post"
                enctype="multipart/form-data">
                <button style="
                    display: grid;
                    grid-template-columns: max-content 1fr;
                    width: 100%;
                    padding: 0rem;
                    background-color: transparent;"
                    name="method"
                    value="logout"
                    onclick="return confirm('Are you sure you want to logout?')">
                    <div style="
                        display: flex;
                        align-items: center;
                        padding: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h240q17 0 28.5 11.5T480-800q0 17-11.5 28.5T440-760H200v560h240q17 0 28.5 11.5T480-160q0 17-11.5 28.5T440-120H200Zm487-320H400q-17 0-28.5-11.5T360-480q0-17 11.5-28.5T400-520h287l-75-75q-11-11-11-27t11-28q11-12 28-12.5t29 11.5l143 143q12 12 12 28t-12 28L669-309q-12 12-28.5 11.5T612-310q-11-12-10.5-28.5T613-366l74-74Z"/></svg>
                    </div>
                    <div style="
                        display: flex;
                        align-items: center;
                        padding: 1rem;
                        padding-left: 0rem;
                        color: #fff;">
                        Logout
                    </div>
                </button>
            </form>
        HTML;
    }

    return <<<HTML
        <div style="
            background-color: #0af;">
            <div style="
                padding: 3rem;
                text-align: center;
                font-size: 2rem;
                font-weight: bold;
                color: #fff;
                border-bottom: 1px solid #05a;">
                A Totally Secure<br>
                Social Platform
            </div>
            {$profileRender}
            <a style="
                display: grid;
                grid-template-columns: max-content 1fr;
                border-bottom: 1px solid #05a;
                cursor: pointer;"
                href="./">
                <div style="
                    display: flex;
                    align-items: center;
                    padding: 1rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M160-200v-360q0-19 8.5-36t23.5-28l240-180q21-16 48-16t48 16l240 180q15 11 23.5 28t8.5 36v360q0 33-23.5 56.5T720-120H600q-17 0-28.5-11.5T560-160v-200q0-17-11.5-28.5T520-400h-80q-17 0-28.5 11.5T400-360v200q0 17-11.5 28.5T360-120H240q-33 0-56.5-23.5T160-200Z"/></svg>
                </div>
                <div style="
                    display: flex;
                    align-items: center;
                    padding: 1rem;
                    padding-left: 0rem;
                    color: #fff;">
                    Home
                </div>
            </a>
            {$loginRender}
            <form style="
                display: grid;
                grid-template-columns: 1fr max-content;
                border-bottom: 1px solid #05a;"
                action="search/">
                <div style="
                    display: flex;
                    align-items: center;
                    padding: 1rem;">
                    <input style="
                        background-color: #fff;
                        border-radius: 1rem;"
                        name="q"
                        placeholder="Search..."
                        id="g_inputSearch">
                </div>
                <div style="
                    display: flex;
                    align-items: center;
                    padding: 1rem;
                    padding-left: 0rem;">
                    <button style="
                        background-color: transparent;
                        padding: 0rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
                    </button>
                </div>
            </form>
        </div>
    HTML;
}

function renderPost($post) {
    $db = new SQLite3("database.db");
    $user = getUser();

    $query = <<<SQL
        SELECT * FROM "users"
        WHERE "id" = {$post["user_id"]};
    SQL;

    $author = $db->query($query)->fetchArray(SQLITE3_ASSOC);
    $visibilityRender = "";

    switch ($post["visibility"]) {
        case "public":
            $visibilityRender = <<<HTML
                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1f1f1f"><path d="M324-111.5Q251-143 197-197t-85.5-127Q80-397 80-480t31.5-156Q143-709 197-763t127-85.5Q397-880 480-880t156 31.5Q709-817 763-763t85.5 127Q880-563 880-480t-31.5 156Q817-251 763-197t-127 85.5Q563-80 480-80t-156-31.5ZM440-162v-78q-33 0-56.5-23.5T360-320v-40L168-552q-3 18-5.5 36t-2.5 36q0 121 79.5 212T440-162Zm276-102q41-45 62.5-100.5T800-480q0-98-54.5-179T600-776v16q0 33-23.5 56.5T520-680h-80v80q0 17-11.5 28.5T400-560h-80v80h240q17 0 28.5 11.5T600-440v120h40q26 0 47 15.5t29 40.5Z"/></svg>
            HTML;

            break;
        case "friends":
            $visibilityRender = <<<HTML
                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1f1f1f"><path d="M40-272q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v32q0 33-23.5 56.5T600-160H120q-33 0-56.5-23.5T40-240v-32Zm698 112q11-18 16.5-38.5T760-240v-40q0-44-24.5-84.5T666-434q51 6 96 20.5t84 35.5q36 20 55 44.5t19 53.5v40q0 33-23.5 56.5T840-160H738ZM247-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47Zm466 0q-47 47-113 47-11 0-28-2.5t-28-5.5q27-32 41.5-71t14.5-81q0-42-14.5-81T544-792q14-5 28-6.5t28-1.5q66 0 113 47t47 113q0 66-47 113Z"/></svg>
            HTML;

            break;
        case "private":
            $visibilityRender = <<<HTML
                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1f1f1f"><path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm296.5-223.5Q560-327 560-360t-23.5-56.5Q513-440 480-440t-56.5 23.5Q400-393 400-360t23.5 56.5Q447-280 480-280t56.5-23.5ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80Z"/></svg>
            HTML;

            break;
    }

    $deleteRender = "";

    if ($user != false && $user["id"] == $post["user_id"]) {
        $deleteRender = <<<HTML
            <form style="
                display: flex;
                align-items: center;
                padding: 1rem;"
                action="server.php"
                method="post"
                enctype="multipart/form-data">
                <button style="
                    background-color: transparent;
                    padding: 0rem;"
                    name="method"
                    value="delete_post"
                    onclick="return confirm('Are you sure you want to delete this post?')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm148.5-171.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5Z"/></svg>
                </button>
                <input type="hidden"
                    name="post_id"
                    value="{$post['id']}">
            </form>
        HTML;
    }

    $imageRender = "";

    if ($post["image"] != "") {
        $imageRender = <<<HTML
            <div style="
                padding: 1rem;">
                <img style="
                    max-width: 100%;"
                    src="uploads/{$post['image']}">
            </div>
        HTML;
    }

    $likeRender = <<<HTML
        <div style="
            display: flex;
            align-items: center;
            padding: 1rem;">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M451.5-152q-14.5-5-25.5-16l-69-63q-106-97-191.5-192.5T80-634q0-94 63-157t157-63q53 0 100 22.5t80 61.5q33-39 80-61.5T660-854q94 0 157 63t63 157q0 115-85 211T602-230l-68 62q-11 11-25.5 16t-28.5 5q-14 0-28.5-5Z"/></svg>
        </div>
    HTML;

    if ($user != false) {
        $likeRender = <<<HTML
            <form style="
                display: flex;
                align-items: center;
                padding: 1rem;"
                action="server.php"
                method="post"
                enctype="multipart/form-data">
                <button style="
                    background-color: transparent;
                    padding: 0rem;"
                    name="method"
                    value="like">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M451.5-152q-14.5-5-25.5-16l-69-63q-106-97-191.5-192.5T80-634q0-94 63-157t157-63q53 0 100 22.5t80 61.5q33-39 80-61.5T660-854q94 0 157 63t63 157q0 115-85 211T602-230l-68 62q-11 11-25.5 16t-28.5 5q-14 0-28.5-5Z"/></svg>
                </button>
                <input type="hidden"
                    name="post_id"
                    value="{$post['id']}">
            </form>
        HTML;

        $query = <<<SQL
            SELECT * FROM "likes"
            WHERE "user_id" = {$user['id']}
            AND "post_id" = {$post['id']}
        SQL;

        $like = $db->query($query)->fetchArray(SQLITE3_ASSOC);

        if ($like != false) {
            $likeRender = <<<HTML
                <form style="
                    display: flex;
                    align-items: center;
                    padding: 1rem;"
                    action="server.php"
                    method="post"
                    enctype="multipart/form-data">
                    <button style="
                        background-color: transparent;
                        padding: 0rem;"
                        name="method"
                        value="unlike">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e31f1f"><path d="M451.5-152q-14.5-5-25.5-16l-69-63q-106-97-191.5-192.5T80-634q0-94 63-157t157-63q53 0 100 22.5t80 61.5q33-39 80-61.5T660-854q94 0 157 63t63 157q0 115-85 211T602-230l-68 62q-11 11-25.5 16t-28.5 5q-14 0-28.5-5Z"/></svg>
                    </button>
                    <input type="hidden"
                        name="post_id"
                        value="{$post['id']}">
                </form>
            HTML;
        }
    }

    $query = <<<SQL
        SELECT COUNT(*) FROM "likes"
        WHERE "post_id" = {$post['id']}
    SQL;

    $likeCount = $db->query($query)->fetchArray(SQLITE3_NUM)[0];

    return <<<HTML
        <div style="
            padding: 1rem;">
            <div style="
                background-color: #fff;
                border-radius: 1rem;">
                <div style="
                    display: grid;
                    grid-template-columns: repeat(2, max-content) 1fr repeat(2, max-content);">
                    <div style="
                        display: flex;
                        align-items: center;
                        padding: 1rem;">
                        <img style="
                            width: 2rem;
                            height: 2rem;
                            border-radius: 50%;
                            object-fit: cover;"
                            src="uploads/{$author['avatar']}">
                    </div>
                    <a style="
                        display: flex;
                        align-items: center;
                        padding: 1rem;
                        padding-left: 0rem;"
                        href="user/?id={$author['id']}">
                        {$author["username"]}
                    </a>
                    <div></div>
                    <div style="
                        display: flex;
                        align-items: center;
                        padding: 1rem;
                        opacity: 0.5;">
                        {$visibilityRender}
                    </div>
                    {$deleteRender}
                </div>
                <div style="
                    padding: 1rem;
                    white-space: pre-wrap;">{$post["content"]}</div>
                {$imageRender}
                <div style="
                    display: grid;
                    grid-template-columns: repeat(4, max-content) 1fr max-content;">
                    {$likeRender}
                    <div style="
                        display: flex;
                        align-items: center;
                        padding: 1rem;
                        padding-left: 0rem;">
                        {$likeCount}
                    </div>
                    <a style="
                        display: flex;
                        align-items: center;
                        padding: 1rem;"
                        href="post/?id={$post['id']}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M280-400h400q17 0 28.5-11.5T720-440q0-17-11.5-28.5T680-480H280q-17 0-28.5 11.5T240-440q0 17 11.5 28.5T280-400Zm0-120h400q17 0 28.5-11.5T720-560q0-17-11.5-28.5T680-600H280q-17 0-28.5 11.5T240-560q0 17 11.5 28.5T280-520Zm0-120h400q17 0 28.5-11.5T720-680q0-17-11.5-28.5T680-720H280q-17 0-28.5 11.5T240-680q0 17 11.5 28.5T280-640ZM160-240q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v623q0 27-24.5 37.5T812-148l-92-92H160Z"/></svg>
                    </a>
                    <div style="
                        display: flex;
                        align-items: center;
                        padding: 1rem;
                        padding-left: 0rem;">
                        0
                    </div>
                    <div></div>
                    <div style="
                        display: flex;
                        align-items: center;
                        padding: 1rem;
                        color: #555;"
                        class="unix">
                        {$post["time"]}
                    </div>
                </div>
            </div>
        </div>
    HTML;
}

function renderComment($comment) {
    $db = new SQLite3("database.db");
    $user = getUser();
    
    $query = <<<SQL
        SELECT * FROM "users"
        WHERE "id" = '{$comment["user_id"]}'
    SQL;

    $author = $db->query($query)->fetchArray(SQLITE3_ASSOC);
    $renderDelete = "";

    if ($author["id"] == $user["id"]) {
        $renderDelete = <<<HTML
            <form style="
                padding: 1rem;"
                action="server.php"
                method="post"
                enctype="multipart/form-data">
                <button style="
                    background-color: transparent;
                    padding: 0rem;"
                    name="method"
                    value="delete_comment"
                    onclick="return confirm('Are you sure you want to delete this comment?')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm148.5-171.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5Z"/></svg>
                </button>
                <input type="hidden" name="comment_id" value="{$comment['id']}"/>
            </form>
        HTML;
    }
    
    return <<<HTML
        <div>
            <div style="
                display: grid;
                grid-template-columns: repeat(2, max-content) 1fr max-content;">
                <div style="
                    display: flex;
                    align-items: center;
                    padding: 1rem;">
                    <img style="
                        width: 2rem;
                        height: 2rem;
                        border-radius: 50%;
                        object-fit: cover;"
                        src="uploads/{$author['avatar']}">
                </div>
                <div style="
                    display: flex;
                    align-items: center;">
                    {$author["username"]}
                </div>
                <div></div>
                {$renderDelete}
            </div>
            <div style="
                padding: 1rem;">
                {$comment["content"]}
            </div>
            <div style="
                display: grid;
                grid-template-columns: 1fr max-content;">
                <div></div>
                <div style="
                    padding: 1rem;
                    color: #555;"
                    class="unix">
                    {$comment["time"]}
                </div>
            </div>
        </div>
    HTML;
}