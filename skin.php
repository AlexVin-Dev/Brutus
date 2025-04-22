<?php
// Включение кэширования
function fetchJson($url, &$error = null) {
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => true
        ]);
        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = "Ошибка подключения: " . curl_error($ch);
            curl_close($ch);
            return null;
        }
        
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        curl_close($ch);
    } else {
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            $error = "Ошибка подключения к серверу";
            return null;
        }
        
        preg_match('/HTTP\/\d\.\d (\d{3})/', $http_response_header[0], $matches);
        $statusCode = $matches[1];
        $body = $response;
    }
    
    if ($statusCode != 200) {
        $error = $statusCode == 404 ? "Пользователь не найден" : "Ошибка $statusCode";
        return null;
    }
    
    return json_decode($body, true);
}

function getCache($key) {
    $file = __DIR__ . '/cache/' . md5($key) . '.cache';
    if (file_exists($file) && time() - filemtime($file) < 3600) {
        return json_decode(file_get_contents($file), true);
    }
    return null;
}

function setCache($key, $data) {
    if (!is_dir(__DIR__ . '/cache')) {
        mkdir(__DIR__ . '/cache', 0755, true);
    }
    $file = __DIR__ . '/cache/' . md5($key) . '.cache';
    file_put_contents($file, json_encode($data));
}

// Обработка темы
$theme = $_COOKIE['theme'] ?? 'light';
if(isset($_GET['theme'])) {
    $theme = in_array($_GET['theme'], ['light', 'dark']) ? $_GET['theme'] : 'light';
    setcookie('theme', $theme, time() + 86400 * 30, '/');
}

// Обработка запроса
$username = $_POST['username'] ?? null;
$userData = null;
$error = null;

