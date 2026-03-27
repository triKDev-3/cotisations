<?php

// Test temporaire d'environnement
echo "<h1>Diagnostic d'environnement Vercel</h1>";
echo "📦 <b>PHP Version:</b> " . phpversion() . "<br>";
echo "🐘 <b>PDO PostgreSQL:</b> " . (extension_loaded('pdo_pgsql') ? '✅ Disponible' : '❌ Non installé') . "<br>";

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    echo "📄 <b>Autoload:</b> ✅ Présent (" . realpath($autoload) . ")<br>";
} else {
    echo "📄 <b>Autoload:</b> ❌ Manquant ! (Vérifiez votre build Vercel)<br>";
}

// Liste des extensions importantes
$required = ['bcmath', 'ctype', 'fileinfo', 'hash', 'mbstring', 'openssl', 'pcre', 'pdo', 'session', 'tokenizer', 'xml'];
echo "<h2>Extensions requises pour Laravel:</h2><ul>";
foreach ($required as $ext) {
    echo "<li>$ext: " . (extension_loaded($ext) ? '✅' : '❌') . "</li>";
}
echo "</ul>";

echo "<hr><h3>Variables d'environnement (Filtrées):</h3>";
echo "APP_ENV: " . getenv('APP_ENV') . "<br>";
echo "DB_CONNECTION: " . getenv('DB_CONNECTION') . "<br>";
echo "DB_HOST: " . getenv('DB_HOST') . "<br>";

die("<br><b>Fin du diagnostic.</b>");
