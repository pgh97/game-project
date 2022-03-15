<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>기획데이터 업로드</title>
    <script type="text/javascript">
        function formSubmit(f) {
            // 업로드 할 수 있는 파일 확장자를 제한합니다.
            var extArray = new Array('xls', 'xlsx');
            var path = document.getElementById("fileToUpload").value;
            if (path == "") {
                alert("파일을 선택해 주세요.");
                return false;
            }

            var pos = path.indexOf(".");
            if (pos < 0) {
                alert("확장자가 없는파일 입니다.");
                return false;
            }

            var ext = path.slice(path.indexOf(".") + 1).toLowerCase();
            var checkExt = false;
            for (var i = 0; i < extArray.length; i++) {
                if (ext == extArray[i]) {
                    checkExt = true;
                    break;
                }
            }

            if (checkExt == false) {
                alert("업로드 할 수 없는 파일 확장자 입니다.");
                return false;
            }
            return true;
        }

    </script>
</head>
<body>
<h1>기획데이터 파일 업로드 페이지</h1>
<h5>.xls, .xlsx의 단순 엑셀 파일만 업로드 가능.</h5>
<h5>기획데이터 엑셀파일의 이름은 테이블명과 동일하게 해야함.</h5>
<h5>그리고 데이터 중 등록일 or 생성일은 제외시킴.</h5>
<h5>ex) 테이블명 : fish_info_data -> 엑셀파일명 : fish_info_data.xlsx</h5>
<img src="http://localhost:8888/upload/data_image/example.PNG"/>
<h5>위의 이미지가 예시.</h5>
<br><br>

<form name="uploadForm" id="uploadForm" method="POST" action="excelFileUpload.php"
      enctype="multipart/form-data" onsubmit="return formSubmit(this);">
    <input type="file" name="fileToUpload" id="fileToUpload"/>
    <input type="submit" value="upload" name="submit"/>
</form>
</body>
</html>