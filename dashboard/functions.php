<?php
include 'conn.php';

require_once 'dbconfig4.php';

if(isset($_POST["export_teacher_student"])){
     
     header('Content-Type: text/csv; charset=utf-8');  
     header('Content-Disposition: attachment; filename=students_teacher_payment.csv');
     $fid =  $_POST["feeID"];
     $pid =  $_POST["pay_sub_id"];
     $output = fopen("php://output", "w");  
     fputcsv($output, array('Full Name', 'Contact', 'Address','Payment', 'Date'));  
     $query = "SELECT lmsregister.fullname,lmsregister.contactnumber,lmsregister.address,lmspayment.amount,lmspayment.created_at FROM lmsregister INNER JOIN lmspayment ON lmsregister.reid = lmspayment.userID WHERE lmspayment.feeID=$fid AND lmspayment.pay_sub_id=$pid AND lmspayment.status = 1";  
     $result = mysqli_query($conn, $query);  
     while($row = mysqli_fetch_assoc($result))  
     {  
          fputcsv($output, $row);  
     }  
     fclose($output);  
}

if(isset($_POST["export_grade"])){
     
     header('Content-Type: text/csv; charset=utf-8');  
     header('Content-Disposition: attachment; filename=students_grade.csv');
     $gid =  $_POST["gid"];
     $output = fopen("php://output", "w");  
     fputcsv($output, array('ID', 'Student Number', 'Email', 'Full Name', 'DOB', 'Gender', 'School', 'District', 'Town', 'PContact', 'Contact', 'Address', 'Level', 'Password', 'Image', 'Add Date', 'Status', 'IP Address', 'Relogin', 'Relogin IP', 'Payment', 'Verification code'));  
     $query = "SELECT * from lmsregister WHERE level = $gid ";  
     $result = mysqli_query($conn, $query);  
     while($row = mysqli_fetch_assoc($result))  
     {  
          fputcsv($output, $row);  
     }  
     fclose($output);  
}

if(isset($_POST["Export"])){
     
     header('Content-Type: text/csv; charset=utf-8');  
     header('Content-Disposition: attachment; filename=all_students.csv');  
     $output = fopen("php://output", "w");  
     fputcsv($output, array('ID', 'Student Number', 'Email', 'Full Name', 'DOB', 'Gender', 'School', 'District', 'Town', 'PContact', 'Contact', 'Address', 'Level', 'Password', 'Image', 'Add Date', 'Status', 'IP Address', 'Relogin', 'Relogin IP', 'Payment', 'Verification code'));  
     $query = "SELECT * from lmsregister ORDER BY reid DESC";  
     $result = mysqli_query($conn, $query);  
     while($row = mysqli_fetch_assoc($result))  
     {  
          fputcsv($output, $row);  
     }  
     fclose($output);  
}

if(isset($_POST["Manual"])){
     
     header('Content-Type: text/csv; charset=utf-8');  
     header('Content-Disposition: attachment; filename=manual_payments.csv');  
     $output = fopen("php://output", "w");  
     fputcsv($output, array('Action', 'Status', 'Classes', 'Class Fee', 'Valid-Paid Month', 'Pay Date'));  
     $query = "SELECT yp.pid,yp.status,yr.fullname,yp.amount,yp.created_at,yp.expiredate,yp.pay_month,yt.fullname ytfullname,ys.name

     FROM lmspayment yp LEFT JOIN lmsregister yr ON yp.userID=yr.reid
     
     INNER JOIN lmstealmsr yt ON yp.feeID=yt.tid
     
     INNER JOIN lmssubject ys ON yp.pay_sub_id=ys.sid
     
     WHERE yp.paymentMethod='Manual'
     
     ORDER BY yp.pid DESC";  
     $result = mysqli_query($conn, $query);  
     while($row = mysqli_fetch_assoc($result))  
     {  
          fputcsv($output, $row);  
     }  
     fclose($output);  
}

if(isset($_POST["Pending"])){
     
     header('Content-Type: text/csv; charset=utf-8');  
     header('Content-Disposition: attachment; filename=pending_bank_payments.csv');  
     $output = fopen("php://output", "w");  
     fputcsv($output, array('Slip','Status', 'Student Name','Student Contact','Subject', 'Class Fee', 'Valid-Paid Month', 'Pay Date','Pay Month'));  
     $query = "SELECT yp.fileName,yp.status,yr.fullname,yr.contactnumber,ys.name,yp.amount,yp.expiredate,yp.created_at,yp.pay_month
     FROM lmspayment yp LEFT JOIN lmsregister yr ON yp.userID=yr.reid
     INNER JOIN lmssubject ys ON yp.pay_sub_id=ys.sid
     
     WHERE yp.paymentMethod='Bank' && yp.status=0
     ORDER BY yp.pid DESC";  
     $result = mysqli_query($conn, $query);  
     while($row = mysqli_fetch_assoc($result))  
     {  
          fputcsv($output, $row);  
     }  
     fclose($output);  
}

