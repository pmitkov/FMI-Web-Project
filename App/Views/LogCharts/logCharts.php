<div class="container">
    <div class="row">Please choose a server to display data:</div>
    <div class="custom-select" id="custom-select">
        <select>
            <?php foreach ($hosts as $index => $host): ?>
                <option <?php if ($host['server'] == $initial_host) echo "selected='selected'" ?> value="<?php echo $index ?>"><?php echo $host['server'] . "(" . $host['alias'] . ")" ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($number_of_logs == 0): ?>
        <h3>You don't have any logs for this host, please select another one or import them from the button above</h3>
    <?php else: ?>
        <canvas class="chart" id="status_summary" width="400px" height="400px"></canvas>
        <canvas class="chart" id="day_of_week_summary" width="400px" height="400px"></canvas>
        <canvas class="chart" id="hour_summary" width="650px" height="400px"></canvas>
        <canvas class="chart" id="month_summary" width="400px" height="400px"></canvas>
        <canvas class="chart" id="served_files_summary" width="600px" height="600px"></canvas>
        <canvas class="chart" id="request_verb_summary" width="400px" height="400px"></canvas>
    <?php endif; ?>
    <script>
        var statusSummary = <?php echo $status_summary ?>;
        var dayOfWeekSummary = <?php echo $day_of_week_summary ?>;
        var hourSummary = <?php echo $hour_summary ?>;
        var monthSummary = <?php echo $month_summary ?>;
        var servedFilesSummary = <?php echo $served_files_summary ?>;
        var requestVerbSummary = <?php echo $request_verb_summary ?>;

        function initializeStatusSummaryChart() {
            var ctx = document.getElementById("status_summary").getContext("2d");

            var counts = [];
            var statuses = [];

            for (var i = 0; i < statusSummary.length; i++) {
                counts.push(statusSummary[i]["count"]);
                statuses.push(statusSummary[i]["status"]);
            }

            var statusSummaryChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: statuses,
                    datasets: [{
                        data: counts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: false,
                    legend: {
                        labels: {
                            fontColor: 'white',
                            fontSize: 20,
                        }
                    },
                    title: {
                        display: true,
                        text: 'Status Codes Summary',
                        fontColor: 'white',
                        fontSize: 25
                    }
                }
            });
        }

        function initializeDayOfWeekSummaryChart() {
            var ctx = document.getElementById("day_of_week_summary").getContext("2d");

            var counts = [0, 0, 0, 0, 0, 0, 0];
            var labels = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

            for (var i = 0; i < dayOfWeekSummary.length; i++) {
                switch(dayOfWeekSummary[i]["dayname"]) {
                    case "Monday":
                        counts[0] = dayOfWeekSummary[i]["count"];
                        break;
                    case "Tuesday":
                        counts[1] = dayOfWeekSummary[i]["count"];
                        break;
                    case "Wednesday":
                        counts[2] = dayOfWeekSummary[i]["count"];
                        break;
                    case "Thursday":
                        counts[3] = dayOfWeekSummary[i]["count"];
                        break;
                    case "Friday":
                        counts[4] = dayOfWeekSummary[i]["count"];
                        break;
                    case "Saturday":
                        counts[5] = dayOfWeekSummary[i]["count"];
                        break;
                    case "Sunday":
                        counts[6] = dayOfWeekSummary[i]["count"];
                        break;
                }
            }

            var dayOfWeekSummaryChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(23, 159, 64, 0.2)',
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(23, 159, 64, 1)',
                        ],
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: false,
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Day Of Week Summary Chart',
                        fontColor: 'white',
                        fontSize: 25
                    }
                }
            });
        }

        function getRandom(min, max) {
            return Math.round(Math.random() * (max - min) + min);
        }

        function initializeHourSummaryChart() {
            var ctx = document.getElementById("hour_summary").getContext("2d");

            var counts = [];
            var colors = [];
            var colorsBorder = [];
            var labels = [];

            for (var i = 0; i < 24; i++) {
                counts.push(0);
                var color = "rgba(" + getRandom(0, 256) + "," + getRandom(0, 256) + "," + getRandom(0, 256);
                colors.push(color + ", 0.2)");
                colorsBorder.push(color + ", 1)");
                labels.push(i);
            }

            for (var i = 0; i < hourSummary.length; i++) {
                counts[hourSummary[i]["hour"]] = hourSummary[i]["count"];
            }

            var dayOfWeekSummaryChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        borderWidth: 3,
                        backgroundColor: colors,
                        borderColor: colorsBorder
                    }]
                },
                options: {
                    responsive: false,
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Hour Summary Chart',
                        fontColor: 'white',
                        fontSize: 25
                    }
                }
            });
        }

        function initializeMonthSummaryChart() {
            var ctx = document.getElementById("month_summary").getContext("2d");

            var counts = [];
            var colors = [];
            var colorsBorder = [];
            var labels = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];

            for (var i = 0; i < 12; i++) {
                counts.push(0);
                var color = "rgba(" + getRandom(0, 256) + "," + getRandom(0, 256) + "," + getRandom(0, 256);
                colors.push(color + ", 0.2)");
                colorsBorder.push(color + ", 1)");
            }

            for (var i = 0; i < monthSummary.length; i++) {
                counts[labels.indexOf(monthSummary[i]["month"])] = monthSummary[i]["count"];
            }

            var monthSummaryChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        borderWidth: 3,
                        backgroundColor: colors,
                        borderColor: colorsBorder
                    }]
                },
                options: {
                    responsive: false,
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Month Summary Chart',
                        fontColor: 'white',
                        fontSize: 25
                    }
                }
            });
        }

        function initializeServerFilesSummaryChart() {
            var ctx = document.getElementById("served_files_summary").getContext("2d");

            var counts = [];
            var files = [];
            var colors = [];
            var colorsBorder = [];

            for (var i = 0; i < servedFilesSummary.length; i++) {
                counts.push(servedFilesSummary[i]["count"]);
                files.push(servedFilesSummary[i]["file"]);
                var color = "rgba(" + getRandom(0, 256) + "," + getRandom(0, 256) + "," + getRandom(0, 256);
                colors.push(color + ", 0.2)");
                colorsBorder.push(color + ", 1)");
            }

            var servedFilesSummaryChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: files,
                    datasets: [{
                        data: counts,
                        borderWidth: 3,
                        backgroundColor: colors,
                        borderColor: colorsBorder
                    }]
                },
                options: {
                    responsive: false,
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Served Files Summary Chart',
                        fontColor: 'white',
                        fontSize: 25
                    }
                }
            });
        }

        function initializeRequestVerbSummaryChart() {
            var ctx = document.getElementById("request_verb_summary").getContext("2d");

            var counts = [];
            var verbs = [];
            var colors = [];
            var colorsBorder = [];

            for (var i = 0; i < requestVerbSummary.length; i++) {
                counts.push(requestVerbSummary[i]["count"]);
                verbs.push(requestVerbSummary[i]["verb"]);
                var color = "rgba(" + getRandom(0, 256) + "," + getRandom(0, 256) + "," + getRandom(0, 256);
                colors.push(color + ", 0.2)");
                colorsBorder.push(color + ", 1)");
            }

            var servedFilesSummaryChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: verbs,
                    datasets: [{
                        data: counts,
                        borderWidth: 3,
                        backgroundColor: colors,
                        borderColor: colorsBorder
                    }]
                },
                options: {
                    responsive: false,
                    title: {
                        display: true,
                        text: 'Request Verbs Summary Chart',
                        fontColor: 'white',
                        fontSize: 25
                    }
                }
            });
        }

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

                            window.location.href = "/charts?server=" + server;
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
        initializeStatusSummaryChart();
        initializeDayOfWeekSummaryChart();
        initializeHourSummaryChart();
        initializeMonthSummaryChart();
        initializeServerFilesSummaryChart();
        initializeRequestVerbSummaryChart();
    </script>
</div>
