<?php
include "../config/db_connection.php";
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['name'] != "") {
    $file = $_FILES['fileToUpload'];
    $server_inputFileName=$file['tmp_name'];
    $pc_FileName=$file['name'];
    $file_type= pathinfo($pc_FileName, PATHINFO_EXTENSION);
    $fileName = substr($file['name'], 0,strrpos($file['name'], '.'));

    $upload_directory = "/game/upload/data_csv/";
    $ext_str = "xls,xlsx";
    $allowed_extensions = explode(',', $ext_str);

    $max_file_size = 5242880;

    //upload 디렉토리가 없을 경우 생성
    if(!is_dir($upload_directory)){
        if(!mkdir($upload_directory,0755)){
            echo "디렉토리 생성에 실패했습니다.";
        }
    }

    // 확장자 체크
    if (!in_array($file_type, $allowed_extensions)) {
        echo "업로드할 수 없는 확장자 입니다.";
    }

    // 파일 크기 체크
    if ($file['size'] >= $max_file_size) {
        echo "5MB 까지만 업로드 가능합니다.";
    }

    // mysql 업로드
    if ($server_inputFileName) {
        /** Create a new Excel File Reader  **/
        if ($file_type == 'xls') {
            $reader = new Xls();
        } elseif ($file_type == 'xlsx') {
            $reader = new Xlsx();
        } else {
            echo '처리할 수 있는 엑셀 파일이 아닙니다';
            exit;
        }

        //    확장자에 따른 설정 구분
        //    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xml();
        //    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
        //    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Slk();
        //    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Gnumeric();
        //    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

        /** Load $server_inputFileName to a Spreadsheet Object  **/
        $spreadsheet = $reader->load($server_inputFileName);

        $spreadData = $spreadsheet->getActiveSheet()->toArray();

        $rows = count($spreadData);
        $cols = (count($spreadData, 1) / count($spreadData)) - 1;

        $sql = "INSERT INTO ".$fileName." (";
        $sqlValues = "values (";
        $fullSql = array();
        echo '<table border="1" style="width:100%">';
        for ($i = 0; $i < $rows; $i++) {
            $sqlValues = "values (";
            echo '<tr>';
            for ($j = 0; $j < $cols; $j++) {
                echo '<td nowrap align="center">' . $spreadData[$i][$j] . '</td>';
                if($i==0){
                    if ($j != $cols-1){
                        $sql .= $spreadData[$i][$j].",";
                    }else{
                        $sql .=$spreadData[$i][$j].") ";
                    }
                }else {
                    if ($j != $cols-1){
                        $sqlValues .= $spreadData[$i][$j].",";
                    }else{
                        $sqlValues .= $spreadData[$i][$j].")";
                    }
                }
            }
            echo '</tr>';

            if($i!=0) {
                $fullSql[$i-1] = $sql.$sqlValues;
            }
        }
        echo '</table>';

        $conn = OpenConn();
        for ($i = 0; $i < count($fullSql); $i++) {
            if (mysqli_query($conn, $fullSql[$i])) {
                echo "New record created successfully";
                echo "<br />";
            } else {
                echo "Error: " . $fullSql . "<br>" . mysqli_error($conn);
                echo "<br />";
            }
        }
        mysqli_close($conn);
        //echo "<h3>MySQL DB table create, data insert 성공</h3>";

        //	print_r($spreadData);

    }

    //파일 업로드
    $path = $upload_directory.basename($pc_FileName);
    if(move_uploaded_file($server_inputFileName, $path)) {
        echo "<h3>파일 업로드 성공</h3>";
        echo "<br />";
    }else{
        echo "<h3>파일 업로드 실패</h3>";
        echo "<br />";
    }
    echo '<a href="javascript:history.go(-1);">업로드 페이지</a>';
} else {
    echo "<h3>파일이 업로드 되지 않았습니다.</h3>";
    echo '<a href="javascript:history.go(-1);">이전 페이지</a>';
}

echo "<br />";

if (UPLOAD_ERR_OK != $_FILES['fileToUpload']['error']) {
    switch ($_FILES['fileToUpload']['error']) {
        case UPLOAD_ERR_INI_SIZE:
            $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            break;
        case UPLOAD_ERR_PARTIAL:
            $message = "The uploaded file was only partially uploaded";
            break;
        case UPLOAD_ERR_NO_FILE:
            $message = "No file was uploaded";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $message = "Missing a temporary folder";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $message = "Failed to write file to disk";
            break;
        case UPLOAD_ERR_EXTENSION:
            $message = "File upload stopped by extension";
            break;
        default:
            $message = "Unknown upload error";
            break;
    }
    echo $message;
}

session_destroy();
?>