if(isset($_POST["Paid"])){
     
     header('Content-Type: text/csv; charset=utf-8');  
     header('Content-Disposition: attachment; filename=paid_bank_payments.csv');  
     $output = fopen("php://output", "w");  
     fputcsv($output, array('Slip','Status', 'Student Name','Student Contact','Subject', 'Class Fee', 'Valid-Paid Month', 'Pay Date','Pay Month'));  
     $query = "SELECT yp.fileName,yp.status,yr.fullname,yr.contactnumber,ys.name,yp.amount,yp.expiredate,yp.created_at,yp.pay_month
     FROM lmspayment yp LEFT JOIN lmsregister yr ON yp.userID=yr.reid
     INNER JOIN lmssubject ys ON yp.pay_sub_id=ys.sid
     
     WHERE yp.paymentMethod='Bank' && yp.status=1
     ORDER BY yp.pid DESC";  
     $result = mysqli_query($conn, $query);  
     while($row = mysqli_fetch_assoc($result))  
     {  
          fputcsv($output, $row);  
     }  
     fclose($output);  
}

if(isset($_POST["Rejected"])){
     
     header('Content-Type: text/csv; charset=utf-8');  
     header('Content-Disposition: attachment; filename=rejected_bank_payments.csv');  
     $output = fopen("php://output", "w");  
     fputcsv($output, array('Slip','Status', 'Student Name','Student Contact','Subject', 'Class Fee', 'Valid-Paid Month', 'Pay Date','Pay Month'));  
     $query = "SELECT yp.fileName,yp.status,yr.fullname,yr.contactnumber,ys.name,yp.amount,yp.expiredate,yp.created_at,yp.pay_month
     FROM lmspayment yp LEFT JOIN lmsregister yr ON yp.userID=yr.reid
     INNER JOIN lmssubject ys ON yp.pay_sub_id=ys.sid
     
     WHERE yp.paymentMethod='Bank' && yp.status=2
     ORDER BY yp.pid DESC";  
     $result = mysqli_query($conn, $query);  
     while($row = mysqli_fetch_assoc($result))  
     {  
          fputcsv($output, $row);  
     }  
     fclose($output);  
}

if(isset($_POST["Online"])){
     
     header('Content-Type: text/csv; charset=utf-8');  
     header('Content-Disposition: attachment; filename=online_payments.csv');  
     $output = fopen("php://output", "w");  
     fputcsv($output, array('Status', 'Student Name','Student Contact','Subject', 'Class Fee', 'Valid-Paid Month', 'Pay Date','Pay Month'));  
     $query = "SELECT yp.status,yr.fullname,yr.contactnumber,ys.name,yp.amount,yp.expiredate,yp.created_at,yp.pay_month
     FROM lmspayment yp LEFT JOIN lmsregister yr ON yp.userID=yr.reid
     INNER JOIN lmssubject ys ON yp.pay_sub_id=ys.sid
     
     WHERE yp.paymentMethod='Card' && yp.status=1
     ORDER BY yp.pid DESC";  
     $result = mysqli_query($conn, $query);  
     while($row = mysqli_fetch_assoc($result))  
     {  
          fputcsv($output, $row);  
     }  
     fclose($output);  
}

if(isset($_POST["Teacher_Payments"])){
     
     header('Content-Type: text/csv; charset=utf-8');  
     header('Content-Disposition: attachment; filename=teacher_payments.csv');  
     $output = fopen("php://output", "w");  
     fputcsv($output, array('Name','Percentage', 'Amount', 'Company Amount', 'Pay Date'));  
     $query = "SELECT yr.fullname,yr.Percentage,yp.lms_teacher_payment_history_amount,yp.lms_teacher_payment_company_amount,yp.lms_teacher_payment_history_time
     FROM lms_teacher_payment_history yp LEFT JOIN lmstealmsr yr ON yp.lms_teacher_payment_history_tid=yr.tid
     ORDER BY yp.lms_teacher_payment_history_id DESC";  
     $result = mysqli_query($conn, $query);  
     while($row = mysqli_fetch_assoc($result))  
     {  
          fputcsv($output, $row);  
     }  
     fclose($output);  
}

?>