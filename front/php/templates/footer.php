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
    initCPUtemp();
  </script>

  <script>
    function getDevicesTotalsBadge () {
      // get totals and put in boxes
      $.get('php/server/devices.php?action=getDevicesTotals', function(data) {
        var totalsDevicesbadge = JSON.parse(data);
        var unsetbadge = "";

        if (totalsDevicesbadge[1] > 0) {$('#header_dev_count_on').html(totalsDevicesbadge[1].toLocaleString());} else {$('#header_dev_count_on').html(unsetbadge.toLocaleString());}
        if (totalsDevicesbadge[3] > 0) {$('#header_dev_count_new').html(totalsDevicesbadge[3].toLocaleString());} else {$('#header_dev_count_new').html(unsetbadge.toLocaleString());}
        if (totalsDevicesbadge[4] > 0) {$('#header_dev_count_down').html(totalsDevicesbadge[4].toLocaleString());} else {$('#header_dev_count_down').html(unsetbadge.toLocaleString());}
      } );
    }

    function getICMPTotalsBadge () {
      // get totals and put in boxes
      $.get('php/server/icmpmonitor.php?action=getICMPHostTotals', function(data) {
        var totalsICMPbadge = JSON.parse(data);
        var unsetbadge = "";

        if (totalsICMPbadge[2] > 0) {$('#header_icmp_count_on').html(totalsICMPbadge[2].toLocaleString());} else {$('#header_icmp_count_on').html(unsetbadge.toLocaleString());}
        if (totalsICMPbadge[1] > 0) {$('#header_icmp_count_down').html(totalsICMPbadge[1].toLocaleString());} else {$('#header_icmp_count_down').html(unsetbadge.toLocaleString());}
      } );
    }

    function getReportTotals () {
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

    function updateTotals() {
      getDevicesTotalsBadge();
      getICMPTotalsBadge();
    }

    getReportTotals();
    updateTotals();
    setInterval(updateTotals, 60000);
    setInterval(getReportTotals, 10000);
  </script>

</body>
</html>
