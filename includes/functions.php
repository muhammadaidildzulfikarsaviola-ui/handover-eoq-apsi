<?php
declare(strict_types=1);
function h(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function rupiah(float|int|string $value): string { return 'Rp' . number_format((float) $value, 0, ',', '.'); }
function number_id(float|int|string $value, int $decimals = 2): string { return number_format((float) $value, $decimals, ',', '.'); }
function redirect_to(string $page, array $params = []): never { header('Location: index.php?' . http_build_query(array_merge(['page' => $page], $params))); exit; }
function set_flash(string $type, string $message): void { $_SESSION['flash'] = ['type' => $type, 'message' => $message]; }
function get_flash(): ?array { if (!isset($_SESSION['flash'])) return null; $flash = $_SESSION['flash']; unset($_SESSION['flash']); return $flash; }
function csrf_token(): string { if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); return $_SESSION['csrf_token']; }
function verify_csrf(): void { $token = $_POST['csrf_token'] ?? ''; if (!is_string($token) || !hash_equals(csrf_token(), $token)) { http_response_code(419); exit('Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.'); } }
function post_string(string $key, string $default = ''): string { $value = $_POST[$key] ?? $default; return is_string($value) ? trim($value) : $default; }
