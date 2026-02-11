<?php

chdir("../../");
require_once "common.php";
$db = new SQLite3("database.db");
$user = getUser();

?>

<html>
    <head>
        <title>
            Edit | A Totally Secure Social Platform
        </title>
        <base href="../../">
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            button {
                background-color: #0af;
                color: #fff;
                border-radius: 1rem;
            }

            input {
                background-color: transparent;
                border-bottom: 1px solid #0af;
            }

            textarea {
                background-color: #eee;
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
                display: grid;
                grid-template-columns: 1fr max-content 1fr;
                background-color: #eef;">
                <div></div>
                <div style="
                    padding: 1rem;">
                    <form style="
                        padding: 1rem;
                        background-color: #fff;
                        border-radius: 1rem;"
                        action="server.php"
                        method="post"
                        enctype="multipart/form-data">
                        <div style="
                            padding: 1rem;
                            text-align: center;
                            font-size: 2rem;
                            font-weight: bold;">
                            Edit Profile
                        </div>
                        <div style="
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);">
                            <div style="
                                padding: 1rem;">
                                <div style="
                                    padding: 1rem;">
                                    Avatar:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <img style="
                                        width: 10rem;
                                        height: 10rem;
                                        border-radius: 50%;
                                        object-fit: cover;
                                        cursor: pointer;"
                                        src="uploads/<?= $user["avatar"] ?>"
                                        id="imgPreview">
                                    <input style="
                                        display: none;"
                                        type="file"
                                        name="avatar"
                                        accept="image/*"
                                        id="inputImage">
                                </div>
                                <div style="
                                    padding: 1rem;">
                                    Username:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <input name="username"
                                        placeholder="Username"
                                        value="<?= $user["username"] ?>"
                                        required>
                                </div>
                                <div style="
                                    padding: 1rem;">
                                    Description:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <textarea name="description"
                                        placeholder="Description"
                                        required><?= $user["description"] ?></textarea>
                                </div>
                            </div>
                            <div style="
                                padding: 1rem;">
                                <div style="
                                    padding: 1rem;
                                    text-align: center;
                                    font-weight: bold;
                                    font-size: 1.5rem;">
                                    Change Password
                                </div>
                                <div style="
                                    padding: 1rem;">
                                    Old password:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <input name="oldpassword"
                                        placeholder="Old password">
                                </div>
                                <div style="
                                    padding: 1rem;">
                                    New password:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <input name="newpassword"
                                        placeholder="New password">
                                </div>
                                <div style="
                                    padding: 1rem;">
                                    Confirm password:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <input name="repassword"
                                        placeholder="Confirm password">
                                </div>
                            </div>
                        </div>
                        <div style="
                            padding: 1rem;
                            text-align: center;">
                            <button name="method"
                                value="edit_profile">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
                <div></div>
            </div>
        </div>
        <script src="script.js"></script>
        <script>
            const imgPreview = document.getElementById("imgPreview");
            const inputImage = document.getElementById("inputImage");

            imgPreview.onclick = () => {
                inputImage.click();
            }

            inputImage.onchange = () => {
                if (inputImage.files.length > 0) {
                    imgPreview.setAttribute("src", URL.createObjectURL(inputImage.files[0]));
                } else {
                    imgPreview.setAttribute("src", "uploads/<?= $user["avatar"] ?>");
                }
            }
        </script>
    </body>
</html>