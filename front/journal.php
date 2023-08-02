<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector
#
#  journal.php - Front module. Journal page
#-------------------------------------------------------------------------------
#  leiweibau 2023                                          GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php
session_start();

if ($_SESSION["login"] != 1) {
	header('Location: ./index.php');
	exit;
}
require 'php/templates/header.php';
require 'php/server/journal.php';

# Init DB Connection
$db_file = '../db/pialert.db';
$db = new SQLite3($db_file);
$db->exec('PRAGMA journal_mode = wal;');

?>

<!-- Page ------------------------------------------------------------------ -->
  <div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
      <h1 id="pageTitle">
         <?php echo $pia_journ_lang['Title'] ?>
      </h1>
    </section>

<!-- Main content ---------------------------------------------------------- -->
    <section class="content">

<!-- datatable ------------------------------------------------------------- -->
      <div class="row">
        <div class="col-xs-12">
          <div id="tableEventsBox" class="box">

            <!-- box-header -->
            <div class="box-header">
              <h3 id="tableEventsTitle" class="box-title text-aqua">Journal</h3>
            </div>

            <!-- table -->
            <div class="box-body table-responsive">
              <table id="tableEvents" class="table table-bordered table-hover table-striped ">
                <thead>
                <tr>
                  <th>Date / Time</th>
                  <th>LogClass</th>
                  <th>LogString</th>
                  <th>LogCode</th>
                  <th>Class</th>
                  <th>Trigger</th>
                  <th>Hash</th>
                  <th>Additional_Info</th>
                </tr>
                </thead>
                  <tbody>
        <?php
# Create Journal table
get_pialert_journal();
?>
                  </tbody>
              </table>
            </div>
            <!-- /.box-body -->

          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

<!-- ----------------------------------------------------------------------- -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- ----------------------------------------------------------------------- -->
<?php
require 'php/templates/footer.php';

function get_pialert_journal() {
	global $db;
	global $pia_journ_lang;

	$pia_journal = $db->query('SELECT * FROM pialert_journal ORDER BY Journal_DateTime DESC Limit 1000');
	while ($row = $pia_journal->fetchArray()) {

		if ($row['LogClass'] == "a_000") {$full_additional_info = $pia_journ_lang[$row['LogString']] . '<br>' . $pia_journ_lang['File_hash'] . ': <span class="text-danger">' . $row['Hash'] . '</span>';} else { $full_additional_info = $pia_journ_lang[$row['LogString']];}
		$full_additional_info = $full_additional_info . '<br>' . $row['Additional_Info'];

		echo '<tr>
                  <td style="white-space: nowrap;">' . $row['Journal_DateTime'] . '</td>
                  <td>' . $row['LogClass'] . '</td>
                  <td>' . $row['LogString'] . '</td>
                  <td>' . str_replace('LogStr', $row['LogClass'], $row['LogString']) . '</td>
                  <td style="white-space: nowrap;">' . $pia_journ_lang[$row['LogClass']] . '</td>
                  <td>' . $row['Trigger'] . '</td>
                  <td>' . $row['Hash'] . '</td>
                  <td>' . $full_additional_info . '</td>
              </tr>';
	}
}

?>

<!-- ----------------------------------------------------------------------- -->
<!-- Datatable -->
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <script src="lib/AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="lib/AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!-- page script ----------------------------------------------------------- -->
<script>
  var devicesList         = [];
  var pos                 = -1;
  var parPeriod           = 'Front_Journal_Period';
  var parEventsRows       = 'Front_Journal_Rows';
  var period              = '1 month';

  main();

function main () {
  initializeDatatable();
}

function initializeDatatable () {
  $('#tableEvents').DataTable({
    'paging'       : true,
    'lengthChange' : true,
    'lengthMenu'   : [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, 'All']],
    //'bLengthChange': false,
    'searching'    : true,
    'ordering'     : true,
    'info'         : true,
    'autoWidth'    : false,
    'pageLength'   : 25,
    'order'        : [[0, 'desc']],
    'columns': [
        { "data": 0 },
        { "data": 1 },
        { "data": 2 },
        { "data": 3 },
        { "data": 4 },
        { "data": 5 },
        { "data": 6 },
        { "data": 7 }
      ],

    'columnDefs'  : [
      //{className: 'text-center', targets: [1,2] },
      { "width": "120px", "targets": [0] },
      { "width": "90px", "targets": [3] },

      //Device Name
      {targets: [0],
       "createdCell": function (td, cellData, rowData, row, col) {
         $(td).html ('<b>'+ cellData +'</b>');
      } },

      {targets: [1,2,6],
          visible: false
      },
    ],

    // Processing
    'processing'  : true,
    'language'    : {
      processing: '<table><td width="130px" align="middle">Loading...</td><td><i class="ion ion-ios-loop-strong fa-spin fa-2x fa-fw"></td></table>',
      emptyTable: 'No data',
      "lengthMenu": "<?php echo $pia_lang['Events_Tablelenght']; ?>",
      "search":     "<?php echo $pia_lang['Events_Searchbox']; ?>: ",
      "paginate": {
          "next":       "<?php echo $pia_lang['Events_Table_nav_next']; ?>",
          "previous":   "<?php echo $pia_lang['Events_Table_nav_prev']; ?>"
      },
      "info":           "<?php echo $pia_lang['Events_Table_info']; ?>",
    },
  });
};

</script>
