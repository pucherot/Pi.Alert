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
         <?=$pia_journ_lang['Title']?>
      </h1>
    </section>

<!-- Main content ---------------------------------------------------------- -->
    <section class="content">

<!-- datatable ------------------------------------------------------------- -->
      <div class="row">
        <div class="col-xs-12">
          <div id="tableEventsBox" class="box">

            <div class="box-header">
              <h3 id="tableEventsTitle" class="box-title text-aqua">Journal</h3>
            </div>

            <div class="box-body table-responsive">
              <table id="tableEvents" class="table table-bordered table-hover table-striped ">
                <thead>
                <tr>
                  <th style="min-width: 120px;"><?=$pia_lang['Events_TableHead_Date'];?></th>
                  <th>LogClass</th>
                  <th style="min-width: 80px;">LogCode</th>
                  <th style="min-width: 90px;"><?=$pia_journ_lang['Journal_TableHead_Class'];?></th>
                  <th style="min-width: 100px;"><?=$pia_journ_lang['Journal_TableHead_Trigger'];?></th>
                  <th>Hash</th>
                  <th style="min-width: 500px;"><?=$pia_lang['Events_TableHead_AdditionalInfo'];?></th>
                </tr>
                </thead>
                  <tbody>
<?php
get_pialert_journal();
?>
                  </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
<!-- ----------------------------------------------------------------------- -->
    </section>

  </div>
<!-- ----------------------------------------------------------------------- -->
<?php
require 'php/templates/footer.php';

function get_pialert_journal() {
	global $db;
	global $pia_journ_lang;

	$pia_journal = $db->query('SELECT * FROM pialert_journal ORDER BY Journal_DateTime DESC Limit 500');
	while ($row = $pia_journal->fetchArray()) {
		if ($row['LogClass'] == "a_000") {$full_additional_info = $pia_journ_lang[$row['LogString']] . '<br>' . $pia_journ_lang['File_hash'] . ': <span class="text-danger">' . $row['Hash'] . '</span>';} else { $full_additional_info = $pia_journ_lang[$row['LogString']];}
		$full_additional_info = $full_additional_info . '<br>' . $row['Additional_Info'];

		// DEBUG
		// $logcode = str_replace('LogStr', $row['LogClass'], $row['LogString']);
		// $logclass = $row['LogClass'];
		$logcode = "";
		$logclass = "";

		echo '<tr>
              <td style="">' . $row['Journal_DateTime'] . '</td>
              <td>' . $logclass . '</td>
              <td>' . $logcode . '</td>
              <td style="white-space: nowrap;">' . $pia_journ_lang[$row['LogClass']] . '</td>
              <td>' . $row['Trigger'] . '</td>
              <td>' . $row['Hash'] . '</td>
              <td>' . $full_additional_info . '</td>
          </tr>';
	}
}
?>

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
        { "data": 6 }
      ],

    'columnDefs'  : [
      {className: 'text-center', targets: [1,2] },
      { "width": "120px", "targets": [0] },
      { "width": "90px", "targets": [2] },

      {targets: [0],
        "createdCell": function (td, cellData, rowData, row, col) {
            var createdAtValue = new Date(cellData);
            var today = new Date();
            var currentTime = new Date();
            var oneHourAgo = new Date(currentTime.getTime() - (60 * 60 * 1000)); // Subtract 1 hour in milliseconds

            today.setHours(0, 0, 0, 0);

            if (createdAtValue.getTime() >= today.getTime() && oneHourAgo > createdAtValue) {
                $(td).html('<b style="color:#3468ff;">' + cellData.replace(/ /g, '&nbsp;&nbsp;&nbsp;&nbsp;') + '</b>');
            } else if (createdAtValue >= oneHourAgo) {
                $(td).html('<b style="color:#ff644d;">' + cellData.replace(/ /g, '&nbsp;&nbsp;&nbsp;&nbsp;') + '</b>');
            } else {
                $(td).html('<b>' + cellData.replace(/ /g, '&nbsp;&nbsp;&nbsp;&nbsp;') + '</b>');
            }
        }
      },
      {targets: [1,2,5],
          visible: false
      },
    ],

    // Processing
    'processing'  : true,
    'language'    : {
      processing: '<table><td width="130px" align="middle">Loading...</td><td><i class="ion ion-ios-loop-strong fa-spin fa-2x fa-fw"></td></table>',
      emptyTable: 'No data',
      "lengthMenu": "<?=$pia_lang['Events_Tablelenght'];?>",
      "search":     "<?=$pia_lang['Events_Searchbox'];?>: ",
      "paginate": {
          "next":       "<?=$pia_lang['Events_Table_nav_next'];?>",
          "previous":   "<?=$pia_lang['Events_Table_nav_prev'];?>"
      },
      "info":           "<?=$pia_lang['Events_Table_info'];?>",
    },
  });
};
</script>
