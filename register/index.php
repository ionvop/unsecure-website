<?php

chdir("../");
require_once "common.php";

?>

<html>
    <head>
        <title>
            Login | A Totally Secure Social Platform
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

            input {
                background-color: transparent;
                border-bottom: 1px solid #0af;
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
                            Register
                        </div>
                        <div style="
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);">
                            <div>
                                <div style="
                                    padding: 1rem;">
                                    Username:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <input name="username"
                                        placeholder="Username"
                                        required>
                                </div>
                                <div style="
                                    padding: 1rem;">
                                    Email:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <input type="email"
                                        name="email"
                                        placeholder="Email"
                                        required>
                                </div>
                            </div>
                            <div>
                                <div style="
                                    padding: 1rem;">
                                    Password:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <input type="password"
                                        name="password"
                                        placeholder="Password"
                                        required>
                                </div>
                                <div style="
                                    padding: 1rem;">
                                    Confirm password:
                                </div>
                                <div style="
                                    padding: 1rem;
                                    padding-top: 0rem;">
                                    <input type="password"
                                        name="repassword"
                                        placeholder="Confirm password"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div style="
                            padding: 1rem;
                            text-align: center;">
                            <button name="method"
                                value="register">
                                Register
                            </button>
                        </div>
                        <div style="
                            padding: 1rem;
                            padding-top: 0rem;
                            text-align: center;">
                            <a style="
                                text-decoration: underline;
                                color: initial;"
                                href="login/">
                                Login
                            </a>
                        </div>
                    </form>
                </div>
                <div></div>
            </div>
        </div>
        <script src="script.js"></script>
        <script>

        </script>
    </body>
</html>