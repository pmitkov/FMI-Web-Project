<div class="container">
    <div class="row">Please choose a server to display data:</div>
        <div class="custom-select" id="custom-select">
            <select>
                <?php foreach($hosts as $index => $host): ?>
                    <option value="<?php echo $index ?>"><?php echo $host['server'] . "(" . $host['alias'] . ")"  ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="row">
            <h3 class="graph-anotation">Network utilization</h3>
            <canvas id="network" width="350px" height="150px"></canvas>
        </div>
        <div class="row">
            <h3 class="graph-anotation">Disk utilization</h3>
            <canvas id="disk" width="350px" height="150px"></canvas>
        </div>
        <div class="row">
            <h3 class="graph-anotation">CPU utilization</h3>
            <canvas id="cpu" width="350px" height="150px"></canvas>
        </div>
        <div class="row">
            <h3 class="graph-anotation">Memory utilization</h3>
            <canvas id="memory" width="350px" height="150px"></canvas>
        </div>
        <script>
            var interval;

            var networkGraph;
            var diskGraph;
            var cpuGraph;
            var memoryGraph;

            var networkInput;
            var networkOutput;
            var diskRead;
            var diskWrite;
            var cpuUtilization;
            var memoryUtilization;

            function beginMonitoring(serverName) {
                networkGraph = new SmoothieChart({millisPerPixel: 100});
                networkGraph.streamTo(document.getElementById("network"));

                diskGraph = new SmoothieChart({millisPerPixel: 100});
                diskGraph.streamTo(document.getElementById("disk"));

                cpuGraph = new SmoothieChart({millisPerPixel: 100});
                cpuGraph.streamTo(document.getElementById("cpu"));

                memoryGraph = new SmoothieChart({millisPerPixel: 100});
                memoryGraph.streamTo(document.getElementById("memory"));

                networkInput = new TimeSeries();
                networkOutput = new TimeSeries();

                diskWrite = new TimeSeries();
                diskRead = new TimeSeries();

                cpuUtilization = new TimeSeries();

                memoryUtilization = new TimeSeries();

                networkGraph.addTimeSeries(networkInput, {lineWidth:2,strokeStyle:'#f45642'});
                networkGraph.addTimeSeries(networkOutput, {lineWidth:2,strokeStyle:'#00ff00'});

                diskGraph.addTimeSeries(diskWrite, {lineWidth:2,strokeStyle:'#f45642'});
                diskGraph.addTimeSeries(diskRead, {lineWidth:2,strokeStyle:'#00ff00'});

                cpuGraph.addTimeSeries(cpuUtilization, {lineWidth:2,strokeStyle:'#00ff00'});

                memoryGraph.addTimeSeries(memoryUtilization, {lineWidth:2,strokeStyle:'#00ff00'});

                interval = setInterval(function() {
                    var request = new ajaxRequest();

                    request.open("post", "/monitoring/" + serverName, true);
                    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    request.onreadystatechange = function() {
                        if (this.readyState === 4) {
                            if (this.status === 200) {
                                if (this.responseText != null) {
                                    var monitoringData = JSON.parse(this.responseText);

                                    networkInput.append(new Date().getTime(), monitoringData["network_utilization"]["input_traffic"]);
                                    networkOutput.append(new Date().getTime(), monitoringData["network_utilization"]["output_traffic"]);

                                    diskRead.append(new Date().getTime(), monitoringData["disk_utilization"]["read_IO"]);
                                    diskWrite.append(new Date().getTime(), monitoringData["disk_utilization"]["write_IO"]);

                                    cpuUtilization.append(new Date().getTime(), monitoringData["cpu_utilization"]["cpu_usage"]);

                                    memoryUtilization.append(new Date().getTime(), monitoringData["memory_utilization"]["memory_used"]);
                                } else {
                                    console.log("No response");
                                }
                            } else {
                                console.log("Error fetching data");
                            }
                        }
                    };

                    request.send();
                }, 3000);
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

            function changeServerMonitoring(server) {
                var currentServerSelect = document.getElementsByClassName("select-selected")[0];
                var currentServer = currentServerSelect.innerText.split("(")[0];

                if (server !== currentServer) {
                    clearInterval(interval);

                    networkGraph.stop();
                    diskGraph.stop();
                    cpuGraph.stop();
                    memoryGraph.stop();

                    var networkGraphCanvas = document.getElementById("network");
                    var diskGraphCanvas = document.getElementById("disk");
                    var cpuGraphCanvas = document.getElementById("cpu");
                    var memoryGraphCanvas = document.getElementById("memory");

                    networkGraphCanvas.getContext("2d").clearRect(0, 0, networkGraphCanvas.width, networkGraphCanvas.height);
                    diskGraphCanvas.getContext("2d").clearRect(0, 0, diskGraphCanvas.width, diskGraphCanvas.height);
                    cpuGraphCanvas.getContext("2d").clearRect(0, 0, cpuGraphCanvas.width, cpuGraphCanvas.height);
                    memoryGraphCanvas.getContext("2d").clearRect(0, 0, memoryGraphCanvas.width, memoryGraphCanvas.height);

                    beginMonitoring(server)
                }
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
                    fake.addEventListener("click", function(e) {
                        changeServerMonitoring(this.innerText.split("(")[0]);

                        var s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                        var h = this.parentNode.previousSibling;

                        for (var i = 0; i < s.length; i++) {
                            if (s.options[i].innerHTML == this.innerHTML) {
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

                replacement.addEventListener("click", function(e) {
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

            var server = document.getElementsByClassName("select-selected")[0];
            var serverName = server.innerText.split("(")[0];

            beginMonitoring(serverName);
        </script>
    </div>
</div>