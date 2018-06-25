<?php if ($error): ?>
    <h3><?php echo $error ?></h3>
<?php endif ?>
<div class="registration">
    <script type="text/javascript">
        function validatePassword() {
            var input = document.getElementById("password");

            if (input.classList.contains("pristine")) {
                input.classList.remove("pristine");
            }

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

            if (input.classList.contains("pristine")) {
                input.classList.remove("pristine");
            }

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
            params = "username=" + name;

            request = new ajaxRequest();

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
            userinput = document.getElementById("username");
            passinput = document.getElementById("password");

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
    </script>
    <form method="post" id="registration-form" novalidate="novalidate" class="required input-focus" action="/login/finalize">
        <div class="title primary">
            <span>Login</span>
        </div>
        <div id="registration-input-container">
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
                       onblur="validateName()"
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
                       onblur="validatePassword()"
                       value="<?php echo $password ?>">
            </div>
        </div>
        <div class="submit">
            <button type="submit" id="submit" class="primary" disabled>Login</button>
        </div>
    </form>
</div>