<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  footer.php - Front module. Common footer to all the web pages
#-------------------------------------------------------------------------------
#  Puche 2021        pi.alert.application@gmail.com        GNU GPLv3
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- Default to the left -->

<?php
echo '<span style="display:inline-block; transform: rotate(180deg)">&copy;</span> ' . $conf_data['VERSION_YEAR'] . ' Puche & leiweibau';
?>
    <!-- To the right -->
    <div class="pull-right no-hidden-xs">
<?php
echo '' . $conf_data['VERSION'] . '&nbsp;&nbsp;<small>(' . $conf_data['VERSION_DATE'] . ')</small>';
?>
    </div>
  </footer>

</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
  <script src="lib/AdminLTE/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.4.1 -->
  <script src="lib/AdminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
  <script src="lib/AdminLTE/dist/js/adminlte.min.js"></script>
<!-- Custom JS -->
  <script src="js/pialert_common.js"></script>

  <script>
    function getDevicesTotalsBadge() {
      // get totals and put in boxes
      $.get('php/server/devices.php?action=getDevicesTotals', function(data) {
        var totalsDevicesbadge = JSON.parse(data);
        var unsetbadge = "";

        if (totalsDevicesbadge[1] > 0) {$('#header_dev_count_on').html(totalsDevicesbadge[1].toLocaleString());} else {$('#header_dev_count_on').html(unsetbadge.toLocaleString());}
        if (totalsDevicesbadge[3] > 0) {$('#header_dev_count_new').html(totalsDevicesbadge[3].toLocaleString());} else {$('#header_dev_count_new').html(unsetbadge.toLocaleString());}
        if (totalsDevicesbadge[4] > 0) {$('#header_dev_count_down').html(totalsDevicesbadge[4].toLocaleString());} else {$('#header_dev_count_down').html(unsetbadge.toLocaleString());}
      } );
    }

    function getICMPTotalsBadge() {
      // get totals and put in boxes
      $.get('php/server/icmpmonitor.php?action=getICMPHostTotals', function(data) {
        var totalsICMPbadge = JSON.parse(data);
        var unsetbadge = "";

        if (totalsICMPbadge[2] > 0) {$('#header_icmp_count_on').html(totalsICMPbadge[2].toLocaleString());} else {$('#header_icmp_count_on').html(unsetbadge.toLocaleString());}
        if (totalsICMPbadge[1] > 0) {$('#header_icmp_count_down').html(totalsICMPbadge[1].toLocaleString());} else {$('#header_icmp_count_down').html(unsetbadge.toLocaleString());}
      } );
    }

    function getServicesTotalsBadge() {
      // get totals and put in boxes
      $.get('php/server/services.php?action=getServiceMonTotals', function(data) {
        var totalsServicesbadge = JSON.parse(data);
        var unsetbadge = "";

        if (totalsServicesbadge[2] > 0) {$('#header_services_count_on').html(totalsServicesbadge[2].toLocaleString());} else {$('#header_services_count_on').html(unsetbadge.toLocaleString());}
        if (totalsServicesbadge[1] > 0) {$('#header_services_count_down').html(totalsServicesbadge[1].toLocaleString());} else {$('#header_services_count_down').html(unsetbadge.toLocaleString());}
        if (totalsServicesbadge[3] > 0) {$('#header_services_count_warning').html(totalsServicesbadge[3].toLocaleString());} else {$('#header_services_count_warning').html(unsetbadge.toLocaleString());}
      } );
    }

    function GetUpdateStatus() {
      // get totals and put in boxes
      $.get('php/server/files.php?action=GetUpdateStatus', function(data) {
        var UpdateCheckbadge = JSON.parse(data);

        $('#header_updatecheck_notification').html(UpdateCheckbadge[0].toLocaleString());
      } );
    }


    function getReportTotalsBadge() {
      // get totals and put in boxes
      $.get('php/server/files.php?action=getReportTotals', function(data) {
        var totalsReportbadge = JSON.parse(data);
        var unsetbadge = "";

        if (totalsReportbadge[0] > 0) {
          $('#Menu_Report_Counter_Badge').html(totalsReportbadge[0].toLocaleString());
          $('#Menu_Report_Envelope_Icon' ).addClass("text-red");
        } else {
          $('#Menu_Report_Counter_Badge').html(unsetbadge.toLocaleString());
          $('#Menu_Report_Envelope_Icon' ).removeClass("text-red");
        }
      });
    }

    var pia_servertime;
    var TopServerClock;

    function GetPiAlertServerTime() {
      clearTimeout(TopServerClock);
      $.get('php/server/files.php?action=GetServerTime', function(data) {
          var dateArray = data.split(',').map(Number);
          pia_servertime = new Date(dateArray[0], dateArray[1] - 1, dateArray[2], dateArray[3], dateArray[4], dateArray[5]);
          ShowPiAlertServerTime();
      });
    }

    function ShowPiAlertServerTime() {
        if (!document.getElementById) {
            return;
        }
        var pia_hour = pia_servertime.getHours();
        var pia_minute = pia_servertime.getMinutes();
        var pia_second = pia_servertime.getSeconds();
        pia_servertime.setSeconds(pia_second + 1);
        if (pia_hour <= 9) { pia_hour = "0" + pia_hour; }
        if (pia_minute <= 9) { pia_minute = "0" + pia_minute; }
        if (pia_second <= 9) { pia_second = "0" + pia_second; } 
        var realtime_pia_servertime = "(" + pia_hour + ":" + pia_minute + ":" + pia_second + ")";
        if (document.getElementById) { 
            document.getElementById("PIA_Servertime_place").innerHTML = realtime_pia_servertime; 
        } 
        TopServerClock = setTimeout(ShowPiAlertServerTime, 1000);
    }

    function initializeiCheck() {
       // Blue
       $('input[type="checkbox"].blue').iCheck({
         checkboxClass: 'icheckbox_flat-blue',
         radioClass:    'iradio_flat-blue',
         increaseArea:  '20%'
       });
    }

    function updateTotals() {
      getDevicesTotalsBadge();
      getICMPTotalsBadge();
      getServicesTotalsBadge();
      GetPiAlertServerTime();
      GetUpdateStatus();
    }

    // Init functions
    initCPUtemp();
    getReportTotalsBadge();
    updateTotals();

    // Start function timers
    setInterval(updateTotals, 60000);
    setInterval(getReportTotalsBadge, 15000);
  </script>

  <script>
    var timeoutId; // Declare the timeoutId variable globally

    // Function to reload the page every 60 seconds
    function reloadPage() {
      timeoutId = setTimeout(function () {
        location.reload();
      }, 120000); // 120 seconds
    }

    // Function to handle checkbox state changes
    function handleCheckboxChange() {
      var autoReloadCheckbox = document.getElementById('autoReloadCheckbox');

      if (autoReloadCheckbox.checked) {
        // Start auto-reload if checked
        reloadPage();
        // Save checkbox state to localStorage
        localStorage.setItem('autoReloadChecked', 'true');
      } else {
        // Stop auto-reload if unchecked
        clearTimeout(timeoutId);
        // Remove checkbox state from localStorage
        localStorage.removeItem('autoReloadChecked');
      }
    }

    // Attach the event listener to the checkbox
    document.getElementById('autoReloadCheckbox').addEventListener('change', handleCheckboxChange);

    // Check localStorage for the saved state
    var savedState = localStorage.getItem('autoReloadChecked');
    if (savedState === 'true') {
      document.getElementById('autoReloadCheckbox').checked = true;
      // Start auto-reload
      reloadPage();
    }
  </script>

</body>
</html>
