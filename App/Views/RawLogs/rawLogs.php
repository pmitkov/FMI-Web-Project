<div class="container">
    <div class="row">Please choose a server to display data:</div>
    <div class="custom-select" id="custom-select">
        <select>
            <?php foreach ($hosts as $index => $host): ?>
                <option value="<?php echo $index ?>"><?php echo $host['server'] . "(" . $host['alias'] . ")" ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($number_of_logs == 0): ?>
        <h3>You don't have any logs for this host, please select another one or import them from the button above</h3>
    <?php else: ?>
        <table class="custom-table">
            <tr>
                <th>Remote Address</th>
                <th>Remote User</th>
                <th>Local Time</th>
                <th>Request</th>
                <th>Status</th>
                <th>Body Bytes Send</th>
                <th>HTTP Referer</th>
                <th>HTTP User Agent</th>
            </tr>
            <?php foreach ($page as $log): ?>
                <tr>
                    <td><?php echo $log["remoteAddr"] ?></td>
                    <td><?php echo $log["remoteUser"] ?></td>
                    <td><?php echo $log["timeLocal"] ?></td>
                    <td><?php echo $log["request"] ?></td>
                    <td><?php echo $log["status"] ?></td>
                    <td><?php echo $log["bodyBytesSend"] ?></td>
                    <td><?php echo $log["httpReferer"] ?></td>
                    <td><?php echo $log["httpUserAgent"] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <script>
        function styleSelectBox() {
            var selectDiv = document.getElementById("custom-select");
            var selectElement = selectDiv.getElementsByTagName("select")[0];
            var replacement = document.createElement("div");

            replacement.setAttribute("class", "select-selected");
            replacement.innerHTML = selectElement.options[selectElement.selectedIndex].innerHTML;
            selectDiv.appendChild(replacement);

            var hidden = document.createElement("div");
            hidden.setAttribute("class", "select-items select-hide");

            for (var i = 0; i < selectElement.length; i++) {
                var fake = document.createElement("div");
                fake.innerHTML = selectElement.options[i].innerHTML;
                fake.addEventListener("click", function (e) {

                    var s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                    var h = this.parentNode.previousSibling;

                    for (var i = 0; i < s.length; i++) {
                        if (s.options[i].innerHTML == this.innerHTML) {
                            var server = this.innerHTML.split("(")[0];

                            window.location.href = "/raw?server=" + server;
                            s.selectedIndex = i;
                            h.innerHTML = this.innerHTML;
                            var y = this.parentNode.getElementsByClassName("same-as-selected");

                            for (var k = 0; k < y.length; k++) {
                                y[k].removeAttribute("class");
                            }

                            this.setAttribute("class", "same-as-selected");
                            break;
                        }
                    }
                    h.click();
                });

                hidden.appendChild(fake);
            }

            selectDiv.appendChild(hidden);

            replacement.addEventListener("click", function (e) {
                e.stopPropagation();
                closeAllSelect(this);
                this.nextSibling.classList.toggle("select-hide");
                this.classList.toggle("select-arrow-active");
            });
        }

        function closeAllSelect(element) {
            var arrNo = [];
            var selectItems = document.getElementsByClassName("select-items");
            var selectedItems = document.getElementsByClassName("select-selected");

            for (var i = 0; i < selectedItems.length; i++) {
                if (element === selectedItems[i]) {
                    arrNo.push(i);
                } else {
                    selectedItems[i].classList.remove("select-arrow-active");
                }
            }

            for (var i = 0; i < selectItems.length; i++) {
                if (arrNo.indexOf(i)) {
                    selectItems[i].classList.add("select-hide");
                }
            }
        }

        document.addEventListener("click", closeAllSelect);

        styleSelectBox();
    </script>
    <div class="pagination-container">
        <div class="pagination">
            <a href="#">&laquo;</a>
            <a href="#">1</a>
            <a href="#" class="active">2</a>
            <a href="#">3</a>
            <a href="#">4</a>
            <a href="#">5</a>
            <a href="#">6</a>
            <a href="#">&raquo;</a>
        </div>
    </div>
</div>
