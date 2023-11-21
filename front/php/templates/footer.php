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

        $('#header_dev_count_on').html   (totalsDevicesbadge[1].toLocaleString());
        if (totalsDevicesbadge[3] > 0) {$('#header_dev_count_new').html  (totalsDevicesbadge[3].toLocaleString());}
        if (totalsDevicesbadge[4] > 0) {$('#header_dev_count_down').html (totalsDevicesbadge[4].toLocaleString());}
      } );
    }

    getDevicesTotalsBadge();
    setInterval(getDevicesTotalsBadge, 60000);
  </script>

</body>
</html>
