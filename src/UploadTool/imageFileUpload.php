<?php
if(isset($_FILES['imageToUpload']) && $_FILES['imageToUpload']['name'] != "") {
    $file = $_FILES['imageToUpload'];
    $upload_directory = "/game/upload/data_image/";
    $ext_str = "hwp,xls,doc,xlsx,docx,pdf,jpg,gif,png,txt,ppt,pptx,PNG";
    $allowed_extensions = explode(',', $ext_str);
    $max_file_size = 5242880;
    $ext = substr($file['name'], strrpos($file['name'], '.') + 1);

    // 확장자 체크
    if(!in_array($ext, $allowed_extensions)) {
        echo "업로드할 수 없는 확장자 입니다.";
    }

    // 파일 크기 체크
    if($file['size'] >= $max_file_size) {
        echo "5MB 까지만 업로드 가능합니다.";
    }

    $path = $upload_directory . basename($file["name"]);
    if(move_uploaded_file($file['tmp_name'], $path)) {
        echo"<h3>파일 업로드 성공</h3>";
        echo '<a href="javascript:history.go(-1);">이전 페이지</a>';
    }

} else {
    echo "<h3>파일이 업로드 되지 않았습니다.</h3>";
    echo '<a href="javascript:history.go(-1);">이전 페이지</a>';
}