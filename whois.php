<?php
// Конфигурация кэширования
$CACHE_DIR = __DIR__.'/cache';
if (!is_dir($CACHE_DIR)) mkdir($CACHE_DIR, 0755, true);

// Обработка темы
$theme = $_COOKIE['theme'] ?? 'light';
if(isset($_GET['theme'])) {
    $theme = in_array($_GET['theme'], ['light', 'dark']) ? $_GET['theme'] : 'light';
    setcookie('theme', $theme, time() + 86400 * 30, '/');
}

// Обработка WHOIS запроса
$query = $_POST['query'] ?? null;
$result = null;
$error = null;
$server = null;

if ($query) {
    $query = trim($query);
    $cacheKey = "whois_".md5($query);
    
    if ($cached = getCache($cacheKey)) {
        $result = $cached['result'];
        $server = $cached['server'];
    } else {
        $server = get_whois_server($query);
        $result = whois_query($server, $query, $error);
        
        if ($result && !$error) {
            setCache($cacheKey, ['result' => $result, 'server' => $server]);
        }
    }
}

function getCache($key) {
    global $CACHE_DIR;
    $file = $CACHE_DIR.'/'.md5($key).'.cache';
    return file_exists($file) ? json_decode(file_get_contents($file), true) : null;
}

function setCache($key, $data) {
    global $CACHE_DIR;
    file_put_contents($CACHE_DIR.'/'.md5($key).'.cache', json_encode($data));
}

function get_whois_server($input) {
    if (preg_match('/^AS\d+$/i', $input)) return 'whois.ripe.net';
    if (filter_var($input, FILTER_VALIDATE_IP)) return 'whois.arin.net';
    if (preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $input)) return 'whois.verisign-grs.com';
    return 'whois.arin.net';
}

function whois_query($server, $query, &$error = null) {
    try {
        $fp = fsockopen($server, 43, $errno, $errstr, 10);
        if (!$fp) {
            $error = "Ошибка подключения: $errstr ($errno)";
            return null;
        }
        
        fwrite($fp, $query."\r\n");
        $response = '';
        while (!feof($fp)) $response .= fgets($fp, 128);
        fclose($fp);

        // Проверка реферального сервера
        if (preg_match('/ReferralServer: whois:\/\/(.+)/i', $response, $matches)) {
            return whois_query(trim($matches[1]), $query, $error);
        }

        return $response;
    } catch (Exception $e) {
        $error = "Ошибка при выполнении запроса: ".$e->getMessage();
        return null;
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
    <title>WHOIS Query Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --gradient-primary: linear-gradient(45deg, #4e73df, #224abe);
            --gradient-info: linear-gradient(45deg, #36b9cc, #258391);
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

        .gradient-info {
            background: var(--gradient-info);
        }

        pre {
            background: var(--bs-tertiary-bg);
            border-radius: 8px;
            padding: 1rem;
            max-height: 500px;
            white-space: pre-wrap;
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
            <i class="fas fa-search me-3"></i>WHOIS Query Tool
        </h1>

        <div class="card shadow-lg card-hover">
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-12 col-md-8">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                            <input type="text" 
                                   name="query" 
                                   class="form-control" 
                                   placeholder="Введите домен, IP или AS (пример: example.com, 8.8.8.8, AS1234)"
                                   value="<?= esc($query) ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search me-2"></i> Поиск
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-4 fade-in">
                <?= esc($error) ?>
            </div>
        <?php elseif ($result): ?>
            <div class="row mt-4 fade-in">
                <div class="col-12">
                    <div class="card shadow-lg card-hover position-relative">
                        <span class="server-badge badge bg-info">
                            <i class="fas fa-server me-2"></i><?= esc($server) ?>
                        </span>
                        <div class="card-header gradient-info text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Результаты WHOIS</h5>
                        </div>
                        <div class="card-body">
                            <pre class="mb-0"><?= esc($result) ?></pre>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row mt-4 fade-in">
            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-lg card-hover h-100">
                    <div class="card-header gradient-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Справка</h5>
                    </div>
                    <div class="card-body">
                        <dl class="mb-0">
                            <dt>🏷️ Доменные имена</dt>
                            <dd>Используется сервер: whois.verisign-grs.com</dd>
                            
                            <dt class="mt-3">📡 IP-адреса</dt>
                            <dd>Используется сервер: whois.arin.net</dd>
                            
                            <dt class="mt-3">🌍 Автономные системы</dt>
                            <dd>Используется сервер: whois.ripe.net</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 mb-4">
                <div class="card shadow-lg card-hover h-100">
                    <div class="card-header gradient-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>О сервисе</h5>
                    </div>
                    <div class="card-body">
                        <p>WHOIS (от англ. who is — «кто это?») — сетевой протокол для получения информации о доменах, IP-адресах и автономных системах.</p>
                        <p class="mb-0">Сервис автоматически определяет тип запроса и выбирает соответствующий WHOIS-сервер. Результаты кэшируются на 1 час.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Анимация элементов
            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.opacity = 0;
                setTimeout(() => el.style.opacity = 1, 100);
            });

            // Подсветка синтаксиса
            document.querySelectorAll('pre').forEach(pre => {
                const lines = pre.textContent.split('\n');
                pre.innerHTML = lines.map(line => {
                    if(line.startsWith('#')) return `<span class="text-muted">${esc(line)}</span>`;
                    if(line.includes(':') && !line.startsWith(' ')) {
                        const [key, ...value] = line.split(':');
                        return `<strong>${esc(key)}:</strong>${esc(value.join(':'))}`;
                    }
                    return esc(line);
                }).join('\n');
            });
        });

        function esc(str) {
            return str.replace(/&/g, '&amp;')
                     .replace(/</g, '&lt;')
                     .replace(/>/g, '&gt;');
        }
    </script>
</body>
</html>
