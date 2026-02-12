<?php

require_once "common.php";

if (isset($_POST["method"])) {
    switch ($_POST["method"]) {
        case "register":
            register();
            break;
        case "login":
            login();
            break;
        case "logout":
            logout();
            break;
        case "post":
            post();
            break;
        case "delete_post":
            deletePost();
            break;
        case "like":
            like();
            break;
        case "unlike":
            unlike();
            break;
        case "edit_profile":
            editProfile();
            break;
        case "comment":
            comment();
            break;
        case "delete_comment":
            deleteComment();
            break;
        case "follow":
            follow();
            break;
        case "unfollow":
            unfollow();
            break;
        default:
            defaultMethod();
            break;
    }
} else {
    defaultMethod();
}

// Security is intentionally bad for demonstration about vulnerabilities

function register() {
    $db = new SQLite3("database.db");

    if ($_POST["username"] == "" || $_POST["email"] == "" || $_POST["password"] == "") {
        alert("All fields are required.");
    }

    if ($_POST["password"] != $_POST["repassword"]) {
        alert("Passwords do not match.");
    }

    $query = <<<SQL
        SELECT *
        FROM "users"
        WHERE "username" = '{$_POST["username"]}'
    SQL;

    $user = $db->query($query)->fetchArray(SQLITE3_ASSOC);

    if ($user != false) {
        alert("Username already exists.");
    }

    $query = <<<SQL
        SELECT *
        FROM "users"
        WHERE "email" = '{$_POST["email"]}'
    SQL;

    $user = $db->query($query)->fetchArray(SQLITE3_ASSOC);

    if ($user != false) {
        alert("Email already exists.");
    }

    $query = <<<SQL
        INSERT INTO "users" ("username", "email", "password")
        VALUES ('{$_POST["username"]}', '{$_POST["email"]}', '{$_POST["password"]}');
    SQL;

    $result = $db->exec($query);

    if ($result == false) {
        alert("Failed to register.");
    }

    $userId = $db->lastInsertRowID();
    setcookie("userId", $userId, time() + 86400);
    header("Location: ./");
}

function login() {
    $db = new SQLite3("database.db");

    $query = <<<SQL
        SELECT *
        FROM "users"
        WHERE "username" = '{$_POST["username"]}' AND "password" = '{$_POST["password"]}'
    SQL;

    $user = $db->query($query)->fetchArray(SQLITE3_ASSOC);

    if ($user == false) {
        alert("Invalid username or password.");
    }

    setcookie("userId", $user["id"], time() + 86400);
    header("Location: ./");
}

function logout() {
    setcookie("userId", "", time() - 3600);
    header("Location: ./");
}

function post() {
    $db = new SQLite3("database.db");
    $filename = "";

    if ($_FILES["image"]["error"] != 4) {
        $filename = uniqid("image_") . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $filename);
    }

    $query = <<<SQL
        INSERT INTO "posts" ("user_id", "content", "image", "visibility")
        VALUES ('{$_COOKIE["userId"]}', '{$_POST["content"]}', '{$filename}', '{$_POST["visibility"]}');
    SQL;

    $result = $db->exec($query);

    if ($result == false) {
        alert("Failed to post.");
    }

    header("Location: ./");
}

function deletePost() {
    $db = new SQLite3("database.db");

    $query = <<<SQL
        DELETE
        FROM "posts"
        WHERE "id" = '{$_POST["post_id"]}'
    SQL;

    $result = $db->exec($query);
    header("Location: ./");
}

