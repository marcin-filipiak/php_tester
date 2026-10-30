<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // maksymalny rozmiar pliku: 10 MB

class UploadController
{
    // ścieżka docelowa względna względem głównego katalogu projektu
    private $targetDir = __DIR__ . '/../public/works/';

    public function handleRequest()
    {
        if (!$this->checkUserAuthentication()) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processUpload();
        } else {
            $this->showUploadForm();
        }
    }

    private function showUploadForm($messages = [])
    {
        // $messages może zawierać np. ['error' => '...', 'success' => '...']
        include 'views/upload_form.php';
    }

    private function processUpload()
    {
        $firstName = $_SESSION['firstName'] ?? '';
        $lastName  = $_SESSION['lastName'] ?? '';

        if (empty($firstName) || empty($lastName)) {
            $this->showUploadForm(['error' => 'Brak danych użytkownika w sesji.']);
            return;
        }

        if (!isset($_FILES['uploaded_file'])) {
            $this->showUploadForm(['error' => 'Brak przesłanego pliku.']);
            return;
        }

        $file = $_FILES['uploaded_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->showUploadForm(['error' => $this->fileUploadErrorMessage($file['error'])]);
            return;
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            $this->showUploadForm(['error' => 'Plik przekracza dozwolony rozmiar 10 MB.']);
            return;
        }

        if (!is_dir($this->targetDir)) {
            if (!mkdir($this->targetDir, 0755, true)) {
                $this->showUploadForm(['error' => 'Nie udało się utworzyć katalogu docelowego.']);
                return;
            }
        }

        $this->ensureHtaccess();

        $originalName = $file['name'];
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);

        $safeFirst = $this->sanitizeForFilename($firstName);
        $safeLast  = $this->sanitizeForFilename($lastName);
        $date = date('d_m_y');

        $finalName = sprintf('%s_%s_(%s)', $safeFirst, $safeLast, $date);
        if ($ext !== '') {
            $finalName .= '.' . $ext;
        }

        $destPath = $this->targetDir . $finalName;
        $counter = 1;
        while (file_exists($destPath)) {
            $finalName = sprintf('%s_%s_(%s)_%d', $safeFirst, $safeLast, $date, $counter);
            if ($ext !== '') $finalName .= '.' . $ext;
            $destPath = $this->targetDir . $finalName;
            $counter++;
        }

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->showUploadForm(['error' => 'Nie udało się zapisać pliku na serwerze.']);
            return;
        }

        @chmod($destPath, 0644);

        // zamiast przekierowania — wyświetlenie formularza z komunikatem o sukcesie
        $this->showUploadForm(['success' => 'Plik został pomyślnie przesłany.']);
    }

    private function checkUserAuthentication()
    {
        return isset($_SESSION['user_id']);
    }

    private function sanitizeForFilename($str)
    {
        if (function_exists('transliterator_transliterate')) {
            $str = transliterator_transliterate('Any-Latin; Latin-ASCII', $str);
        }
        $str = preg_replace('/[^A-Za-z0-9_-]+/', '_', $str);
        $str = preg_replace('/_+/', '_', $str);
        $str = trim($str, '_-');
        return $str === '' ? 'user' : $str;
    }

    private function ensureHtaccess()
    {
        $htaccessPath = $this->targetDir . '.htaccess';
        $content = <<<HT
# Zabezpieczenie katalogu uploads - zabronić bezpośredniego dostępu
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>

# Dodatkowo wymusić traktowanie plików jako binarne (nie wykonywać .php)
<IfModule mod_mime.c>
    RemoveHandler .php .phtml .php3 .php4 .php5
    AddType application/octet-stream .php .phtml .php3 .php4 .php5
</IfModule>

HT;
        if (!file_exists($htaccessPath)) {
            @file_put_contents($htaccessPath, $content);
            @chmod($htaccessPath, 0644);
        }
    }

    private function fileUploadErrorMessage($errorCode)
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'Plik jest większy niż limit określony w php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'Plik jest większy niż dozwolony rozmiar przesłany w formularzu.',
            UPLOAD_ERR_PARTIAL    => 'Plik został tylko częściowo przesłany.',
            UPLOAD_ERR_NO_FILE    => 'Nie wybrano pliku.',
            UPLOAD_ERR_NO_TMP_DIR => 'Brak katalogu tymczasowego.',
            UPLOAD_ERR_CANT_WRITE => 'Błąd zapisu pliku na dysk.',
            UPLOAD_ERR_EXTENSION  => 'Przesyłanie pliku zatrzymane przez rozszerzenie PHP.'
        ];
        return $messages[$errorCode] ?? 'Nieznany błąd przesyłania pliku.';
    }
}

