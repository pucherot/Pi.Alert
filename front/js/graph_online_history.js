function pia_draw_graph_online_history(pia_js_graph_online_history_time, pia_js_graph_online_history_ondev, pia_js_graph_online_history_dodev, pia_js_graph_online_history_ardev) {
        var xValues = pia_js_graph_online_history_time;
        new Chart("OnlineChart", {
          type: "bar",
          data: {
            labels: xValues,
            datasets: [{
              label: 'Online',
              data: pia_js_graph_online_history_ondev,
              borderColor: "rgba(0, 166, 89)",
              fill: true,
              backgroundColor: "rgba(0, 166, 89, .6)",
              pointStyle: 'circle',
              pointRadius: 3,
              pointHoverRadius: 3
            }, {
              label: 'Offline/Down',
              data: pia_js_graph_online_history_dodev,
              borderColor: "rgba(222, 74, 56)",
              fill: true,
              backgroundColor: "rgba(222, 74, 56, .6)",
            }, {
              label: 'Archived',
              data: pia_js_graph_online_history_ardev,
              borderColor: "rgba(220,220,220)",
              fill: true,
              backgroundColor: "rgba(220,220,220, .6)",
            }]
          },
          options: {
            legend: {
                display: true,
                labels: {
                    fontColor: "#A0A0A0",
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        fontColor: '#A0A0A0'
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, 0)",
                    },
                    stacked: true,
                }],
                xAxes: [{
                    ticks: {
                        fontColor: '#A0A0A0',
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, 0)",
                    },
                    stacked: true,
                }],
            },
            tooltips: {
                mode: 'index'
            }
          }
        });
};

function pia_draw_graph_services_history(pia_js_online_history_time, pia_js_online_history_down, pia_js_online_history_2xx, pia_js_online_history_3xx, pia_js_online_history_4xx, pia_js_online_history_5xx) {
        var xValues = pia_js_online_history_time;
        new Chart("ServiceChart", {
          type: "bar",
          data: {
            labels: xValues,
            datasets: [{
              label: '2xx',
              data: pia_js_online_history_2xx,
              borderColor: "rgba(0, 166, 89)",
              fill: true,
              backgroundColor: "rgba(0, 166, 89, .6)",
              pointStyle: 'circle',
              pointRadius: 3,
              pointHoverRadius: 3
            }, {
              label: '3xx',
              data: pia_js_online_history_3xx,
              borderColor: "rgba(242,156,18)",
              fill: true,
              backgroundColor: "rgba(242,156,18, .7)",
            }, {
              label: '4xx',
              data: pia_js_online_history_4xx,
              borderColor: "rgba(242,156,18)",
              fill: true,
              backgroundColor: "rgba(242,156,18, .7)",
            }, {
              label: '5xx',
              data: pia_js_online_history_5xx,
              borderColor: "rgba(254,76,0)",
              fill: true,
              backgroundColor: "rgba(254,76,0, .7)",
            }, {
              label: 'Down',
              data: pia_js_online_history_down,
              borderColor: "rgba(189, 43, 26)",
              fill: true,
              backgroundColor: "rgba(189, 43, 26, .7)",
            }]
          },
          options: {
            maintainAspectRatio: false,
            legend: {
                display: true,
                labels: {
                    fontColor: "#A0A0A0",
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        display: false
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, 0)",
                    },
                    stacked: true,
                }],
                xAxes: [{
                    ticks: {
                        fontColor: '#A0A0A0',
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, 0)",
                    },
                    stacked: true,
                }],
            },
          }
        });
};
