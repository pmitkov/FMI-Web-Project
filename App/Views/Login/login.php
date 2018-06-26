<?php if ($error): ?>
    <div class="alert">
        <span class="closebtn">&times;</span>
        <strong>Error!</strong> <?php echo $error ?>
    </div>
<?php endif ?>
<div class="login">
    <script type="text/javascript">
        function makeDirty(input) {
            if (input.classList.contains("pristine")) {
                input.classList.remove("pristine");
            }
        }

        function validatePassword() {
            var input = document.getElementById("password");

            if (input.value === "") {
                input.setAttribute("placeholder", "REQUIRED");

                if (!input.classList.contains("invalid")) {
                    input.classList.add("invalid");
                    classChange();
                }
            } else if (input.value.length < 5) {
                if (!input.classList.contains("invalid")) {
                    input.classList.add("invalid");
                    classChange();
                }
            } else {
                if (input.classList.contains("invalid")) {
                    input.classList.remove("invalid");
                    classChange();
                }
            }
        }

        function validateName() {
            var input = document.getElementById("username");

            if (input.value === "") {
                input.setAttribute("placeholder", "REQUIRED");

                if (!input.classList.contains("invalid")) {
                    input.classList.add("invalid");
                    classChange();
                }
            } else {
                checkIfNameExists(input.value);
            }
        }

        function checkIfNameExists(name) {
            var params = "username=" + name;

            var request = new ajaxRequest();

            request.open("post", "/login/check", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            request.onreadystatechange = function() {
                if (this.readyState === 4) {
                    if (this.status === 200) {
                        if (this.responseText != null) {
                            var input = document.getElementById("username");

                            if (!JSON.parse(this.responseText)["exists"]) {
                                if (!input.classList.contains("invalid")) {
                                    input.classList.add("invalid");
                                    classChange();
                                }
                            } else {
                                if (input.classList.contains("invalid")) {
                                    input.classList.remove("invalid");
                                    classChange();
                                }
                            }
                        } else {
                            console.log("No response");
                        }
                    } else {
                        console.log("Error fetching data");
                    }
                }
            };

            request.send(params);
        }

        function classChange() {
            var userinput = document.getElementById("username");
            var passinput = document.getElementById("password");

            if (userinput.classList.contains("invalid") || passinput.classList.contains("invalid")) {
                document.getElementById("submit").disabled = true;
            } else {
                document.getElementById("submit").disabled = false;
            }
        }

        function ajaxRequest() {
            try {
                var request = new XMLHttpRequest();
            } catch (e1) {
                try {
                    request = new ActiveXObject("Msxml2.XMLHTPP");
                } catch (e2) {
                    try {
                        request = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e3) {
                        request = false;
                    }
                }
            }

            return request;
        }

        var close = document.getElementsByClassName("closebtn");

        for (var i = 0; i < close.length; i++) {
            close[i].onclick = function() {
                var div = this.parentElement;
                div.style.opacity = "0";
                setTimeout(function() {
                    div.style.display = "none";
                }, 600);
            }
        }
    </script>
    <form method="post" id="login-form" novalidate="novalidate" class="required input-focus" action="/login/finalize">
        <div class="title primary">
            <span>Login</span>
        </div>
        <div id="login-input-container">
            <div class="errors">
                <noscript>
                    Javascript is disabled. Please enable it to use this site.
                </noscript>
            </div>
            <div class="input-group">
                <label id="username-label" class="control-label" for="username">Username</label>
                <input id="username"
                       class="invalid pristine"
                       name="username"
                       type="text"
                       autocorrect="off"
                       spellcheck="false"
                       placeholder="Username"
                       oninput="validateName()"
                       onblur="makeDirty(this); validateName()"
                       value="<?php echo $username ?>">
            </div>
            <div class="input-group">
                <label id="password-label" class="control-label" for="password">Password</label>
                <input id="password"
                       class="invalid pristine"
                       name="password"
                       type="password"
                       autocorrect="off"
                       spellcheck="false"
                       placeholder="Password"
                       oninput="validatePassword()"
                       onblur="makeDirty(this); validatePassword()"
                       value="<?php echo $password ?>">
            </div>
        </div>
        <div class="submit">
            <button type="submit" id="submit" class="primary" disabled>Login</button>
        </div>
        <script type="text/javascript">
            var userinput = document.getElementById("username");
            var passinput = document.getElementById("password");

            if (userinput.value !== "") {
                userinput.classList.remove("pristine");

                validateName();
            }

            if (passinput.value !== "") {
                passinput.classList.remove("pristine");

                validatePassword();
            }
        </script>
    </form>
</div>