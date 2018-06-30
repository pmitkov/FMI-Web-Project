<div class="container">
    <div class="row">Please choose a server to display data:</div>
    <div class="custom-select" id="custom-select">
        <select>
            <?php foreach ($hosts as $index => $host): ?>
                <option <?php if ($host['server'] == $initial_host) echo "selected='selected'" ?> value="<?php echo $index ?>"><?php echo $host['server'] . "(" . $host['alias'] . ")" ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="custom-button" onclick="location.href='/raw/refresh/' + document.getElementsByClassName('select-selected')[0].innerText.split('(')[0];">Refresh Logs</button>
    <?php if ($number_of_logs == 0): ?>
        <h3>You don't have any logs for this host, please select another one or import them from the button above</h3>
    <?php else: ?>
        <table class="custom-table">
            <thead>
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
            </thead>
            <tbody>
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
            </tbody>
        </table>
    <?php endif; ?>
    <script>
        var currentPage = 1;
        var numberOfPages = <?php echo $number_of_pages ?>;
        var pageSize = <?php echo $page_size ?>;

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
    <?php if ($number_of_logs != 0): ?>
        <div class="pagination-container">
            <div class="pagination">
                <a class="slider" onclick="backwardSlide()">&laquo;</a>
                <a class="active pagination-item">1</a>
                <?php for ($index = 2; $index <= min(6, $number_of_pages); $index++): ?>
                    <a class="pagination-item"><?php echo $index ?></a>
                <?php endfor; ?>
                <a class="slider" onclick="forwardSlide()">&raquo;</a>
            </div>
        </div>
    <?php endif; ?>
    <script type="text/javascript">
        var paginationItems = document.getElementsByClassName("pagination-item");

        if (numberOfPages != 0) {
            updateSliders();
        }

        for (var i = 0; i < paginationItems.length; i++) {
            paginationItems[i].addEventListener("click", function () {
                if (this.innerText != currentPage) {
                    for (var j = 0; j < paginationItems.length; j++) {
                        if (paginationItems[j].classList.contains("active")) {
                            paginationItems[j].classList.remove("active");
                        }
                    }

                    this.classList.add("active");
                    currentPage = parseInt(this.innerText);

                    var request = new ajaxRequest();

                    var serverName = document.getElementsByClassName("select-selected")[0].innerText.split("(")[0];

                    request.open("get", "/raw/" + serverName + "/" + currentPage, true);
                    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    request.onreadystatechange = function () {
                        if (this.readyState === 4) {
                            if (this.status === 200) {
                                if (this.responseText != null) {
                                    var table = document.getElementsByClassName("custom-table")[0];

                                    var newRows = JSON.parse(this.responseText);

                                    /* pad the table to 10 records */
                                    for (var i = table.rows.length; i < pageSize + 1; i++) {
                                        var newRow = table.rows[1].cloneNode(true);
                                        table.appendChild(newRow);
                                    }

                                    /* update table with new records */
                                    for (var i = 0; i < newRows.length; i++) {
                                        var tableData = table.rows[i+1].getElementsByTagName("td");

                                        tableData[0].innerText = newRows[i]["remoteAddr"];
                                        tableData[1].innerText = newRows[i]["remoteUser"];
                                        tableData[2].innerText = newRows[i]["timeLocal"];
                                        tableData[3].innerText = newRows[i]["request"];
                                        tableData[4].innerText = newRows[i]["status"];
                                        tableData[5].innerText = newRows[i]["bodyBytesSend"];
                                        tableData[6].innerText = newRows[i]["httpReferer"];
                                        tableData[7].innerText = newRows[i]["httpUserAgent"];
                                    }

                                    console.log(newRows.length);

                                    for (var i = newRows.length + 1; i <= pageSize; i++) {
                                        table.deleteRow(newRows.length + 1);
                                    }
                                } else {
                                    console.log("No response");
                                }
                            } else {
                                console.log("Error fetching data");
                            }
                        }
                    };

                    request.send();

                    updateSliders();
                }
            });
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

        function updateSliders() {
            var backwardSlider = document.getElementsByClassName("slider")[0];
            var forwardSlider = document.getElementsByClassName("slider")[1];

            if (currentPage == 1) {
                backwardSlider.classList.add("not-active");
            }

            if (currentPage != 1 && backwardSlider.classList.contains("not-active")) {
                backwardSlider.classList.remove("not-active");
            }

            if (currentPage == numberOfPages) {
                forwardSlider.classList.add("not-active");
            }

            if (currentPage != numberOfPages && forwardSlider.classList.contains("not-active")) {
                forwardSlider.classList.remove("not-active");
            }
        }

        function forwardSlide() {
            var selected;

            if (paginationItems[paginationItems.length - 1].innerText == numberOfPages) {
                for (var i = 0; i < paginationItems.length; i++) {
                    if (paginationItems[i].innerText == currentPage + 1) {
                        selected = paginationItems[i];
                    }
                }
            } else {
                for (var i = 0; i < paginationItems.length; i++) {
                    paginationItems[i].innerText++;

                    if (paginationItems[i].innerText == currentPage + 1) {
                        selected = paginationItems[i];
                    }
                }
            }

            updateSliders();

            selected.click();
        }

        function backwardSlide() {
            var selected;

            if (paginationItems[0].innerText == 1) {
                for (var i = 0; i < paginationItems.length; i++) {
                    if (paginationItems[i].innerText == currentPage - 1) {
                        selected = paginationItems[i];
                    }
                }
            } else {
                for (var i = 0; i < paginationItems.length; i++) {
                    paginationItems[i].innerText--;

                    if (paginationItems[i].innerText == currentPage - 1) {
                        selected = paginationItems[i];
                    }
                }
            }

            updateSliders();

            selected.click();
        }

    </script>
</div>
