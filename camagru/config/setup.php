<?php
require_once 'database.php';

try {
    $PDO = new PDO($DB_DSN, $DB_USER , $DB_PASSWORD, array());
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // The subject requires this mode of error handling
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// mail and username are indexed, since they have the unqiue constraint.
// They are stored in B-trees for fast lookup.
$query_users = <<<EOT
    CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mail VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    notification BOOLEAN DEFAULT true,
    keycheck CHAR(16) NOT NULL,
    confirmed BOOLEAN DEFAULT false,
    creation_date DATETIME DEFAULT CURRENT_TIMESTAMP(),
    last_seen DATETIME DEFAULT CURRENT_TIMESTAMP());
EOT;

$query_images = <<<EOT
    CREATE TABLE IF NOT EXISTS images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        path VARCHAR(1000) NOT NULL,
        likes INT,
        comments INT,
        creation_date DATETIME NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    );
EOT;

$query_comments = <<<EOT
    CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_id INT NOT NULL,
        owner_id INT NOT NULL,
        content VARCHAR(150) NOT NULL,
        creation_date DATETIME NOT NULL,
        FOREIGN KEY (image_id) REFERENCES images(id),
        FOREIGN KEY (owner_id) REFERENCES users(id)
    );
EOT;

$query_calques = <<<EOT
    CREATE TABLE IF NOT EXISTS calques (
        id INT AUTO_INCREMENT PRIMARY KEY,
        path VARCHAR(1000) NOT NULL
    );
EOT;

$query_calques_seed = <<<EOT
INSERT INTO calques (path) VALUES ('/common/calques/angry.png');
INSERT INTO calques (path) VALUES ('/common/calques/happy.png');
INSERT INTO calques (path) VALUES ('/common/calques/perfect.png');
INSERT INTO calques (path) VALUES ('/common/calques/spacex.png');
INSERT INTO calques (path) VALUES ('/common/calques/thug.png');
INSERT INTO calques (path) VALUES ('/common/calques/whaat.png');
EOT;

$query_likes = <<<EOT
    CREATE TABLE IF NOT EXISTS likes (
        id INT PRIMARY KEY AUTO_INCREMENT,
        liker_id INT NOT NULL,
        image_id INT NOT NULL,
        creation_date DATETIME NOT NULL,
        FOREIGN KEY (liker_id) REFERENCES users(id),
        FOREIGN KEY (image_id) REFERENCES images(id)
    );
EOT;

try {
    print($query_users . "\n");
    $PDO->exec($query_users);

    print($query_images . "\n");
    $PDO->exec($query_images);

    print($query_comments . "\n");
    $PDO->exec($query_comments);

    print($query_calques . "\n");
    $PDO->exec($query_calques);

    print($query_likes . "\n");
    $PDO->exec($query_likes);

    print($query_calques_seed . "\n");
    $PDO->exec($query_calques_seed);

} catch (PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
