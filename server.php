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
        WHERE "username" = :username
    SQL;

    $stmt = $db->prepare($query);
    $stmt->bindValue(":username", $_POST["username"]);
    $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($user != false) {
        alert("Username already exists.");
    }

    $query = <<<SQL
        SELECT *
        FROM "users"
        WHERE "email" = :email
    SQL;

    $stmt = $db->prepare($query);
    $stmt->bindValue(":email", $_POST["email"]);
    $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($user != false) {
        alert("Email already exists.");
    }

    $query = <<<SQL
        INSERT INTO "users" ("username", "email", "password")
        VALUES (:username, :email, :password)
    SQL;

    $stmt = $db->prepare($query);
    $stmt->bindValue(":username", $_POST["username"]);
    $stmt->bindValue(":email", $_POST["email"]);
    $stmt->bindValue(":password", password_hash($_POST["password"], PASSWORD_DEFAULT));
    $result = $stmt->execute();

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
        WHERE "username" = :username
    SQL;

    $stmt = $db->prepare($query);
    $stmt->bindValue(":username", $_POST["username"]);
    $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($user == false || !password_verify($_POST["password"], $user["password"])) {
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
        VALUES (:user_id, :content, :image, :visibility)
    SQL;

    $stmt = $db->prepare($query);
    $stmt->bindValue(":user_id", $_COOKIE["userId"]);
    $stmt->bindValue(":content", $_POST["content"]);
    $stmt->bindValue(":image", $filename);
    $stmt->bindValue(":visibility", $_POST["visibility"]);
    $result = $stmt->execute();

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
        WHERE "id" = :post_id
    SQL;

    $stmt = $db->prepare($query);
    $stmt->bindValue(":post_id", $_POST["post_id"]);
    $stmt->execute();
    header("Location: ./");
}

function like() {
    $db = new SQLite3("database.db");
    $user = getUser();

    // $query = <<<SQL
    //     INSERT INTO "likes" ("user_id", "post_id")
    //     VALUES ('{$user["id"]}', '{$_POST["post_id"]}');
    // SQL;

    // $result = $db->exec($query);

    $query = <<<SQL
        INSERT INTO "likes" ("user_id", "post_id")
        VALUES (:user_id, :post_id)
    SQL;

    $stmt = $db->prepare($query);
    $stmt->bindValue(":user_id", $user["id"]);
    $stmt->bindValue(":post_id", $_POST["post_id"]);
    $result = $stmt->execute();

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
        WHERE "user_id" = :user_id AND "post_id" = :post_id
    SQL;

    $stmt = $db->prepare($query);
    $stmt->bindValue(":user_id", $user["id"]);
    $stmt->bindValue(":post_id", $_POST["post_id"]);
    $result = $stmt->execute();

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
            SET "password" = :password
            WHERE "id" = :user_id
        SQL;

        $stmt = $db->prepare($query);
        $stmt->bindValue(":password", password_hash($_POST["newpassword"], PASSWORD_DEFAULT));
        $stmt->bindValue(":user_id", $user["id"]);
        $result = $stmt->execute();

        if ($result == false) {
            alert("Failed to change password.");
        }
    }

    if ($_FILES["avatar"]["error"] != 4) {
        $filename = uniqid("avatar_") . "." . pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES["avatar"]["tmp_name"], "uploads/" . $filename);

        $query = <<<SQL
            UPDATE "users"
            SET "avatar" = :avatar
            WHERE "id" = :user_id
        SQL;

        $stmt = $db->prepare($query);
        $stmt->bindValue(":avatar", $filename);
        $stmt->bindValue(":user_id", $user["id"]);
        $result = $stmt->execute();

        if ($result == false) {
            alert("Failed to change avatar.");
        }
    }

    if ($_POST["username"] != $user["username"]) {
        $query = <<<SQL
            SELECT * FROM "users"
            WHERE "username" = :username
        SQL;

        $stmt = $db->prepare($query);
        $stmt->bindValue(":username", $_POST["username"]);
        $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($user != false) {
            alert("Username already exists.");
        }

        $query = <<<SQL
            UPDATE "users"
            SET "username" = :username
            WHERE "id" = :user_id
        SQL;

        $stmt = $db->prepare($query);
        $stmt->bindValue(":username", $_POST["username"]);
        $stmt->bindValue(":user_id", $user["id"]);
        $result = $stmt->execute();

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