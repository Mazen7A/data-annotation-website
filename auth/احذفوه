<?php
// إعداد الاتصال بقاعدة البيانات
$servername = "localhost";  // أو اسم الخادم في حال كنت تستخدم خادم بعيد
$username = "root";         // اسم المستخدم الخاص بقاعدة البيانات
$password = "";             // كلمة المرور الخاصة بقاعدة البيانات
$dbname = "thaqaftna";      // اسم قاعدة البيانات

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// التحقق من وجود بيانات POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // تشفير كلمة المرور

    // التحقق من وجود المستخدم أو البريد الإلكتروني في قاعدة البيانات
    $sql_check_user = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($sql_check_user);
    if ($result->num_rows > 0) {
        echo "اسم المستخدم أو البريد الإلكتروني موجود مسبقًا.";
    } else {
        // إدخال البيانات في قاعدة البيانات
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "تم إنشاء الحساب بنجاح!";
        } else {
            echo "حدث خطأ: " . $conn->error;
        }
    }
}

$conn->close();
?>
