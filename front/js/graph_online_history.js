function graph_online_history_main(pia_js_graph_online_history_time, pia_js_graph_online_history_ondev, pia_js_graph_online_history_dodev, pia_js_graph_online_history_ardev) {
    var xValues = pia_js_graph_online_history_time;
    new Chart("OnlineChart", {
      type: "bar",
      scaleIntegersOnly: true,
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

function graph_online_history_icmp(pia_js_graph_online_history_time, pia_js_graph_online_history_ondev, pia_js_graph_online_history_dodev) {
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
                    fontColor: '#A0A0A0',
                    stepSize: 1
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

function graph_services_history(pia_js_online_history_time, pia_js_online_history_down, pia_js_online_history_2xx, pia_js_online_history_3xx, pia_js_online_history_4xx, pia_js_online_history_5xx) {
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

function graph_icmphost_history(pia_js_online_history_time, pia_js_online_history_down, pia_js_online_history_online) {
    var xValues = pia_js_online_history_time;
    new Chart("ServiceChart", {
      type: "bar",
      data: {
        labels: xValues,
        datasets: [{
          label: 'Online',
          data: pia_js_online_history_online,
          borderColor: "rgba(0, 166, 89)",
          fill: true,
          backgroundColor: "rgba(0, 166, 89, .6)",
          pointStyle: 'circle',
          pointRadius: 3,
          pointHoverRadius: 3
        }, {
          label: 'Down/Offline',
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

function graph_speedtest_history(speedtest_js_time, speedtest_js_ping, speedtest_js_down, speedtest_js_up) {
    new Chart("SpeedtestChart", {
        type: 'line',
        data: {
        labels: speedtest_js_time,
        datasets: [{
          label: 'Ping (ms)',
          data: speedtest_js_ping,
          borderColor: "rgba(22, 122, 196)",
          fill: false,
          backgroundColor: "rgba(22, 122, 196, .6)",
          pointStyle: 'circle',
          pointRadius: 3,
          pointHoverRadius: 3,
        },{
          label: 'Download (Mbps)',
          data: speedtest_js_down,
          borderColor: "rgba(0, 166, 89)",
          fill: false,
          backgroundColor: "rgba(0, 166, 89, .6)",
          pointStyle: 'circle',
          pointRadius: 3,
          pointHoverRadius: 3
        },{
          label: 'Upload (Mbps)',
          data: speedtest_js_up,
          borderColor: "rgba(185, 0, 43)",
          fill: false,
          backgroundColor: "rgba(185, 0, 43, .6)",
          pointStyle: 'circle',
          pointRadius: 3,
          pointHoverRadius: 3
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
                        display: true,
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, .3)",
                    },
                    stacked: false,
                }],
                xAxes: [{
                    ticks: {
                        fontColor: '#A0A0A0',
                        callback: function(value) {
                           const val = `${value}`
                           return [`${val.substring(0, 6)}`, `${val.substring(6)}`]
                        }
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, .3)",
                    },
                    stacked: false,
                }],
            },
        }
    });
};