if ($username) {
    $username = trim($username);
    
    if (!preg_match('/^[a-zA-Z0-9_]{3,16}$/u', $username)) {
        $error = "Некорректный никнейм";
    } else {
        $cacheKey = "user_{$username}";
        if ($cached = getCache($cacheKey)) {
            $userData = $cached;
        } else {
            $profiles = fetchJson("https://authserver.ely.by/api/users/profiles/minecraft/{$username}", $error);
            
            if ($profiles) {
                $userData = [
                    'id' => $profiles['id'],
                    'uuid' => preg_replace(
                        '/(\w{8})(\w{4})(\w{4})(\w{4})(\w{12})/',
                        '$1-$2-$3-$4-$5',
                        $profiles['id']
                    ),
                    'name' => $profiles['name']
                ];
                
                $skinInfo = fetchJson("https://skinsystem.ely.by/textures/{$username}", $error);
                if ($skinInfo && isset($skinInfo['SKIN'])) {
                    $userData['skin'] = [
                        'url' => $skinInfo['SKIN']['url'],
                        'face' => "https://ely.by/services/skins-renderer?url=" . 
                                  urlencode($skinInfo['SKIN']['url']) . 
                                  "&scale=18.9&renderFace=1",
                        'render' => "https://ely.by/services/skins-renderer?url=" . 
                                   urlencode($skinInfo['SKIN']['url']) . 
                                   "&scale=8.65&slim=0"
                    ];
                }
                
                if ($userData) setCache($cacheKey, $userData);
            }
        }
    }
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
    <title><?= $userData ? "Скин " . esc($userData['name']) : "Поиск скина" ?> - Ely.by</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --gradient-primary: linear-gradient(45deg, #4e73df, #224abe);
            --gradient-success: linear-gradient(45deg, #1cc88a, #13855c);
        }

        a.rollover {
            background-image: url("https://ely.by/services/skins-renderer?url=<?= 
                isset($userData['skin']) ? urlencode($userData['skin']['url']) : '' ?>&scale=8.65&slim=0&v=<?= time() ?>");
            display: block;
            width: 121px;
            height: 276px;
            background-position: -8px 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        a.rollover:hover {
            background-position: -146px 0;
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
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

        .gradient-primary {
            background: var(--gradient-primary);
        }

        .gradient-success {
            background: var(--gradient-success);
        }

        .loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(var(--bs-body-bg-rgb), 0.9);
            z-index: 9999;
        }
    </style>
</head>
<body class="bg-body-secondary">
    <div class="loader">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
    </div>

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
        <h1 class="mb-4 text-center gradient-primary bg-clip-text text-transparent">
            <i class="fas fa-search me-3"></i>Поиск скинов Ely.by
        </h1>

        <div class="card shadow-lg card-hover fade-in">
            <div class="card-body">
                <form method="POST" class="row g-3" id="searchForm">
                    <div class="col-12 col-md-8">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" 
                                   name="username" 
                                   class="form-control" 
                                   placeholder="Введите ник игрока"
                                   value="<?= esc($username) ?>"
                                   pattern="[a-zA-Z0-9_]{3,16}"
                                   title="Только буквы, цифры и подчёркивания (3-16 символов)"
                                   required>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search me-2"></i> Найти
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger fade-in mt-4">
                <?= esc($error) ?>
                <button onclick="history.back()" class="btn btn-link float-end">
                    <i class="fas fa-arrow-left me-2"></i>Назад
                </button>
            </div>
        <?php elseif ($userData): ?>
            <div class="row g-4 mt-4 fade-in">
                <div class="col-12 col-lg-4">
                    <div class="card shadow-lg h-100 card-hover">
                        <div class="card-header gradient-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Основная информация</h5>
                        </div>
                        <div class="card-body">
                            <dl class="mb-0">
                                <dt>Никнейм</dt>
                                <dd><?= esc($userData['name']) ?></dd>
                                
                                <dt class="mt-3">UUID</dt>
                                <dd><code class="text-break"><?= esc($userData['uuid']) ?></code></dd>
                                
                                <dt class="mt-3">URL текстуры</dt>
                                <dd>
                                    <code class="text-break">
                                        <?= isset($userData['skin']) ? esc($userData['skin']['url']) : 'Недоступно' ?>
                                    </code>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="card shadow-lg card-hover">
                        <div class="card-header gradient-success text-white">
                            <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Внешний вид</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4 text-center">
                                <div class="col-12 col-sm-4">
                                    <h5>Голова</h5>
                                    <img src="<?= isset($userData['skin']) ? esc($userData['skin']['face']) : 'https://ely.by/images/skins/steve-face.png' ?>" 
                                         class="img-fluid shadow"
                                         style="width: 150px; height: 150px;">
                                </div>
                                
                                <div class="col-12 col-sm-4">
                                    <h5>3D Превью</h5>
                                    <div class="d-inline-block position-relative">
                                        <a href='https://ely.by/skins?uploader=<?= esc($userData['name']) ?>' 
                                           target='_blank' 
                                           class='rollover'></a>
                                        <div class="position-absolute bottom-0 start-0 end-0 p-2 bg-dark bg-opacity-75 text-white small">
                                            Кликните для просмотра
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-sm-4">
                                    <h5>Текстура</h5>
                                    <?php if (isset($userData['skin'])): ?>
                                        <a href="<?= esc($userData['skin']['url']) ?>" 
                                           target="_blank"
                                           class="d-block mb-3">
                                            <img src="<?= esc($userData['skin']['url']) ?>" 
                                                 class="img-fluid rounded shadow">
                                        </a>
                                        <a href="https://ely.by/skins?uploader=<?= urlencode($userData['name']) ?>" 
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-history me-2"></i>История скинов
                                        </a>
                                    <?php else: ?>
                                        <div class="text-muted">Скин не найден</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Сохранение положения скролла
            const scrollPos = sessionStorage.getItem('scrollPos');
            if(scrollPos) window.scrollTo(0, scrollPos);

            // Обработка отправки формы
            document.getElementById('searchForm').addEventListener('submit', () => {
                sessionStorage.setItem('scrollPos', window.pageYOffset);
                document.querySelector('.loader').style.display = 'flex';
            });

            // Инициализация анимаций
            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.opacity = 0;
                setTimeout(() => el.style.opacity = 1, 100);
            });

            // Предотвращение мерцания темы
            const savedTheme = localStorage.getItem('theme');
            if(savedTheme) {
                document.documentElement.setAttribute('data-bs-theme', savedTheme);
            }
        });
    </script>
</body>
</html>