function like() {
    $db = new SQLite3("database.db");
    $user = getUser();

    $query = <<<SQL
        INSERT INTO "likes" ("user_id", "post_id")
        VALUES ('{$user["id"]}', '{$_POST["post_id"]}');
    SQL;

    $result = $db->exec($query);

    if ($result == false) {
        alert("Failed to like.");
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

function unlike() {
    $db = new SQLite3("database.db");
    $user = getUser();

    $query = <<<SQL
        DELETE FROM "likes"
        WHERE "user_id" = '{$user["id"]}' AND "post_id" = '{$_POST["post_id"]}'
    SQL;

    $result = $db->exec($query);

    if ($result == false) {
        alert("Failed to unlike.");
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

function editProfile() {
    $db = new SQLite3("database.db");
    $user = getUser();

    if ($_POST["newpassword"] != "") {
        if ($_POST["oldpassword"] != $user["password"]) {
            alert("Incorrect password.");
        }

        if ($_POST["newpassword"] != $_POST["repassword"]) {
            alert("Passwords do not match.");
        }

        $query = <<<SQL
            UPDATE "users"
            SET "password" = '{$_POST["newpassword"]}'
            WHERE "id" = '{$user["id"]}'
        SQL;

        $result = $db->exec($query);

        if ($result == false) {
            alert("Failed to change password.");
        }
    }

    if ($_FILES["avatar"]["error"] != 4) {
        $filename = uniqid("avatar_") . "." . pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES["avatar"]["tmp_name"], "uploads/" . $filename);

        $query = <<<SQL
            UPDATE "users"
            SET "avatar" = '{$filename}'
            WHERE "id" = '{$user["id"]}'
        SQL;

        $result = $db->exec($query);

        if ($result == false) {
            alert("Failed to change avatar.");
        }
    }

    if ($_POST["username"] != $user["username"]) {
        $query = <<<SQL
            SELECT * FROM "users"
            WHERE "username" = '{$_POST["username"]}'
        SQL;

        $user = $db->query($query)->fetchArray(SQLITE3_ASSOC);

        if ($user != false) {
            alert("Username already exists.");
        }

        $query = <<<SQL
            UPDATE "users"
            SET "username" = '{$_POST["username"]}'
            WHERE "id" = '{$user["id"]}'
        SQL;

        $result = $db->exec($query);

        if ($result == false) {
            alert("Failed to change username.");
        }
    }

    if ($_POST["email"] != $user["email"]) {
        $query = <<<SQL
            SELECT * FROM "users"
            WHERE "email" = '{$_POST["email"]}'
        SQL;

        $user = $db->query($query)->fetchArray(SQLITE3_ASSOC);

        if ($user != false) {
            alert("Email already exists.");
        }

        $query = <<<SQL
            UPDATE "users"
            SET "email" = '{$_POST["email"]}'
            WHERE "id" = '{$user["id"]}'
        SQL;

        $result = $db->exec($query);

        if ($result == false) {
            alert("Failed to change email.");
        }
    }

    $query = <<<SQL
        UPDATE "users"
        SET "description" = '{$_POST["description"]}'
        WHERE "id" = '{$user["id"]}'
    SQL;

    $result = $db->exec($query);

    if ($result == false) {
        alert("Failed to edit profile.");
    }

    header("Location: user/?id=" . $user["id"]);
}

function comment() {
    $db = new SQLite3("database.db");
    $user = getUser();

    $query = <<<SQL
        INSERT INTO "comments" ("user_id", "post_id", "content")
        VALUES ('{$user["id"]}', '{$_POST["post_id"]}', '{$_POST["content"]}');
    SQL;

    $result = $db->exec($query);

    if ($result == false) {
        alert("Failed to comment.");
    }

    header("Location: post/?id=" . $_POST["post_id"]);
}

function deleteComment() {
    $db = new SQLite3("database.db");
    $user = getUser();

    $query = <<<SQL
        DELETE FROM "comments"
        WHERE "id" = '{$_POST["comment_id"]}' AND "user_id" = '{$user["id"]}'
    SQL;

    $result = $db->exec($query);

    if ($result == false) {
        alert("Failed to delete comment.");
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

function follow() {
    $db = new SQLite3("database.db");
    $user = getUser();

    $query = <<<SQL
        INSERT INTO "follows" ("user_id", "target_id")
        VALUES ('{$user["id"]}', '{$_POST["target_id"]}');
    SQL;

    $result = $db->exec($query);

    if ($result == false) {
        alert("Failed to follow.");
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

function unfollow() {
    $db = new SQLite3("database.db");
    $user = getUser();

    $query = <<<SQL
        DELETE FROM "follows"
        WHERE "user_id" = '{$user["id"]}' AND "target_id" = '{$_POST["target_id"]}'
    SQL;

    $result = $db->exec($query);

    if ($result == false) {
        alert("Failed to unfollow.");
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

function defaultMethod() {
    breakpoint([
        "post" => $_POST,
        "files" => $_FILES
    ]);
}