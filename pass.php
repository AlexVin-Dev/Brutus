<?php
// Обработка темы
$theme = $_COOKIE['theme'] ?? 'light';
if(isset($_GET['theme'])) {
    $theme = in_array($_GET['theme'], ['light', 'dark']) ? $_GET['theme'] : 'light';
    setcookie('theme', $theme, [
        'expires' => time() + 86400 * 30,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'samesite' => 'Lax',
        'httponly' => true
    ]);
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Обработка формы
$password = '';
$error = '';
$length = $_POST['length'] ?? 16;
$useUpper = isset($_POST['upper']);
$useNumbers = isset($_POST['numbers']);
$useSpecial = isset($_POST['special']);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $length = (int)($length);
        if($length < 12 || $length > 64) {
            throw new Exception("Длина пароля должна быть от 12 до 64 символов");
        }
        
        if(!$useUpper && !$useNumbers && !$useSpecial) {
            throw new Exception("Выберите хотя бы один тип символов");
        }

        $password = generate_password($length, $useUpper, $useNumbers, $useSpecial);
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

function generate_password($length, $useUpper, $useNumbers, $useSpecial) {
    $charsets = [
        'lower' => 'abcdefghjkmnpqrstuvwxyz',
        'upper' => 'ABCDEFGHJKMNPQRSTUVWXYZ',
        'numbers' => '23456789',
        'special' => '!@#$%^&*()_+-=[]{}|;:,.<>?'
    ];

    $selected = ['lower'];
    if($useUpper) $selected[] = 'upper';
    if($useNumbers) $selected[] = 'numbers';
    if($useSpecial) $selected[] = 'special';

    $password = '';
    foreach($selected as $set) {
        $password .= $charsets[$set][random_int(0, strlen($charsets[$set]) - 1)];
    }

    $allChars = implode('', array_map(fn($s) => $charsets[$s], $selected));
    for($i = strlen($password); $i < $length; $i++) {
        $password .= $allChars[random_int(0, strlen($allChars) - 1)];
    }

    return str_shuffle($password);
}

function esc($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генератор паролей VelFan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
 <style>
 <style>
        :root {
            --gradient-primary: linear-gradient(45deg, #4e73df, #224abe);
            --gradient-info: linear-gradient(45deg, #36b9cc, #258391);
        }

        [data-bs-theme="light"] {
            --bs-body-bg: #f8f9fa;
            --bs-body-color: #212529;
            --bs-tertiary-bg: #e9ecef;
            --bs-secondary-bg: #dee2e6;
        }

        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1a1a;
            --bs-body-color: #ffffff;
            --bs-tertiary-bg: #2d2d2d;
            --bs-secondary-bg: #404040;
        }

        body {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            transition: background-color 0.3s, color 0.3s;
        }

        .password-display {
            background: var(--bs-tertiary-bg);
            color: var(--bs-body-color);
        }

        .theme-switcher {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .card-hover {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
        }

        .password-display {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            letter-spacing: 2px;
            background: var(--bs-tertiary-bg);
            border-radius: 8px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .copy-success {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            display: none;
        }

        .gradient-primary {
            background: var(--gradient-primary);
        }

        .server-badge {
            position: absolute;
            top: -12px;
            right: 20px;
            z-index: 2;
        }
    </style>
</head>
<body class="bg-body-secondary">
    <div class="theme-switcher">
        <div class="btn-group shadow">
            <a href="?theme=light" class="btn btn-<?= $theme === 'light' ? 'primary' : 'secondary' ?>">
                <i class="fas fa-sun"></i>
            </a>
            <a href="?theme=dark" class="btn btn-<?= $theme === 'dark' ? 'primary' : 'secondary' ?>">
                <i class="fas fa-moon"></i>
            </a>
        </div>
    </div>

    <div class="container py-5">
        <h1 class="mb-4 text-center text-primary gradient-primary bg-clip-text text-transparent">
            <i class="fas fa-shield-alt me-3"></i>Генератор паролей VelFan
        </h1>

        <div class="card shadow-lg card-hover">
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label"><i class="fas fa-ruler-horizontal me-2"></i>Длина пароля</label>
                        <input type="number" 
                               name="length" 
                               class="form-control" 
                               min="12" 
                               max="64" 
                               value="<?= esc($length) ?>"
                               required>
                    </div>
                    
                    <div class="col-12 col-md-5">
                        <label class="form-label"><i class="fas fa-tools me-2"></i>Настройки символов</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="upper" id="upper" <?= $useUpper ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="upper">
                                        <i class="fas fa-text-height me-1"></i>Заглавные буквы
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="numbers" id="numbers" <?= $useNumbers ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="numbers">
                                        <i class="fas fa-hashtag me-1"></i>Цифры
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="special" id="special" <?= $useSpecial ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="special">
                                        <i class="fas fa-exclamation me-1"></i>Спецсимволы
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-sync-alt me-2"></i>Сгенерировать
                        </button>
                    </div>
                </form>

                <?php if($password): ?>
                    <div class="mt-4 position-relative">
                        <span class="server-badge badge bg-info">
                            <i class="fas fa-lock me-2"></i>Безопасно
                        </span>
                        <div class="password-display" onclick="copyPassword(this)">
                            <?= esc($password) ?>
                            <span class="badge bg-success float-end">Кликните для копирования</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($error): ?>
                    <div class="alert alert-danger mt-4 fade-in">
                        <?= esc($error) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mt-4 g-4">
            <div class="col-12 col-md-6">
                <div class="card shadow-lg card-hover h-100">
                    <div class="card-header gradient-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shield me-2"></i>Безопасность</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-ban text-danger me-2"></i>
                                <strong>Защита от брутфорс-атак</strong>
                                <p class="mb-0 text-muted">Использование криптографически безопасного генератора</p>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-microchip text-primary me-2"></i>
                                <strong>Локальная генерация</strong>
                                <p class="mb-0 text-muted">Данные не покидают ваше устройство</p>
                            </li>
                            <li>
                                <i class="fas fa-sliders-h text-warning me-2"></i>
                                <strong>Гибкие настройки</strong>
                                <p class="mb-0 text-muted">Контроль длины и типов символов</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card shadow-lg card-hover h-100">
                    <div class="card-header gradient-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-toolbox me-2"></i>Технологии</h5>
                    </div>
                    <div class="card-body">
                        <div class="row row-cols-2 g-3">
                            <div class="col">
                                <div class="p-3 text-center border rounded">
                                    <div class="text-primary fs-4"><i class="fas fa-random"></i></div>
                                    <div class="fw-bold">CRNG</div>
                                    <small class="text-muted">Cryptographically Secure RNG</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3 text-center border rounded">
                                    <div class="text-primary fs-4"><i class="fas fa-code"></i></div>
                                    <div class="fw-bold">PHP 8.1+</div>
                                    <small class="text-muted">Современные стандарты</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3 text-center border rounded">
                                    <div class="text-primary fs-4"><i class="fas fa-paint-brush"></i></div>
                                    <div class="fw-bold">Bootstrap 5</div>
                                    <small class="text-muted">Адаптивный дизайн</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3 text-center border rounded">
                                    <div class="text-primary fs-4"><i class="fas fa-shield-alt"></i></div>
                                    <div class="fw-bold">HTTPS</div>
                                    <small class="text-muted">Безопасное соединение</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="copy-success alert alert-success shadow-lg fade-in">
        <i class="fas fa-check-circle me-2"></i>Пароль скопирован в буфер обмена!
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyPassword(element) {
            const text = element.textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                const alert = document.querySelector('.copy-success');
                alert.style.display = 'block';
                setTimeout(() => alert.style.display = 'none', 2000);
            });
        }

    // Обновленный скрипт управления темами
    document.addEventListener('DOMContentLoaded', () => {
        const themeButtons = document.querySelectorAll('.theme-switcher a');
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        
        // Обновление кнопок
        themeButtons.forEach(btn => {
            btn.classList.remove('active', 'btn-primary', 'btn-secondary');
            const isActive = btn.getAttribute('href').includes(`theme=${currentTheme}`);
            btn.classList.add(isActive ? 'btn-primary' : 'btn-secondary');
        });

        // Принудительное обновление темы
        document.documentElement.setAttribute('data-bs-theme', currentTheme);
    });
    </script>
</body>
</html>
