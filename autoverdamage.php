<?php
include "db.php";
// error_reporting(0);

$sql = "SELECT id_rtn,reqrtn_code,convert(char(10),reqrtn_date,126) as reqrtn_date,datediff(DAY,reqrtn_date,GETDATE()) as day FROM header_returnck 
where reqrtn_type_req=5 and reqrtn_destination_approve='On Progress'";
$resultheader = sqlsrv_query($conn,$sql);

while ($rowdetail = sqlsrv_fetch_array( $resultheader, SQLSRV_FETCH_ASSOC)) {
    if ($rowdetail['day'] >= 1) {
        $id=$rowdetail['id_rtn'];
        $date = $rowdetail['reqrtn_date'];
        try {
            // Begin the transaction
            sqlsrv_begin_transaction($conn);
        
            // Update the detail_returnck table
            $sql1 = "UPDATE e
                SET
                rtnitem_qty_verifikasi_good = a.rtnitem_qty_good,
                rtnitem_qty_verifikasi_not_good = a.rtnitem_qty_not_good
                FROM detail_returnck e
                INNER JOIN detail_returnck a ON e.id_detailrtn = a.id_detailrtn and e.header_idrtn = a.header_idrtn
                WHERE e.header_idrtn ='$id';
            ";
        
            $stmt1 = sqlsrv_query($conn, $sql1);
        
            // Check if the first update was successful
            if (!$stmt1) {
                throw new Exception("Error updating detail_returnck");
            }
        
            // Update the header_returnck table
            $sql2 = "
                UPDATE header_returnck
                SET
                reqrtn_destination_arrival_goods_date='$date',
                reqrtn_destination_approve = 'Verifikasi',
                reqrtn_destination_approve_date = GETDATE(),
                status_progress = 3
                WHERE id_rtn = '$id';
            ";
        
            $stmt2 = sqlsrv_query($conn, $sql2);
        
            // Check if the second update was successful
            if (!$stmt2) {
                throw new Exception("Error updating header_returnck");
            }
        
            // If both updates were successful, commit the transaction
            sqlsrv_commit($conn);
        
            echo "Transaction committed successfully.";
        
        } catch (Exception $e) {
            // If an error occurred during the transaction, rollback the changes
            sqlsrv_rollback($conn);
            echo "Transaction rolled back. Error: " . $e->getMessage();
        }
    }
}

// Close the connection
sqlsrv_close($conn);

