<?php foreach ($errors as $error): ?>
    <div class="alert">
        <span class="closebtn">&times;</span>
        <strong>Error!</strong> <?php echo $error ?>
    </div>
<?php endforeach; ?>
<div class="registration">
    <script type="text/javascript">
        function makeDirty(input) {
            if (input.classList.contains("pristine")) {
                input.classList.remove("pristine");
            }
        }

        function validateRepeatPassword() {
            var input = document.getElementById("repeat-password");

            if (input.value === "") {
                input.setAttribute("placeholder", "REQUIRED");

                if (!input.classList.contains("invalid")) {
                    input.classList.add("invalid");
                    classChange();
                }
            } else {
                var pass = document.getElementById("password");

                if (input.value !== pass.value) {
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

                            if (JSON.parse(this.responseText)["exists"]) {
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
            var firstnameinput = document.getElementById("firstname");
            var lastnameinput = document.getElementById("lastname");
            var passwordinput = document.getElementById("password");
            var repeatpasswordinput = document.getElementById("repeat-password");
            var emailinput = document.getElementById("email");
            var phoneinput = document.getElementById("phone");

            if (userinput.classList.contains("invalid") ||
                firstnameinput.classList.contains("invalid") ||
                lastnameinput.classList.contains("invalid") ||
                passwordinput.classList.contains("invalid") ||
                repeatpasswordinput.classList.contains("invalid") ||
                emailinput.classList.contains("invalid") ||
                phoneinput.classList.contains("invalid")) {
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

        function validateRequired(input) {
            if (input.value === "") {
                input.setAttribute("placeholder", "REQUIRED");

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
    <form method="post" id="registration-form" novalidate="novalidate" class="required input-focus" action="/register/finalize">
        <div class="title primary">
            <span>Registration</span>
        </div>
        <div id="registration-input-container">
            <div class="errors">
                <noscript>
                    Javascript is disabled. Please enable it to use this site.
                </noscript>
            </div>
            <!-- 1. USERNAME -->
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
            <!-- 2. FIRST NAME -->
            <div class="input-group">
                <label id="firstname-label" class="control-label" for="firstname">Firstname</label>
                <input id="firstname"
                       class="invalid pristine"
                       name="firstname"
                       type="text"
                       autocorrect="off"
                       spellcheck="false"
                       placeholder="First name"
                       oninput="validateRequired(this)"
                       onblur="makeDirty(this); validateRequired(this)"
                       value="<?php echo $firstname ?>">
            </div>
            <!-- 3. LAST NAME -->
            <div class="input-group">
                <label id="lastname-label" class="control-label" for="lastname">Lastname</label>
                <input id="lastname"
                       class="invalid pristine"
                       name="lastname"
                       type="text"
                       autocorrect="off"
                       spellcheck="false"
                       placeholder="Last name"
                       oninput="validateRequired(this)"
                       onblur="makeDirty(this); validateRequired(this)"
                       value="<?php echo $lastname ?>">
            </div>
            <!-- 4. PASSWORD -->
            <div class="input-group">
                <label id="password-label" class="control-label" for="password">Password</label>
                <input id="password"
                       class="invalid pristine"
                       name="password"
                       type="password"
                       autocorrect="off"
                       spellcheck="false"
                       placeholder="Password"
                       oninput="validateRequired(this); validateRepeatPassword()"
                       onblur="makeDirty(this); validateRequired(this)"
                       value="<?php echo $password ?>">
            </div>
            <!-- 4. REPEAT PASSWORD -->
            <div class="input-group">
                <label id="repeat-password-label" class="control-label" for="repeat-password">RepeatPassword</label>
                <input id="repeat-password"
                       class="invalid pristine"
                       name="repeat_password"
                       type="password"
                       autocorrect="off"
                       spellcheck="false"
                       placeholder="Repeat password"
                       oninput="validateRepeatPassword()"
                       onblur="makeDirty(this); validateRepeatPassword()"
                       value="<?php echo $repeat_password ?>">
            </div>
            <!-- 5. EMAIL -->
            <div class="input-group">
                <label id="email-label" class="control-label" for="email">Email</label>
                <input id="email"
                       class="invalid pristine"
                       name="email"
                       type="text"
                       autocorrect="off"
                       spellcheck="false"
                       placeholder="Email"
                       oninput="validateRequired(this)"
                       onblur="makeDirty(this); validateRequired(this)"
                       value="<?php echo $email ?>">
            </div>
            <!-- 6. PHONE -->
            <div class="input-group">
                <label id="phone-label" class="control-label" for="phone">Phone</label>
                <input id="phone"
                       class="invalid pristine"
                       name="phone"
                       type="text"
                       autocorrect="off"
                       spellcheck="false"
                       placeholder="Phone"
                       oninput="validateRequired(this)"
                       onblur="makeDirty(this); validateRequired(this)"
                       value="<?php echo $phone ?>">
            </div>
        </div>
        <div class="submit">
            <button type="submit" id="submit" class="primary" disabled>Register</button>
        </div>
        <script type="text/javascript">
            var userinput = document.getElementById("username");
            var firstnameinput = document.getElementById("firstname");
            var lastnameinput = document.getElementById("lastname");
            var passwordinput = document.getElementById("password");
            var repeatpasswordinput = document.getElementById("repeat-password");
            var emailinput = document.getElementById("email");
            var phoneinput = document.getElementById("phone");

            if (userinput.value !== "") {
                userinput.classList.remove("pristine");

                validateName();
            }

            if (firstnameinput.value !== "") {
                firstnameinput.classList.remove("pristine");

                validateRequired(firstnameinput);
            }

            if (lastnameinput.value !== "") {
                lastnameinput.classList.remove("pristine");

                validateRequired(lastnameinput);
            }

            if (passwordinput.value !== "") {
                passwordinput.classList.remove("pristine");

                validateRequired(passwordinput);
            }

            if (repeatpasswordinput.value !== "") {
                repeatpasswordinput.classList.remove("pristine");

                validateRepeatPassword();
            }

            if (emailinput.value !== "") {
                emailinput.classList.remove("pristine");

                validateRequired(emailinput);
            }

            if (phoneinput.value !== "") {
                phoneinput.classList.remove("pristine");

                validateRequired(phoneinput);
            }
        </script>
    </form>
</div>