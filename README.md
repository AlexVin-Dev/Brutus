
# 🛠️ PHP-скрипты от VelFan 

Набор инструментов для безопасности и сетевого взаимодействия

## 🔗 Быстрая навигация
1. [🔑 Генератор паролей](#-генератор-паролей)
2. [🌐 WHOIS-клиент](#-whois-клиент)
3. [🎮 Парсер игровых скинов](#-парсер-игровых-скинов)

---

## 🔑 Генератор паролей
![Shield](https://img.shields.io/badge/Security-Level_99%25-brightgreen)

### 🛡️ Ваш цифровой щит
> Надёжный пароль — главный защитник вашей приватности в сети.  
**Почему это важно:**
- 🚫 Защита от брутфорс-атак
- 🔄 Уникальность для каждого сервиса
- 💥 Комбинация символов: `A-Z a-z 0-9 !@#$%^&*`

### ⚙️ Технология работы
```php
$generator = new PasswordGenerator(
    length: 16,
    useUpper: true,
    useNumbers: true,
    useSpecial: true
);
echo $generator->generate(); // Вывод: Xk8#qL!2vPw$9TzR
```
**Преимущества:**
- 🔒 Локальная генерация (без передачи данных)
- 📏 Настраиваемая длина (12-64 символа)
- 📦 Интеграция с [VelFan Password Manager](#)

---

## 🌐 WHOIS-клиент
![WHOIS](https://img.shields.io/badge/Protocol-TCP_43-blue)

### 🔍 Что можно узнать?
| Тип данных       | Примеры источников       |
|------------------|--------------------------|
| 🏷️ Доменные имена  | whois.verisign-grs.com   |
| 📡 IP-адреса      | whois.arin.net           |
| 🌍 Автономные системы | whois.ripe.net         |

**Пример использования:**
```php
$whois = new WhoisClient('192.0.2.1');
$result = $whois->setTimeout(10)->query();
echo $result->getOwnerInfo(); // Вывод: Network Operator Corp
```

---

## 🎮 Парсер игровых скинов
![Minecraft](https://img.shields.io/badge/Game-Minecraft-green)

### 📦 Получаемые данные
```http
POST /skin.php HTTP/1.1
Host: velfan.ru
Content-Type: application/x-www-form-urlencoded

username=Notch
```

**📥 Ответ сервера:**
```json
{
  "success": true,
  "data": {
    "username": "Notch",
    "uuid": "069a79f444e94726a5befca90e38aaf5",
    "skin_3d": "https://ely.by/skins/notch_3d.png",
    "avatar": "https://ely.by/avatars/notch.jpg",
    "texture_hash": "1a2b3c4d5e"
  }
}
```

### 🖼️ Визуализация данных
1. Иконка профиля  
   ![Иконка](https://ely.by/avatars/notch.jpg)
2. 3D-модель скина  
   [Посмотреть скин](https://ely.by/skins/notch_3d.png)

---

## 🚀 Технические требования
- PHP 7.4+
- Расширение cURL
- 50 MB свободного места
- Поддержка SSL/TLS

[![Лицензия](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
